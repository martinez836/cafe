<?php
require_once '../models/consultas.php';

if (isset($_POST['idcategoria']) && !empty($_POST['idcategoria'])) {
    $categoria = $_POST['idcategoria'];

    $consultas = new consultas();
    $productos = $consultas->traer_productos_por_categoria($categoria);

    if ($productos && mysqli_num_rows($productos) > 0) {
        while ($producto = mysqli_fetch_assoc($productos)) {
            $id = $producto['idproductos'];
            $nombre = htmlspecialchars($producto['nombre_producto']);
            $precio = number_format($producto['precio_producto'], 2);
            echo '
            <div class="col-md-4 mb-3">
              <div class="card card-cafe p-3" data-id="' . $id . '">
                <h5 class="text-center">' . $nombre . '</h5>
                <p class="text-center">Precio: $' . $precio . '</p>
                <input 
                  type="number" 
                  class="form-control text-center cantidad-input" 
                  min="1" 
                  value="1" 
                  placeholder="Cantidad">
                <button 
                  class="btn btn-primary mt-2" 
                  data-precio="' . $producto['precio_producto'] . '"
                  onclick="abrirModal(this, ' . $producto['idproductos'] . ', \'' . addslashes($producto['nombre_producto']) . '\', ' . $producto['precio_producto'] . ')">
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
