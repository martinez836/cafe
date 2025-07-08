// Funcionalidad para la gestión de pedidos
document.addEventListener('DOMContentLoaded', function() {
    loadOrders(); // Cargar pedidos al cargar la página
    
    // Actualizar pedidos automáticamente cada 30 segundos
    setInterval(loadOrders, 30000);
});

function loadOrders() {
    fetch('../../controllers/admin/pedidos.php?action=get_all_orders')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const ordersTableBody = document.getElementById('ordersTableBody');
            ordersTableBody.innerHTML = ''; // Limpiar la tabla

            if (data.success && data.data.length > 0) {
                data.data.forEach(order => {
                    const row = `
                        <tr>
                            <td>${order.idpedidos}</td>
                            <td>${formatDateTime(order.fecha_hora_pedido)}</td>
                            <td>${order.nombre_mesa}</td>
                            <td>
                                <span class="badge bg-${getEstadoColor(order.estado_pedido)}">
                                    ${order.estado_pedido}
                                </span>
                            </td>
                            <td>${order.nombre_usuario}</td>
                            <td>
                                <button class="btn btn-sm btn-info me-1" onclick="verDetallePedido(${order.idpedidos})">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </button>
                            </td>
                        </tr>
                    `;
                    ordersTableBody.insertAdjacentHTML('beforeend', row);
                });
            } else {
                ordersTableBody.innerHTML = `<tr><td colspan="6" class="text-center">No hay pedidos para mostrar.</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error al cargar pedidos:', error);
            const ordersTableBody = document.getElementById('ordersTableBody');
            ordersTableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error al cargar pedidos: ${error.message}</td></tr>`;
        });
}

function getEstadoColor(estado) {
    switch(estado.toLowerCase()) {
        case 'pendiente':
            return 'warning';
        case 'en preparación':
            return 'info';
        case 'listo':
            return 'success';
        case 'entregado':
            return 'primary';
        case 'cancelado':
            return 'danger';
        default:
            return 'secondary';
    }
}

function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function verDetallePedido(idPedido) {
    const modal = new bootstrap.Modal(document.querySelector('#detallePedidoModal'));
    const content = document.getElementById('detallePedidoContent');
    
    // Mostrar loading
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando detalles del pedido...</p>
        </div>
    `;
    
    modal.show();

    // Cargar detalles del pedido
    fetch(`../../controllers/admin/pedidos.php?action=get_order_detail&id=${idPedido}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.data) {
                const pedido = data.data;
                let productosHtml = '';
                
                if (pedido.productos && pedido.productos.length > 0) {
                    productosHtml = `
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${pedido.productos.map(producto => `
                                        <tr>
                                            <td>
                                                ${producto.nombre_producto}
                                                ${producto.observaciones ? `<br><small class="text-muted">(${producto.observaciones})</small>` : ''}
                                            </td>
                                            <td>${producto.cantidad_producto}</td>
                                            <td>$${parseFloat(producto.precio_producto).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                            <td>$${parseFloat(producto.subtotal).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    productosHtml = '<p class="text-muted">No hay productos registrados para este pedido.</p>';
                }

                content.innerHTML = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>ID del Pedido:</strong> ${pedido.idpedidos}
                        </div>
                        <div class="col-md-6">
                            <strong>Mesa:</strong> ${pedido.nombre_mesa}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Fecha y Hora:</strong> ${formatDateTime(pedido.fecha_hora_pedido)}
                        </div>
                        <div class="col-md-6">
                            <strong>Estado:</strong> 
                            <span class="badge bg-${getEstadoColor(pedido.estado_pedido)}">
                                ${pedido.estado_pedido}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Usuario:</strong> ${pedido.nombre_usuario}
                        </div>
                        <div class="col-md-6">
                            <strong>Total:</strong> $${parseFloat(pedido.total_pedido || 0).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </div>
                    </div>
                    <hr>
                    <h6>Productos del Pedido:</h6>
                    ${productosHtml}
                `;
            } else {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${data.message || 'Error al cargar los detalles del pedido.'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error al cargar detalles del pedido:', error);
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error al cargar los detalles del pedido: ${error.message}
                </div>
            `;
        });
}
