let pedido = [];

// funcion para cargar productos al seleccionar una categoria de productos
document.addEventListener("DOMContentLoaded", function () {
  const select = document.querySelector("#categoriaSelect");
  const contenedor = document.querySelector("#productosContainer");
  const mesaSelect = document.querySelector("#mesaSelect");

  // Validar que se haya seleccionado una mesa antes de cargar productos
  select.addEventListener("change", function () {
    if (!mesaSelect.value) {
      Swal.fire({
        icon: 'warning',
        title: 'Seleccione una mesa',
        text: 'Por favor, seleccione una mesa antes de ver los productos.',
      });
      select.value = '';
      return;
    }

    const idcategorias = select.value;
    if (!idcategorias) {
      contenedor.innerHTML = "";
      return;
    }

    // Mostrar loading
    contenedor.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

    fetch("../controllers/cargar_productos.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "idcategorias=" + encodeURIComponent(idcategorias),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          contenedor.innerHTML = data.html;
          // Limitar el input de cantidad según el stock
          document.querySelectorAll('#productosContainer .card').forEach(card => {
            const stockBadge = card.querySelector('.badge.bg-secondary');
            let stock = null;
            if (stockBadge) {
              const match = stockBadge.textContent.match(/Stock:\s*(\d+)/);
              if (match) stock = parseInt(match[1]);
            }
            const input = card.querySelector('input[type=number]');
            if (input && stock !== null) {
              input.max = stock;
            }
          });
          // Agregar event listeners a los botones de agregar
          document.querySelectorAll('#productosContainer .btn-primary').forEach(btn => {
            btn.addEventListener('click', function() {
              const card = this.closest('.card');
              const id = card.getAttribute('data-id');
              const nombre = card.querySelector('h5').textContent.trim();
              const precio = parseFloat(this.getAttribute('data-precio'));
              const input = card.querySelector('input[type=number]');
              const cantidad = parseInt(input.value);
              // Validar stock
              const stockBadge = card.querySelector('.badge.bg-secondary');
              let stock = null;
              if (stockBadge) {
                const match = stockBadge.textContent.match(/Stock:\s*(\d+)/);
                if (match) stock = parseInt(match[1]);
              }
              if (stock !== null && cantidad > stock) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Cantidad inválida',
                  text: 'No puedes agregar más que el stock disponible.',
                  confirmButtonText: 'Entendido'
                });
                return;
              }
              if (!cantidad || cantidad <= 0) {
                Swal.fire({
                  icon: 'warning',
                  title: 'Cantidad inválida',
                  text: 'Por favor, ingrese una cantidad válida.',
                  confirmButtonText: 'Entendido'
                });
                return;
              }

              // Actualizar el modal con los datos del producto
              document.getElementById("productoSeleccionado").value = id;
              document.getElementById("productoNombreSeleccionado").textContent = nombre;
              document.getElementById("productoCantidadSeleccionada").textContent = cantidad;
              document.getElementById("productoPrecioSeleccionado").textContent = `$${precio.toFixed(2)}`;
              document.getElementById("comentarioInput").value = "";
              document.getElementById("productoSeleccionado").setAttribute("data-precio", precio);
              document.getElementById("productoSeleccionado").setAttribute("data-nombre", nombre);
              document.getElementById("productoSeleccionado").setAttribute("data-cantidad", cantidad);

              // Mostrar el modal
              const modalElement = document.getElementById("observacionModal");
              const modal = new bootstrap.Modal(modalElement);
              modal.show();
            });
          });
        } else {
          contenedor.innerHTML = data.html;
          console.error('Error:', data.message);
        }
      })
      .catch((error) => {
        console.error('Error:', error);
        contenedor.innerHTML = `
          <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            Error al cargar productos. Por favor, intente nuevamente.
          </div>`;
      });
  });

  // Validar selección de mesa
  mesaSelect.addEventListener("change", function() {
    if (select.value) {
      select.dispatchEvent(new Event('change'));
    }
    // Cargar pedido activo de la mesa
    const mesaId = mesaSelect.value;
    if (mesaId) {
      fetch("../controllers/pedidos_activos_mesa.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ mesa_id: mesaId })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.pedidos && data.pedidos.length > 0) {
          // Tomar el primer pedido activo
          const productos = data.pedidos[0].productos;
          pedido = productos.map(prod => ({
            id: prod.id,
            nombre: prod.nombre,
            cantidad: parseInt(prod.cantidad),
            comentario: prod.comentario,
            precio: parseFloat(prod.precio)
          }));
          actualizarLista();
        } else {
          pedido = [];
          actualizarLista();
        }
      });
    } else {
      pedido = [];
      actualizarLista();
    }
  });
});

// funcion para buscar productos desde el input buscador
document.addEventListener("DOMContentLoaded", function () {
  const buscador = document.querySelector("#buscadorProductos");

  buscador.addEventListener("input", function () {
    const filtro = buscador.value.toLowerCase();
    const productos = document.querySelectorAll("#productosContainer .card");

    productos.forEach(card => {
      const nombre = card.querySelector("h5").textContent.toLowerCase();
      card.parentElement.style.display = nombre.includes(filtro) ? "" : "none";
    });
  });
});

function abrirModal(button, id_producto, nombre_producto) {
  const card = button.closest(".card");
  const inputCantidad = card.querySelector("input[type='number']");
  const cantidad = parseInt(inputCantidad.value);
  const precio = parseFloat(button.getAttribute("data-precio"));

  if (!cantidad || cantidad <= 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Cantidad inválida',
      text: 'Por favor, ingrese una cantidad válida.',
      confirmButtonText: 'Entendido'
    });
    return;
  }

  // Actualizar el modal con los datos del producto
  document.getElementById("productoSeleccionado").value = id_producto;
  document.getElementById("productoNombreSeleccionado").textContent = nombre_producto;
  document.getElementById("productoCantidadSeleccionada").textContent = cantidad;
  document.getElementById("productoPrecioSeleccionado").textContent = `$${precio.toFixed(2)}`;
  document.getElementById("comentarioInput").value = "";
  document.getElementById("productoSeleccionado").setAttribute("data-precio", precio);
  document.getElementById("productoSeleccionado").setAttribute("data-nombre", nombre_producto);
  document.getElementById("productoSeleccionado").setAttribute("data-cantidad", cantidad);

  // Mostrar el modal
  new bootstrap.Modal(document.getElementById("observacionModal")).show();
}

function agregarAlPedido() {
  const id = document.getElementById("productoSeleccionado").value;
  const comentario = document.getElementById("comentarioInput").value.trim();
  const precio = parseFloat(document.getElementById("productoSeleccionado").getAttribute("data-precio"));
  const nombre = document.getElementById("productoSeleccionado").getAttribute("data-nombre");
  const cantidad = parseInt(document.getElementById("productoSeleccionado").getAttribute("data-cantidad"));

  if (!cantidad || cantidad <= 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Cantidad inválida',
      text: 'Por favor, ingrese una cantidad válida.',
    });
    return;
  }

  const existente = pedido.find(p => p.id === id && p.comentario === comentario);

  if (existente) {
    existente.cantidad += cantidad;
  } else {
    pedido.push({
      id: id,
      nombre: nombre,
      cantidad: cantidad,
      comentario: comentario,
      precio: precio
    });
  }

  actualizarLista();
  bootstrap.Modal.getInstance(document.getElementById("observacionModal")).hide();
}

function actualizarLista() {
  const lista = document.getElementById("pedidoLista");
  lista.innerHTML = "";
  let total = 0;

  pedido.forEach((item, index) => {
    const subtotal = item.precio * item.cantidad;
    total += subtotal;

    lista.innerHTML += `
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
          <strong>${item.nombre}</strong>
          <br>
          <small class="text-muted">${item.comentario || "sin obs."}</small>
          <br>
          <small>$${item.precio.toFixed(2)} x ${item.cantidad}</small>
        </div>
        <div class="text-end">
          <div class="mb-2">$${subtotal.toFixed(2)}</div>
          <div>
            <button class="btn btn-sm btn-secondary" onclick="cambiarCantidad(${index}, -1)">-</button>
            <button class="btn btn-sm btn-secondary" onclick="cambiarCantidad(${index}, 1)">+</button>
            <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${index})">x</button>
          </div>
        </div>
      </li>`;
  });

  // Agregar el total al final de la lista
  lista.innerHTML += `
    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
      <strong>Total</strong>
      <strong>$${total.toFixed(2)}</strong>
    </li>`;
}

function actualizarCantidadEnBD(index, nuevaCantidad) {
  // Solo si hay pedido activo
  const mesaId = document.getElementById('mesaSelect').value;
  if (!mesaId || !window.pedidosActivosGlobal || !window.pedidosActivosGlobal[mesaId]) return;
  const pedidoActivo = window.pedidosActivosGlobal[mesaId];
  const prod = pedido[index];
  fetch('../controllers/actualizar_detalle_pedido.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      pedido_id: pedidoActivo.pedido_id,
      producto_id: prod.id,
      comentario: prod.comentario,
      cantidad: nuevaCantidad
    })
  })
  .then(res => res.json())
  .then(data => {
    if (!data.success) {
      Swal.fire('Error', data.message || 'No se pudo actualizar el pedido', 'error');
    } else {
      // Actualizar el stock en la card si está visible
      const card = document.querySelector(`#productosContainer .card[data-id='${prod.id}']`);
      if (card) {
        const stockBadge = card.querySelector('.badge.bg-secondary');
        if (stockBadge) {
          // Restar la cantidad agregada/eliminada
          let stock = parseInt(stockBadge.textContent.replace(/\D/g, ''));
          let diff = nuevaCantidad - (prod.cantidad || 0);
          stockBadge.textContent = 'Stock: ' + (stock - diff);
        }
      }
    }
  });
}

function cambiarCantidad(index, delta) {
  pedido[index].cantidad += delta;
  if (pedido[index].cantidad <= 0) {
    eliminarProducto(index);
    return;
  }
  actualizarCantidadEnBD(index, pedido[index].cantidad);
  actualizarLista();
}

function eliminarProducto(index) {
  actualizarCantidadEnBD(index, 0);
  pedido.splice(index, 1);
  actualizarLista();
}

function confirmarPedido() {
  const mesa = document.getElementById("mesaSelect").value;
  const mesaSelect = document.getElementById("mesaSelect");
  const mesaNombre = mesaSelect.options[mesaSelect.selectedIndex].text;

  if (!mesa) {
    Swal.fire({
      icon: 'warning',
      title: 'Mesa no seleccionada',
      text: 'Por favor, seleccione una mesa antes de confirmar el pedido.',
    });
    return;
  }

  if (pedido.length === 0) {
    Swal.fire({
      icon: 'info',
      title: 'Sin productos',
      text: 'Agrega al menos un producto al pedido.',
    });
    return;
  }

  // Calcular el total del pedido
  const total = pedido.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);

  fetch("../controllers/confirmar_pedido.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      mesa_id: parseInt(mesa),
      productos: pedido,
      total: total
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Pedido registrado',
          text: data.message,
        });
        pedido = [];
        actualizarLista();
        // Limpiar los inputs seleccion de mesa y categoria
        document.getElementById("mesaSelect").value = "";
        document.getElementById("categoriaSelect").value = "";
        document.getElementById("productosContainer").innerHTML = "";
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message,
        });
      }
    })
    .catch((error) => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo guardar el pedido.',
      });
      console.error(error);
    });
}

function generarTokenMesa() {
  const mesaId = document.getElementById('mesaSelect').value;
  if (!mesaId) {
    if (typeof Swal === 'undefined') {
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
      script.onload = function() {
        Swal.fire('Seleccione una mesa', 'Debe seleccionar una mesa para generar el token', 'warning');
      };
      document.body.appendChild(script);
    } else {
      Swal.fire('Seleccione una mesa', 'Debe seleccionar una mesa para generar el token', 'warning');
    }
    return;
  }
  fetch('../controllers/generar_token.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'mesa_id=' + mesaId
  })
  .then(res => res.json())
  .then(data => {
    function showTokenSwal() {
      if (data.success) {
        Swal.fire({
          title: 'Token generado',
          html: 'El token para la mesa es: <b>' + data.token + '</b><br>Expira a las: <b>' + (new Date(data.expira).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})) + '</b>',
          icon: 'success'
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire('Error', data.message, 'error');
      }
    }
    if (typeof Swal === 'undefined') {
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
      script.onload = showTokenSwal;
      document.body.appendChild(script);
    } else {
      showTokenSwal();
    }
  })
  .catch(error => {
    console.error('Error al generar token:', error);
    function showErrorSwal() {
      Swal.fire('Error', 'No se pudo generar el token.', 'error');
    }
    if (typeof Swal === 'undefined') {
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
      script.onload = showErrorSwal;
      document.body.appendChild(script);
    } else {
      showErrorSwal();
    }
  });
}

// Exportar funciones globales para el HTML
document.addEventListener("DOMContentLoaded", function () {
  window.confirmarPedido = confirmarPedido;
  window.generarTokenMesa = generarTokenMesa;
  
  // Cargar todos los pedidos activos de todas las mesas al iniciar
  function cargarPedidosActivosGlobal() {
    fetch("../controllers/pedidos_activos.php")
      .then(res => res.json())
      .then(data => {
        const cont = document.getElementById("pedidosActivosMesa");
        if (!cont) return;
        window.pedidosActivosGlobal = {};
        if (data.success && data.pedidos && data.pedidos.length > 0) {
          let html = '<div class="accordion" id="accordionPedidosActivos">';
          data.pedidos.forEach((pedido, idx) => {
            window.pedidosActivosGlobal[pedido.mesa_id] = pedido;
            html += `
              <div class="accordion-item">
                <h2 class="accordion-header" id="heading${pedido.pedido_id}">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${pedido.pedido_id}" aria-expanded="false" aria-controls="collapse${pedido.pedido_id}">
                    <strong>${pedido.mesa_nombre || pedido.mesa_id}</strong> 
                  </button>
                </h2>
                <div id="collapse${pedido.pedido_id}" class="accordion-collapse collapse" aria-labelledby="heading${pedido.pedido_id}" data-bs-parent="#accordionPedidosActivos">
                  <div class="accordion-body">
                    <div><strong>Pedido #:</strong> ${pedido.pedido_id}</div>
                    <div><strong>Productos:</strong><ul class='mb-1'>`;
            pedido.productos.forEach(prod => {
              html += `<li>${prod.nombre} x${prod.cantidad} ($${parseFloat(prod.precio).toFixed(2)})</li>`;
            });
            html += `</ul></div>
                    <div><strong>Total:</strong> $${pedido.productos.reduce((sum, p) => sum + (parseFloat(p.precio) * parseInt(p.cantidad)), 0).toFixed(2)}</div>
                    <button class='btn btn-warning btn-sm mt-2' onclick='modificarPedidoActivo(${pedido.pedido_id}, ${pedido.mesa_id})'>Modificar pedido</button>
                  </div>
                </div>
              </div>
            `;
          });
          html += '</div>';
          cont.innerHTML = html;
        } else {
          cont.innerHTML = '<div class="text-muted">No hay pedidos activos.</div>';
        }
      });
  }
  cargarPedidosActivosGlobal();
  setInterval(cargarPedidosActivosGlobal, 10000);
});

window.modificarPedidoActivo = function(pedidoId, mesaId) {
  // Buscar el pedido en la variable global
  const pedidoObj = window.pedidosActivosGlobal[mesaId];
  if (!pedidoObj || pedidoObj.pedido_id != pedidoId) return;
  // Cargar productos en el carrito
  pedido = pedidoObj.productos.map(prod => ({
    id: prod.id,
    nombre: prod.nombre,
    cantidad: parseInt(prod.cantidad),
    comentario: prod.comentario,
    precio: parseFloat(prod.precio)
  }));
  actualizarLista();
  // Resaltar el pedido que se está editando
  document.querySelectorAll('#pedidosActivosMesa .border').forEach(div => div.classList.remove('border-primary'));
  const divPedido = document.getElementById('pedidoActivo_' + pedidoId);
  if (divPedido) divPedido.classList.add('border-primary');
  // Seleccionar la mesa en el select
  const mesaSelect = document.getElementById('mesaSelect');
  if (mesaSelect) mesaSelect.value = mesaId;
}

document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', async function(e) {
      e.preventDefault();
      const correo = document.getElementById('correo').value;
      const contrasena = document.getElementById('contrasena').value;
      try {
        const response = await fetch('../controllers/login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ correo, contrasena })
        });
        const data = await response.json();
        if (data.success) {
          window.location.href = 'mesero.php';
        } else {
          Swal.fire('Error', data.message || 'Credenciales incorrectas', 'error');
        }
      } catch (err) {
        Swal.fire('Error', 'Error de conexión', 'error');
      }
    });
  }

  // Cierre de sesión con SweetAlert2
  const btnCerrarSesion = document.getElementById('btnCerrarSesion');
  if (btnCerrarSesion) {
    btnCerrarSesion.addEventListener('click', function(e) {
      e.preventDefault();
      if (typeof Swal === 'undefined') {
        // Cargar SweetAlert2 dinámicamente si no está
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        script.onload = showLogoutSwal;
        document.body.appendChild(script);
      } else {
        showLogoutSwal();
      }
      function showLogoutSwal() {
        Swal.fire({
          title: '¿Deseas cerrar tu sesión?',
          text: 'Se cerrará tu sesión y volverás al inicio de sesión.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Sí, cerrar sesión',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = '../controllers/logout.php';
          }
        });
      }
    });
  }
});