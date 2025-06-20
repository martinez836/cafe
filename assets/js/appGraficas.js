document.addEventListener('DOMContentLoaded', function() {
    // Función para inicializar y actualizar gráfica de Barras: Ventas por Categoría
    const ventasCategoriaCtx = document.getElementById('ventasCategoriaChart').getContext('2d');
    let ventasCategoriaChart = new Chart(ventasCategoriaCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Ventas ($)',
                data: [],
                backgroundColor: [
                    'rgba(139, 94, 60, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(139, 94, 60, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function loadVentasPorCategoria() {
        fetch('../../controllers/admin/graficas.php?action=get_ventas_por_categoria')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const labels = data.data.map(item => item.categoria);
                    const values = data.data.map(item => parseFloat(item.total_ventas));
                    ventasCategoriaChart.data.labels = labels;
                    ventasCategoriaChart.data.datasets[0].data = values;
                    ventasCategoriaChart.update();
                } else {
                    console.warn('No hay datos para Ventas por Categoría.', data.message);
                }
            })
            .catch(error => console.error('Error al cargar Ventas por Categoría:', error));
    }

    // Gráfica de Pastel: Productos más Vendidos
    const productosVendidosCtx = document.getElementById('productosVendidosChart').getContext('2d');
    let productosVendidosChart = new Chart(productosVendidosCtx, {
        type: 'pie',
        data: {
            labels: [],
            datasets: [{
                label: 'Cantidad Vendida',
                data: [],
                backgroundColor: [
                    'rgba(139, 94, 60, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 206, 86, 0.7)'
                ],
                borderColor: [
                    'rgba(139, 94, 60, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false,
                    text: 'Productos más Vendidos'
                }
            }
        }
    });

    function loadProductosMasVendidos() {
        fetch('../../controllers/admin/graficas.php?action=get_productos_mas_vendidos')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const labels = data.data.map(item => item.producto);
                    const values = data.data.map(item => parseFloat(item.cantidad_vendida));
                    productosVendidosChart.data.labels = labels;
                    productosVendidosChart.data.datasets[0].data = values;
                    productosVendidosChart.update();
                } else {
                    console.warn('No hay datos para Productos más Vendidos.', data.message);
                }
            })
            .catch(error => console.error('Error al cargar Productos más Vendidos:', error));
    }

    // Gráfica de Líneas: Tendencia de Pedidos (Mensual)
    const tendenciaPedidosCtx = document.getElementById('tendenciaPedidosChart').getContext('2d');
    let tendenciaPedidosChart = new Chart(tendenciaPedidosCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Número de Pedidos',
                data: [],
                borderColor: '#8B5E3C',
                backgroundColor: 'rgba(139, 94, 60, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: false,
                    text: 'Tendencia de Pedidos (Mensual)'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function loadTendenciaPedidosMensual() {
        fetch('../../controllers/admin/graficas.php?action=get_tendencia_pedidos_mensual')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const labels = data.data.map(item => item.mes);
                    const values = data.data.map(item => parseInt(item.total_pedidos));
                    tendenciaPedidosChart.data.labels = labels;
                    tendenciaPedidosChart.data.datasets[0].data = values;
                    tendenciaPedidosChart.update();
                } else {
                    console.warn('No hay datos para Tendencia de Pedidos Mensual.', data.message);
                }
            })
            .catch(error => console.error('Error al cargar Tendencia de Pedidos Mensual:', error));
    }

    // Gráfica de Área: Ingresos Anuales
    const ingresosAnualesCtx = document.getElementById('ingresosAnualesChart').getContext('2d');
    let ingresosAnualesChart = new Chart(ingresosAnualesCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Ingresos Anuales ($)',
                data: [],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                fill: true,
                tension: 0.2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: false,
                    text: 'Ingresos Anuales'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function loadIngresosAnuales() {
        fetch('../../controllers/admin/graficas.php?action=get_ingresos_anuales')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const labels = data.data.map(item => item.año);
                    const values = data.data.map(item => parseFloat(item.total_ingresos));
                    ingresosAnualesChart.data.labels = labels;
                    ingresosAnualesChart.data.datasets[0].data = values;
                    ingresosAnualesChart.update();
                } else {
                    console.warn('No hay datos para Ingresos Anuales.', data.message);
                }
            })
            .catch(error => console.error('Error al cargar Ingresos Anuales:', error));
    }

    // Cargar todas las gráficas al cargar la página
    loadVentasPorCategoria();
    loadProductosMasVendidos();
    loadTendenciaPedidosMensual();
    loadIngresosAnuales();
});
