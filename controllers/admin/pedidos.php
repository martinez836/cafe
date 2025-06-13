<?php

require_once __DIR__ . '/../../models/consultasPedidos.php';

header('Content-Type: application/json');

$consultas = new ConsultasPedidos();

$action = $_GET['action'] ?? '';

$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'get_all_orders':
            $pedidos = $consultas->getAllPedidos();
            $response = [
                'success' => true,
                'data' => $pedidos
            ];
            break;

        // Puedes añadir más casos aquí para filtrar, buscar o actualizar pedidos, etc.

        default:
            $response = ['success' => false, 'message' => 'Invalid action provided.'];
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
    error_log("Pedidos Controller Error: " . $e->getMessage());
}

echo json_encode($response);

?> 