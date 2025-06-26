<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cocina - Tienda de Café</title>
    <link href="../assets/cssBootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/estiloCocina.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="text-center">
                <i class="fas fa-coffee coffee-icon"></i>
                <h1 class="d-inline">Tienda de Café</h1>
                <p class="mb-0 mt-2">Módulo de Cocina</p>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row g-4 flex-column-reverse flex-lg-row">
            <!-- Pedidos Pendientes de Preparación -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-hourglass-start me-2"></i>
                        Pedidos Pendientes
                    </div>
                    <div class="card-body">
                        <div id="pedidos_pendientes">
                            <!-- Los pedidos pendientes se cargarán aquí -->
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5>No hay pedidos pendientes</h5>
                                <p  style="color: #8B5E3C;">Todos los pedidos han sido preparados.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles del Pedido y Acciones -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-utensils me-2"></i>
                        Detalles del Pedido
                    </div>
                    <div class="card-body">
                        <div id="detalles_pedido">
                            <div class="empty-state text-center py-5">
                                <i class="fas fa-hand-pointer fa-3x mb-3"></i>
                                <h5>Selecciona un pedido</h5>
                                <p style="color: #8B5E3C;">Haz clic en un pedido de la lista para ver los detalles y prepararlo.</p>
                            </div>
                            <!-- Los detalles del pedido seleccionado se cargarán aquí -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/appCocina.js"></script>
</body>
</html> 