<?php
require_once '../config/config.php';
require_once '../models/consultas.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['mesa_id']) && isset($data['productos']) && is_array($data['productos'])) {
        try {
            $pdo = config::conectar();
            require_once '../models/consultas.php';
            $consultas = new ConsultasMesero();
            $token_utilizado = isset($data['token']) ? $data['token'] : null;
            $pedido_id = $consultas->confirmarPedidoCliente($pdo, $data['mesa_id'], $data['productos'], $token_utilizado);
            echo json_encode([
                'success' => true,
                'message' => 'Pedido confirmado exitosamente',
                'pedido_id' => $pedido_id
            ]);
        } catch (Exception $e) {
            if (isset($pdo)) $pdo->rollBack();
            echo json_encode([
                'success' => false,
                'message' => 'Error al confirmar el pedido: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Datos del pedido incompletos'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
} 