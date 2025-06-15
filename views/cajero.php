<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caja - Tienda de Café</title>
    <link href="../assets/cssBootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../assets/css/estiloCajero.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="text-center">
                <i class="fas fa-coffee coffee-icon"></i>
                <h1 class="d-inline">Tienda de Café</h1>
                <p class="mb-0 mt-2">Módulo de Caja</p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Lista de Pedidos -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Pedidos Pendientes de Pago
                    </div>
                    <div class="card-body">
                        <div id="ordersList">
                            <!-- Los pedidos se cargarán aquí -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de Liquidación -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-cash-register me-2"></i>
                        Liquidación de Pedido
                    </div>
                    <div class="card-body">
                        <div id="paymentPanel">
                            <div class="empty-state">
                                <i class="fas fa-hand-pointer"></i>
                                <h5>Selecciona un pedido</h5>
                                <p>Haz clic en un pedido de la lista para proceder con el pago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/js/appCajero.js"></script>
</body>
</html>