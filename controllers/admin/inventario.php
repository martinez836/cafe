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