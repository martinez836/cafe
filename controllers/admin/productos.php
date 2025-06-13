<?php

require_once __DIR__ . '/../../models/consultasProductos.php';

header('Content-Type: application/json');

$consultas = new ConsultasProductos();

$action = $_GET['action'] ?? '';

$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'getAllProductos':
            $productos = $consultas->getAllProductos();
            $response = [
                'success' => true,
                'data' => $productos
            ];
            break;

        // Puedes añadir más casos aquí para agregar, editar, eliminar productos, etc.

        default:
            $response = ['success' => false, 'message' => 'Invalid action provided.'];
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
    error_log("Productos Controller Error: " . $e->getMessage());
}

echo json_encode($response);

?> 