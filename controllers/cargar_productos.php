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

    if (!$productos || $productos->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'No hay productos disponibles para esta categoría.',
            'html' => '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No hay productos disponibles para esta categoría.</div>'
        ]);
        exit;
    }

    $html = '';
    foreach ($productos as $producto) {
        $id = $producto['idproductos'];
        $nombre = $producto['nombre_producto'];
        $precio = $producto['precio_producto'];
        
        $html .= '<div class="col-md-4 mb-4">
            <div class="card h-100" data-id="' . $id . '">
                <div class="card-body">
                    <h5 class="card-title">' . $nombre . '</h5>
                    <p class="card-text">$' . number_format($precio, 2) . '</p>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control" min="1" value="1">
                        <button class="btn btn-primary" data-precio="' . $precio . '">
                            Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>';
    }

    echo json_encode([
        'success' => true,
        'html' => $html,
        'debug' => [
            'categoria_recibida' => $_POST['idcategorias'],
            'categoria_filtrada' => $categoria,
            'productos_encontrados' => $productos->rowCount()
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'html' => '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($e->getMessage()) . '</div>',
        'debug' => [
            'post_data' => $_POST,
            'error' => $e->getMessage()
        ]
    ]);
}
