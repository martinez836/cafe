        <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Tienda de Café</title>
    <link href="/Cafe/assets/cssBootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Cafe/assets/css/productos.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
                <a class="nav-link active" href="productos.php">
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
                    <a class="nav-link active" href="productos.php">
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
        <h2 class="mb-4">Gestión de Productos</h2>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-list-alt me-2"></i>Lista de Productos
            </div>
            <div class="card-body">
                <div class="table-responsive">
                <button class="btn btn-primary mb-3" id="addProductBtn">
                <i class="fa-solid fa-plus me-2"></i>Crear
                </button>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <!-- Datos de productos se cargarán aquí -->
                            <tr>
                                <td colspan="6" class="text-center">Cargando productos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal para Agregar/Editar Producto -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Agregar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="productForm">
                            <input type="hidden" id="productId">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Nombre del Producto</label>
                                <input type="text" class="form-control" id="productName" required>
                            </div>
                            <div class="mb-3">
                                <label for="productPrice" class="form-label">Precio</label>
                                <input type="number" class="form-control" placeholder="Ej: 5000 (5.000,00)" id="productPrice" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="productStock" class="form-label">Stock</label>
                                <input type="number" class="form-control" placeholder="Dejar nulo si no ejerce Stock" id="productStock" required>
                            </div>
                            <div class="mb-3">
                                <label for="productCategory" class="form-label">Categoría</label>
                                <select class="form-control" id="productCategory" required>
                                    <!-- Las categorías se cargarán dinámicamente -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="productEstado" class="form-label">Estado</label>    
                                <select class="form-control" id="productEstado" required>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="saveProduct">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/jsBootstrap/bootstrap.bundle.js"></script>
    <script src="../../assets/js/appProductos.js"></script>
</body>
</html>
