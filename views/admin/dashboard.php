<?php
session_start();
// Evitar caché para que no se pueda volver con el botón atrás
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistema de Café</title>
    <link href="/Cafe/assets/cssBootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"  href="/Cafe/assets/css/dashboard.css">
    <link rel="stylesheet" href="/Cafe/assets/css/notificaciones.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-coffee me-2"></i>Admin Café
            </a>
            <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" aria-label="Toggle sidebar">
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
                        <a class="nav-link" href="../cajero.php">Módulo de Cajero</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar fija para escritorio -->
    <div class="sidebar d-none d-lg-block">
        <ul class="nav flex-column pt-3">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="usuarios.php">
                    <i class="fas fa-users me-2"></i>Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="productos.php">
                    <i class="fas fa-mug-hot me-2"></i>Productos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="gestion_mesas.php">
                    <i class="fas fa-chair me-2"></i>Gestión Mesas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pedidos.php">
                    <i class="fas fa-receipt me-2"></i>Ventas
                </a>
            </li>
            <li class="nav-item">
                    <a class="nav-link" href="balanceGeneral.php">
                        <i class="fa-solid fa-file-pdf"></i>Balance
                    </a>
                </li>
            <li class="nav-item">
                <a class="nav-link" href="graficas.php">
                    <i class="fas fa-chart-bar me-2"></i>Gráficas
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar offcanvas para móvil -->
    <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menú</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav flex-column pt-3">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">
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
                    <a class="nav-link active" href="gestion_mesas.php">
                        <i class="fas fa-chair me-2"></i>Gestión Mesas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pedidos.php">
                        <i class="fas fa-receipt me-2"></i>Ventas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="balanceGeneral.php">
                        <i class="fa-solid fa-file-pdf"></i>Balance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="graficas.php">
                        <i class="fas fa-chart-bar me-2"></i>Gráficas
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="content">
        <h2 class="mb-4">Dashboard de Administración</h2>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">1,250</h3>
                                <p class="mb-0">Pedidos Totales</p>
                            </div>
                            <i class="fas fa-receipt fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">$25,000</h3>
                                <p class="mb-0">Ingresos del Mes</p>
                            </div>
                            <i class="fas fa-dollar-sign fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">15</h3>
                                <p class="mb-0">Usuarios</p>
                            </div>
                            <i class="fas fa-user-plus fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-area me-2"></i>Ventas Diarias
            </div>
            <div class="card-body">
                <canvas id="ventasDiariasChart"></canvas>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-list me-2"></i>Últimos Pedidos
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush" id="ultimosPedidosList">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pedido #123 - Mesa 1
                                <span class="badge bg-info rounded-pill">Pendiente</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pedido #122 - Mesa 3
                                <span class="badge bg-success rounded-pill">Completado</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pedido #121 - Para llevar
                                <span class="badge bg-success rounded-pill">Completado</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="../../assets/jsBootstrap/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Función para cargar los datos del dashboard
            function loadDashboardData() {
                fetch('../../controllers/admin/dashboard.php?action=get_dashboard_data')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const dashboardData = data.data;

                            // Actualizar tarjetas de resumen
                            document.querySelector('.card.bg-primary h3').textContent = dashboardData.totalPedidos.toLocaleString();
                            document.querySelector('.card.bg-success h3').textContent = `$${dashboardData.ingresosMesActual.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                            document.querySelector('.card.bg-warning h3').textContent = dashboardData.nuevosUsuariosMesActual.toLocaleString();

                            // Actualizar gráfica de Ventas Diarias
                            updateVentasDiariasChart(dashboardData.ventasDiarias.labels, dashboardData.ventasDiarias.data);

                            // Actualizar últimos pedidos
                            const ultimosPedidosList = document.querySelector('#ultimosPedidosList');
                            if (ultimosPedidosList) {
                                ultimosPedidosList.innerHTML = '';
                                if (dashboardData.ultimosPedidos.length > 0) {
                                    dashboardData.ultimosPedidos.forEach(pedido => {
                                        const li = document.createElement('li');
                                        li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                                        let statusClass = '';
                                        if (pedido.status === 'Pendiente') {
                                            statusClass = 'bg-info';
                                        } else if (pedido.status === 'Completado') {
                                            statusClass = 'bg-success';
                                        }
                                        li.innerHTML = `
                                            Pedido #${pedido.id} - ${pedido.table}
                                            <span class="badge ${statusClass} rounded-pill">${pedido.status}</span>
                                        `;
                                        ultimosPedidosList.appendChild(li);
                                    });
                                } else {
                                    ultimosPedidosList.innerHTML = '<li class="list-group-item text-center">No hay pedidos recientes.</li>';
                                }
                            }

                            // Comentarios recientes (si tienes datos para ellos)
                            const comentariosRecientesList = document.querySelector('#comentariosRecientesList');
                            if (comentariosRecientesList) {
                                comentariosRecientesList.innerHTML = '';
                                if (dashboardData.comentariosRecientes.length > 0) {
                                    dashboardData.comentariosRecientes.forEach(comentario => {
                                        const li = document.createElement('li');
                                        li.classList.add('list-group-item');
                                        li.textContent = `"${comentario.texto}" - ${comentario.autor}`;
                                        comentariosRecientesList.appendChild(li);
                                    });
                                } else {
                                    comentariosRecientesList.innerHTML = '<li class="list-group-item text-center">No hay comentarios recientes.</li>';
                                }
                            }

                        } else {
                            console.error('Error al cargar datos del dashboard:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error de red al cargar el dashboard:', error);
                    });
            }

            // Chart para Ventas Diarias (inicialización y actualización)
            const ventasDiariasCtx = document.getElementById('ventasDiariasChart').getContext('2d');
            let ventasDiariasChart = new Chart(ventasDiariasCtx, {
                type: 'line',
                data: {
                    labels: [], // Se llenarán con datos reales
                    datasets: [{
                        label: 'Ventas ($)',
                        data: [], // Se llenarán con datos reales
                        borderColor: '#8B5E3C',
                        backgroundColor: 'rgba(139, 94, 60, 0.2)',
                        fill: true,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: false,
                            text: 'Ventas Diarias'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            function updateVentasDiariasChart(labels, data) {
                ventasDiariasChart.data.labels = labels;
                ventasDiariasChart.data.datasets[0].data = data;
                ventasDiariasChart.update();
            }

            // Cargar datos al iniciar la página
            loadDashboardData();
        });
    </script>
</body>
</html>
