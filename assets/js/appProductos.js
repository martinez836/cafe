let estaEditando = false;
const modalProducto = new bootstrap.Modal(document.getElementById('productModal'));

// Funciones globales
function editarProducto(id) {
    estaEditando = true;
    document.getElementById('productModalLabel').textContent = 'Editar Producto';
    
    fetch(`../../controllers/admin/productos.php?action=getProducto&id=${id}`)
        .then(respuesta => respuesta.json())
        .then(datos => {
            if (datos.success) {
                const producto = datos.data;
                console.log('Producto recibido para edición:', producto);
                document.getElementById('productId').value = producto.idproductos;
                document.getElementById('productName').value = producto.nombre_producto;
                document.getElementById('productPrice').value = producto.precio_producto;
                document.getElementById('productStock').value = producto.stock_producto;
                document.getElementById('productCategory').value = producto.fk_categoria;
                document.getElementById('productEstado').value = producto.estados_idestados;
                modalProducto.show();
            }
        })
        .catch(error => console.error('Error al cargar producto:', error));
}

function eliminarProducto(id) {
    if (confirm('¿Está seguro de que desea eliminar este producto?')) {
        fetch('../../controllers/admin/productos.php?action=deleteProducto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(respuesta => respuesta.json())
        .then(datos => {
            if (datos.success) {
                alert('Producto eliminado exitosamente');
                cargarProductos();
            } else {
                alert('Error al eliminar el producto');
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const formularioProducto = document.getElementById('productForm');
    const botonAgregarProducto = document.getElementById('addProductBtn');
    const botonGuardarProducto = document.getElementById('saveProduct');

    // Cargar categorías
    function cargarCategorias() {
        fetch('../../controllers/admin/categorias.php?action=getAllCategorias')
            .then(respuesta => respuesta.json())
            .then(datos => {
                const selectorCategoria = document.getElementById('productCategory');
                selectorCategoria.innerHTML = '<option value="">Seleccione una categoría</option>';
                
                if (datos.success && datos.data.length > 0) {
                    datos.data.forEach(categoria => {
                        selectorCategoria.innerHTML += `
                            <option value="${categoria.idcategorias}">${categoria.nombre_categoria}</option>
                        `;
                    });
                }
            })
            .catch(error => console.error('Error al cargar categorías:', error));
    }

    // Cargar productos
    window.cargarProductos = function() {
        fetch('../../controllers/admin/productos.php?action=getAllProductos')
            .then(respuesta => {
                if (!respuesta.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return respuesta.json();
            })
            .then(datos => {
                const cuerpoTablaProductos = document.getElementById('productsTableBody');
                cuerpoTablaProductos.innerHTML = '';

                if (datos.success && datos.data.length > 0) {
                    datos.data.forEach(producto => {
                        const fila = `
                            <tr>
                                <td>${producto.idproductos}</td>
                                <td>${producto.nombre_producto}</td>
                                <td>$${parseFloat(producto.precio_producto).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                <td>${producto.nombre_categoria}</td>
                                <td>${producto.estados_idestados == 1 ? 'Activo' : 'Inactivo'}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-1" onclick="editarProducto(${producto.idproductos})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${producto.idproductos})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        cuerpoTablaProductos.insertAdjacentHTML('beforeend', fila);
                    });
                } else {
                    cuerpoTablaProductos.innerHTML = `<tr><td colspan="6" class="text-center">No hay productos para mostrar.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error al cargar productos:', error);
                const cuerpoTablaProductos = document.getElementById('productsTableBody');
                cuerpoTablaProductos.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error al cargar productos: ${error.message}</td></tr>`;
            });
    }

    // Evento para agregar nuevo producto
    botonAgregarProducto.addEventListener('click', () => {
        estaEditando = false;
        document.getElementById('productModalLabel').textContent = 'Agregar Producto';
        formularioProducto.reset();
        document.getElementById('productId').value = '';
        document.getElementById('productEstado').value = '1'; // Por defecto, estado activo
        modalProducto.show();
    });

    // Evento para guardar producto
    botonGuardarProducto.addEventListener('click', () => {
        const datosProducto = {
            id: document.getElementById('productId').value,
            nombre: document.getElementById('productName').value,
            precio: document.getElementById('productPrice').value,
            stock: document.getElementById('productStock').value,
            categoria: document.getElementById('productCategory').value,
            estado: document.getElementById('productEstado').value
        };

        const accion = estaEditando ? 'updateProducto' : 'createProducto';

        fetch(`../../controllers/admin/productos.php?action=${accion}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datosProducto)
        })
        .then(respuesta => respuesta.json())
        .then(datos => {
            if (datos.success) {
                alert(estaEditando ? 'Producto actualizado exitosamente' : 'Producto creado exitosamente');
                modalProducto.hide();
                cargarProductos();
            } else {
                alert('Error al guardar el producto');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Cargar datos iniciales
    cargarCategorias();
    cargarProductos();
});
