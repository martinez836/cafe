<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficas - Tienda de Café</title>
    <link href="../../assets/cssBootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet"  href="../../assets/css/graficas.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-coffee me-2"></i>Admin Café
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Bienvenido, Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../cocina.php">Módulo de Cocina</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar">
        <ul class="nav flex-column pt-3">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="usuarios.php">
                    <i class="fas fa-users me-2"></i>Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="inventario.php">
                    <i class="fas fa-boxes me-2"></i>Inventario
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="productos.php">
                    <i class="fas fa-mug-hot me-2"></i>Productos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pedidos.php">
                    <i class="fas fa-receipt me-2"></i>Pedidos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="graficas.php">
                    <i class="fas fa-chart-bar me-2"></i>Gráficas
                </a>
            </li>
        </ul>
    </div>

    <div class="content">
        <h2 class="mb-4">Gráficas y Reportes</h2>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-2"></i>Ventas por Categoría
                    </div>
                    <div class="card-body">
                        <canvas id="ventasCategoriaChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-pie me-2"></i>Productos más Vendidos
                    </div>
                    <div class="card-body">
                        <canvas id="productosVendidosChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i>Tendencia de Pedidos (Mensual)
                    </div>
                    <div class="card-body">
                        <canvas id="tendenciaPedidosChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-area me-2"></i>Ingresos Anuales
                    </div>
                    <div class="card-body">
                        <canvas id="ingresosAnualesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
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
    </script>
</body>
</html>
