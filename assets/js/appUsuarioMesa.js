let mesaId = null;
let pedido = { productos: [], total: 0 };
let tokenMesa = null;
let expiracionToken = null;
let intervaloExpira = null;
let refreshPedidosActivosInterval = null;

function iniciarAutoRefreshPedidosActivosMesa() {
    if (refreshPedidosActivosInterval) clearInterval(refreshPedidosActivosInterval);
    refreshPedidosActivosInterval = setInterval(cargarPedidosActivosMesa, 10000); // cada 10 segundos
}

function detenerAutoRefreshPedidosActivosMesa() {
    if (refreshPedidosActivosInterval) clearInterval(refreshPedidosActivosInterval);
    refreshPedidosActivosInterval = null;
}

function validarToken() {
    const token = document.getElementById('tokenInput').value.trim();
    if (!token) {
        Swal.fire('Error', 'Por favor ingrese el token', 'error');
        document.getElementById('pedidoSection').style.display = 'none';
        return;
    }

    fetch('../controllers/validar_token.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'token=' + encodeURIComponent(token)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mesaId = data.mesa_id;
            tokenMesa = token;
            expiracionToken = data.expiracion_timestamp;
            document.getElementById('tokenSection').style.display = 'none';
            document.getElementById('pedidoSection').style.display = 'block';
            cargarPedidosActivos();
            cargarCategorias();
            cargarTodosLosProductosDelUsuario();
            cargarPedidosActivosMesa();
            iniciarAutoRefreshPedidosActivosMesa(); // Inicia el refresco automático
            mostrarTiempoExpiracion();
            intervaloExpira = setInterval(mostrarTiempoExpiracion, 1000);
        } else {
            document.getElementById('pedidoSection').style.display = 'none';
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        document.getElementById('pedidoSection').style.display = 'none';
        console.error('Error:', error);
        Swal.fire('Error', 'Error al validar el token', 'error');
    });
}

function cargarPedidosActivos() {
    if (!mesaId) return;
    
    fetch('../controllers/pedidos_activos.php')
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const pedidosMesa = data.pedidos.filter(p => p.mesa_id === mesaId);
            if (pedidosMesa.length > 0) {
                const pedido = pedidosMesa[0]; // Tomamos el pedido más reciente
                pedido.productos.forEach(prod => {
                    agregarAlPedido({
                        id: prod.id,
                        nombre: prod.nombre,
                        precio: prod.precio,
                        cantidad: prod.cantidad,
                        comentario: prod.comentario
                    });
                });
            }
        }
    })
    .catch(error => {
        console.error('Error al cargar pedidos activos:', error);
    });
}

function mostrarTiempoExpiracion() {
    const ahora = new Date().getTime();
    const tiempoRestante = expiracionToken - ahora;
    const div = document.getElementById('expiracionTokenInfo');
    if (!div) return; // Evita error si el elemento no existe

    if (tiempoRestante <= 0) {
        clearInterval(intervaloExpira);
        div.innerHTML = '<span class="text-danger">Token expirado</span>';
        document.getElementById('productosContainer').style.display = 'none';
        document.getElementById('categoriaSelect').style.display = 'none';
        document.getElementById('btnConfirmarPedido').disabled = true;
        return;
    }
    const minutos = Math.floor(tiempoRestante / (1000 * 60));
    const segundos = Math.floor((tiempoRestante % (1000 * 60)) / 1000);
    div.textContent = `Te Quedan: ${minutos}:${segundos.toString().padStart(2, '0')} minutos para confirmar tu pedido`;
}

function cargarCategorias() {
    fetch('../controllers/cargar_categorias.php')
    .then(res => res.json())
    .then(data => {
        const select = document.getElementById('categoriaSelect');
        select.innerHTML = '<option value="">Seleccione una categoría</option>';
        if (data.success && data.categorias) {
            data.categorias.forEach(cat => {
                select.innerHTML += `<option value="${cat.idcategorias}">${cat.nombre_categoria}</option>`;
            });
        }
    });
}

function cargarProductos(idcategoria) {
    console.log('cargarProductos llamado con idcategoria:', idcategoria);
    const contenedor = document.getElementById('productosContainer');
    if (!idcategoria) {
        contenedor.innerHTML = '';
        return;
    }
    contenedor.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';
    
    const body = 'idcategorias=' + encodeURIComponent(idcategoria);
    console.log('Enviando body:', body);
    
    fetch('../controllers/cargar_productos.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(res => {
        console.log('Response status:', res.status);
        return res.json();
    })
    .then(data => {
        console.log('Response data:', data);
        contenedor.innerHTML = data.html;
        // Agregar event listeners a los botones de agregar
        document.querySelectorAll('#productosContainer .btn-primary').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.card');
                const id = card.getAttribute('data-id');
                const nombre = card.querySelector('h5').textContent.trim();
                const precio = parseFloat(this.getAttribute('data-precio'));
                const input = card.querySelector('input[type=number]');
                const cantidad = parseInt(input.value);

                if (!cantidad || cantidad <= 0) {
                    Swal.fire('Cantidad inválida', 'Ingrese una cantidad válida', 'warning');
                    return;
                }

                // Actualizar el modal con los datos del producto
                document.getElementById('productoNombreSeleccionado').textContent = nombre;
                document.getElementById('productoCantidadSeleccionada').textContent = cantidad;
                document.getElementById('productoPrecioSeleccionado').textContent = `$${precio.toFixed(2)}`;
                document.getElementById('productoId').value = id;
                document.getElementById('observaciones').value = '';

                // Mostrar el modal
                const modalElement = document.getElementById('modalObservaciones');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            });
        });
    })
    .catch(error => {
        console.error('Error en cargarProductos:', error);
        contenedor.innerHTML = '<div class="alert alert-danger">Error al cargar los productos</div>';
    });
}

function agregarProductoAlPedido() {
    const id = document.getElementById('productoId').value;
    const nombre = document.getElementById('productoNombreSeleccionado').textContent;
    const cantidad = parseInt(document.getElementById('productoCantidadSeleccionada').textContent);
    const precio = parseFloat(document.getElementById('productoPrecioSeleccionado').textContent.replace('$', '').trim());
    const comentario = document.getElementById('observaciones').value;

    if (isNaN(precio) || isNaN(cantidad)) {
        Swal.fire('Error', 'Error en los valores del producto', 'error');
        return;
    }

    agregarAlPedido({
        id: id,
        nombre: nombre,
        cantidad: cantidad,
        precio: precio,
        comentario: comentario
    });

    // Cerrar el modal
    const modalElement = document.getElementById('modalObservaciones');
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
}

function agregarAlPedido(producto) {
    // Si el producto ya está, suma cantidad y comentario
    const idx = pedido.productos.findIndex(p => p.id == producto.id && p.comentario == producto.comentario);
    if (idx >= 0) {
        pedido.productos[idx].cantidad += producto.cantidad;
    } else {
        pedido.productos.push(producto);
    }
    renderPedido();
}

function renderPedido() {
    const tbody = document.getElementById('productosPedido');
    tbody.innerHTML = '';
    let total = 0;
    pedido.productos.forEach(producto => {
        const subtotal = producto.precio * producto.cantidad;
        total += subtotal;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td data-id="${producto.id}">${producto.nombre}</td>
            <td>${producto.cantidad}</td>
            <td>$${producto.precio.toFixed(2)}</td>
            <td>${producto.comentario || ''}</td>
            <td>$${subtotal.toFixed(2)}</td>
            <td><button class="btn btn-sm btn-danger" onclick="eliminarProducto(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
    });
    pedido.total = total;
    document.getElementById('totalPedido').textContent = '$' + total.toFixed(2);
}

function eliminarProducto(button) {
    const tr = button.closest('tr');
    const id = tr.querySelector('td[data-id]').getAttribute('data-id');
    const comentario = tr.children[3].textContent;
    pedido.productos = pedido.productos.filter(p => !(p.id == id && p.comentario == comentario));
    renderPedido();
}

function regresarASeleccionarProductos() {
    document.getElementById('pedidoSection').style.display = 'block';
    document.getElementById('expiracionTokenInfo').style.display = 'block';
    document.getElementById('pedidoActual').innerHTML = '';
    document.getElementById('productosPedido').innerHTML = '';
    document.getElementById('totalPedido').textContent = '$0.00';
    pedido = { productos: [], total: 0 };

    // Reiniciar el contador si no está corriendo
    if (!intervaloExpira) {
        mostrarTiempoExpiracion();
        intervaloExpira = setInterval(mostrarTiempoExpiracion, 1000);
    }

    cargarTodosLosProductosDelUsuario();

    Swal.fire({
        title: '¡Listo!',
        text: 'Puede agregar más productos a su pedido',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
}

function confirmarPedido() {
    if (!pedido.productos.length) {
        Swal.fire('Error', 'No hay productos en el pedido', 'error');
        return;
    }
    Swal.fire({
        title: '¿Confirmar pedido?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('../controllers/confirmar_pedido.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mesa_id: mesaId, productos: pedido.productos, token: tokenMesa })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ocultar completamente la sección de pedido
                    document.getElementById('pedidoSection').style.display = 'none';
                    
                    // Ocultar completamente el contenedor de token (columna izquierda)
                    const tokenContainer = document.getElementById('tokenPanel');
                    if (tokenContainer) {
                        tokenContainer.style.display = 'none';
                    }
                    
                    // Centrar el panel de pedidos
                    const pedidoPanel = document.getElementById('pedidoPanel');
                    if (pedidoPanel) {
                        pedidoPanel.className = 'col-lg-12';
                        pedidoPanel.style.margin = '0 auto';
                        pedidoPanel.style.maxWidth = '800px';
                    }
                    
                    // Ocultar el tiempo restante del token
                    document.getElementById('expiracionTokenInfo').style.display = 'none';
                    
                    // Cargar todos los productos del usuario con el mismo token para mostrar el resumen completo
                    cargarResumenCompletoDelUsuario();
                    
                    // Limpiar el pedido actual
                    pedido = { productos: [], total: 0 };
                    clearInterval(intervaloExpira);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Error al confirmar el pedido', 'error');
            });
        }
    });
}

function cargarResumenCompletoDelUsuario() {
    detenerAutoRefreshPedidosActivosMesa();
    if (!mesaId || !tokenMesa) {
        console.log('No hay mesaId o tokenMesa:', { mesaId, tokenMesa });
        mostrarResumenPedidoActual();
        return;
    }
    
    console.log('Cargando resumen completo para mesa:', mesaId, 'token:', tokenMesa);
    
    fetch('../controllers/pedidos_usuario_token.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ mesa_id: mesaId, token: tokenMesa })
    })
    .then(res => res.json())
    .then(data => {
        console.log('Respuesta completa de pedidos_usuario_token.php:', data);
        
        if (data.success) {
            const pedidos = data.pedidos;
            console.log('Pedidos encontrados:', pedidos);
            
            if (pedidos.length > 0) {
                // Calcular total general de todos los pedidos
                let totalGeneral = 0;
                let todosLosProductos = [];
                
                pedidos.forEach((pedido, pedidoIndex) => {
                    console.log(`Procesando pedido ${pedidoIndex + 1}:`, pedido);
                    console.log('Productos del pedido:', pedido.productos);
                    
                    pedido.productos.forEach((prod, prodIndex) => {
                        console.log(`Producto ${prodIndex + 1}:`, prod);
                        console.log('Tipo de precio:', typeof prod.precio, 'Valor:', prod.precio);
                        console.log('Tipo de cantidad:', typeof prod.cantidad, 'Valor:', prod.cantidad);
                        
                        // Asegurar que precio y cantidad sean números
                        const precio = parseFloat(prod.precio) || 0;
                        const cantidad = parseInt(prod.cantidad) || 0;
                        
                        console.log('Precio convertido:', precio, 'Cantidad convertida:', cantidad);
                        
                        todosLosProductos.push({
                            ...prod,
                            precio: precio,
                            cantidad: cantidad
                        });
                        totalGeneral += precio * cantidad;
                    });
                });
                
                console.log('Total de productos encontrados:', todosLosProductos.length);
                console.log('Total general:', totalGeneral);
                console.log('Productos procesados:', todosLosProductos);
                
                const resumenHTML = `
                    <div class="alert alert-success text-center mb-4">
                        <h4 class="alert-heading">
                            <i class="fas fa-check-circle me-2"></i>¡Pedido Confirmado!
                        </h4>
                        <p class="mb-0">Su pedido está siendo procesado. Gracias por visitarnos.</p>
                    </div>
                    <div class="card shadow-lg border-0 rounded-4 bg-light mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-receipt me-2"></i>Resumen Completo de Todos sus Pedidos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-success">
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Observaciones</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${todosLosProductos.map(p => `
                                            <tr>
                                                <td>${p.nombre}</td>
                                                <td>${p.cantidad}</td>
                                                <td>$${p.precio.toFixed(2)}</td>
                                                <td>${p.comentario || '-'}</td>
                                                <td>$${(p.cantidad * p.precio).toFixed(2)}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                    <tfoot class="table-success">
                                        <tr>
                                            <th colspan="4" class="text-end">Total General:</th>
                                            <th>$${totalGeneral.toFixed(2)}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('pedidoActual').innerHTML = resumenHTML;

                // Reemplazar el historial para evitar volver atrás
                window.history.replaceState({pedidoConfirmado: true}, '', window.location.href);
            } else {
                console.log('No se encontraron pedidos para el token, mostrando resumen del pedido actual');
                mostrarResumenPedidoActual();
            }
        } else {
            console.log('Error en la respuesta:', data.message);
            mostrarResumenPedidoActual();
        }
    })
    .catch(error => {
        console.error('Error al cargar el resumen completo:', error);
        // Si hay error, mostrar el resumen del pedido actual
        mostrarResumenPedidoActual();
    });
}

function mostrarResumenPedidoActual() {
    const resumenHTML = `
        <div class="alert alert-success text-center mb-4">
            <h4 class="alert-heading">
                <i class="fas fa-check-circle me-2"></i>¡Pedido Confirmado!
            </h4>
            <p class="mb-0">Su pedido está siendo procesado. Gracias por visitarnos.</p>
        </div>
        <div class="card shadow-lg border-0 rounded-4 bg-light mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-receipt me-2"></i>Resumen de su Pedido
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-success">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Observaciones</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${pedido.productos.map(p => `
                                <tr>
                                    <td>${p.nombre}</td>
                                    <td>${p.cantidad}</td>
                                    <td>$${p.precio.toFixed(2)}</td>
                                    <td>${p.comentario || '-'}</td>
                                    <td>$${(p.cantidad * p.precio).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot class="table-success">
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th>$${pedido.total.toFixed(2)}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    `;
    document.getElementById('pedidoActual').innerHTML = resumenHTML;
}

function cargarTodosLosProductosDelUsuario() {
    if (!mesaId || !tokenMesa) return;

    // Limpiar el array de productos antes de agregar los nuevos
    pedido.productos = [];
    pedido.total = 0;

    fetch('../controllers/pedidos_usuario_token.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ mesa_id: mesaId, token: tokenMesa })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const pedidos = data.pedidos;
            if (pedidos.length > 0) {
                mostrarHistorialPedidos(pedidos);
                pedidos.forEach(pedido => {
                    pedido.productos.forEach(prod => {
                        const precio = parseFloat(prod.precio) || 0;
                        const cantidad = parseInt(prod.cantidad) || 0;
                        agregarAlPedido({
                            id: prod.id,
                            nombre: prod.nombre,
                            precio: precio,
                            cantidad: cantidad,
                            comentario: prod.comentario
                        });
                    });
                });
            } else {
                document.getElementById('historialPedidos').style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error al cargar todos los productos del usuario:', error);
    });
}

function mostrarHistorialPedidos(pedidos) {
    const historialDiv = document.getElementById('historialPedidos');
    const contenidoHistorial = document.getElementById('contenidoHistorial');
    
    if (pedidos.length === 0) {
        historialDiv.style.display = 'none';
        return;
    }
    
    let html = '';
    pedidos.forEach((pedido, index) => {
        const totalPedido = pedido.productos.reduce((sum, prod) => {
            const precio = parseFloat(prod.precio) || 0;
            const cantidad = parseInt(prod.cantidad) || 0;
            return sum + (precio * cantidad);
        }, 0);
        
        html += `
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Pedido #${index + 1} - Total: $${totalPedido.toFixed(2)}
                        <small class="text-muted ms-2">(${new Date(pedido.fecha_hora).toLocaleTimeString()})</small>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Observaciones</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${pedido.productos.map(prod => {
                                    const precio = parseFloat(prod.precio) || 0;
                                    const cantidad = parseInt(prod.cantidad) || 0;
                                    return `
                                        <tr>
                                            <td>${prod.nombre}</td>
                                            <td>${cantidad}</td>
                                            <td>$${precio.toFixed(2)}</td>
                                            <td>${prod.comentario || '-'}</td>
                                            <td>$${(cantidad * precio).toFixed(2)}</td>
                                        </tr>
                                    `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    });
    
    contenidoHistorial.innerHTML = html;
    historialDiv.style.display = 'block';
}

function cargarPedidosActivosMesa() {
    if (!mesaId) return;

    fetch('../controllers/pedidos_activos_mesa.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ mesa_id: mesaId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarHistorialPedidos(data.pedidos);
        }
    })
    .catch(error => {
        console.error('Error al cargar pedidos activos de la mesa:', error);
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Agregar event listener al select de categorías
    document.getElementById('categoriaSelect').addEventListener('change', function() {
        cargarProductos(this.value);
    });
    
    // Hacer las funciones globales para que puedan ser llamadas desde el HTML
    window.cargarTodosLosProductosDelUsuario = cargarTodosLosProductosDelUsuario;
    window.mostrarHistorialPedidos = mostrarHistorialPedidos;
    window.cargarResumenCompletoDelUsuario = cargarResumenCompletoDelUsuario;
    window.mostrarResumenPedidoActual = mostrarResumenPedidoActual;
});

// Bloquear el botón atrás si el pedido fue confirmado
window.addEventListener('popstate', function(event) {
    if (event.state && event.state.pedidoConfirmado) {
        if (document.getElementById('pedidoSection')) {
            document.getElementById('pedidoSection').style.display = 'none';
        }
        if (document.getElementById('expiracionTokenInfo')) {
            document.getElementById('expiracionTokenInfo').style.display = 'none';
        }
        // Opcional: mostrar un mensaje
        Swal.fire({
            icon: 'info',
            title: 'Acción no permitida',
            text: 'No puede regresar a la pantalla de pedido después de confirmar.',
            timer: 2500,
            showConfirmButton: false
        });
    }
}); 