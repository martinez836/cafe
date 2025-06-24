<?php
require_once '../models/consultas.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['idcategorias']) || empty($_POST['idcategorias'])) {
        throw new Exception('Categoría no válida - POST data: ' . print_r($_POST, true));
    }

    $categoria = filter_var($_POST['idcategorias'], FILTER_SANITIZE_NUMBER_INT);
    if (!$categoria) {
        throw new Exception('ID de categoría inválido - Valor original: ' . $_POST['idcategorias']);
    }

    $consultas = new ConsultasMesero();
    $productos = $consultas->traer_productos_por_categoria($categoria);

    if ($productos && mysqli_num_rows($productos) > 0) {
        while ($producto = mysqli_fetch_assoc($productos)) {
            echo '
            <div class="col-md-4 mb-3">
              <div class="card card-cafe p-3">
                <h5 class = "text-center">' . htmlspecialchars($producto['nombre_producto']) . '</h5>
                <p class="text-center">Precio: $' . number_format($producto['precio_producto'], 2) . '</p>
                <input type="number" class="form-control text-center" min="0" id="inputCantidad" placeholder="Cantidad">
                <button 
                class="btn btn-primary mt-2" 
                onclick="abrirModal(this, ' . $producto['idproductos'] . ', \'' . addslashes($producto['nombre_producto']) . '\')">
                Agregar
                </button>
              </div>
            </div>';
        }
    } else {
        echo "<p>No hay productos disponibles para esta categoría.</p>";
    }
} else {
    echo "<p>Categoría no válida.</p>";
}
?>

