<?php

require_once __DIR__ . '/../../models/consultasUsuarios.php';

header('Content-Type: application/json');

$consultas = new ConsultasUsuarios();

$action = $_GET['action'] ?? '';

$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'get_all_users':
            $usuarios = $consultas->getAllUsuarios();
            $response = [
                'success' => true,
                'data' => $usuarios
            ];
            break;

        // Puedes añadir más casos aquí para agregar, editar, eliminar usuarios, etc.

        default:
            $response = ['success' => false, 'message' => 'Invalid action provided.'];
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Server error: ' . $e->getMessage()];
    error_log("Usuarios Controller Error: " . $e->getMessage());
}

echo json_encode($response);

?> 