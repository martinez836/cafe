<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Tienda de Café</title>
    <link href="../../assets/cssBootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/productos.css">
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
                <a class="nav-link active" href="productos.php">
                    <i class="fas fa-mug-hot me-2"></i>Productos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pedidos.php">
                    <i class="fas fa-receipt me-2"></i>Pedidos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="graficas.php">
                    <i class="fas fa-chart-bar me-2"></i>Gráficas
                </a>
            </li>
        </ul>
    </div>

    <div class="content">
        <h2 class="mb-4">Gestión de Productos</h2>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-list-alt me-2"></i>Lista de Productos
            </div>
            <div class="card-body">
                <p>Aquí se mostrará una tabla con la lista de productos y opciones para agregar, editar o eliminar.</p>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Categoría</th>
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

        <!-- Podrías agregar formularios para agregar/editar productos aquí -->

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function loadProducts() {
                fetch('../../controllers/admin/productos.php?action=getAllProductos')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const productsTableBody = document.getElementById('productsTableBody');
                        productsTableBody.innerHTML = ''; // Limpiar la tabla

                        if (data.success && data.data.length > 0) {
                            data.data.forEach(product => {
                                const row = `
                                    <tr>
                                        <td>${product.idproductos}</td>
                                        <td>${product.nombre_producto}</td>
                                        <td>$${parseFloat(product.precio_producto).toFixed(2)}</td>
                                        <td>${product.nombre_categoria}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning me-1"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                `;
                                productsTableBody.insertAdjacentHTML('beforeend', row);
                            });
                        } else {
                            productsTableBody.innerHTML = `<tr><td colspan="6" class="text-center">No hay productos para mostrar.</td></tr>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error al cargar productos:', error);
                        const productsTableBody = document.getElementById('productsTableBody');
                        productsTableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error al cargar productos: ${error.message}</td></tr>`;
                    });
            }

            loadProducts(); // Cargar productos al cargar la página
        });
    </script>
</body>
</html>
