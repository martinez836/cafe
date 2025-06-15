<?php

require_once __DIR__ . '/../../models/consultasInventario.php';

header('Content-Type: application/json');

$consultas = new ConsultasInventario();

$action = $_GET['action'] ?? '';

$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'get_all_inventory':
            $inventario = $consultas->getAllInventario();
            $response = [
                'success' => true,
                'data' => $inventario
            ];
            break;
        case 'traer_unidad_medida':
            $unidadMedida = $consultas->traerUnidadMedida();
            $response = [
                'success' => true,
                'data' => $unidadMedida
            ];
            break;
        case 'crear':
            $articulo = $_POST['articulo'] ?? '';
            $stock = $_POST['stock'] ?? 0;
            $unidadMedida = $_POST['unidadMedida'] ?? 0;
            if (empty($articulo) || $stock < 0 || $unidadMedida <= 0) {
                $response = ['success' => false, 'message' => 'Invalid input data.'];
            } else {
                $insertar = $consultas->insertarArticulo($articulo, $stock, $unidadMedida);
                if ($insertar) {
                    $response = ['success' => true, 'message' => 'Article created successfully.'];
                } else {
                    $response = ['success' => false, 'message' => 'Failed to create article.'];
                }
            }
            break;
        case 'editar':
            $articuloId = $_POST['idInventario'];
            $articulo = $_POST['articulo'] ?? '';
            $stock = $_POST['stock'] ?? 0;
            $unidadMedida = $_POST['unidadMedida'] ?? 0;
            if(empty($articulo) || $stock < 0 || $unidadMedida <= 0 || empty($articuloId)) {
                $response = ['success' => false, 'message' => 'Invalid input data.'];
            } else {
                $editar = $consultas->editarArticulo($articuloId, $articulo, $stock, $unidadMedida);
                if ($editar) {
                    $response = ['success' => true, 'message' => 'Article updated successfully.'];
                } else {
                    $response = ['success' => false, 'message' => 'Failed to update article.'];
                }
            }
            break;
        case 'eliminar':
            $articuloId = $_POST['idInventario'];
            if(empty($articuloId)) {
                $response = ['success' => false, 'message' => 'Invalid article ID.'];
            } else {
                 $eliminar = $consultas->eliminarArticulo($articuloId);
                 if ($eliminar) {
                     $response = ['success' => true, 'message' => 'Article deleted successfully.'];
                 } else {
                     $response = ['success' => false, 'message' => 'Failed to delete article.'];
                 }
            }
            break;

        // Puedes añadir más casos aquí para agregar, editar, eliminar ítems de inventario, etc.

        default:
            $response = ['success' => false, 'message' => 'Invalid action provided.'];
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
    error_log("Inventario Controller Error: " . $e->getMessage());
}

echo json_encode($response);

?> 