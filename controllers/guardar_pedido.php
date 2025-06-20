<?php
require_once '../models/consultas.php';
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['mesa_id']) || !isset($data['productos'])) {
        throw new Exception('Datos incompletos');
    }
    $token = isset($data['token']) ? $data['token'] : null;
    $total = isset($data['total']) ? $data['total'] : 0;
    $pdo = config::conectar();
    $consultas = new ConsultasMesero();
    $pdo->beginTransaction();
    try {
        $pedidoId = $consultas->guardarPedido($pdo, $data['mesa_id'], 1, $token); // 1 es el ID del usuario por defecto
        foreach ($data['productos'] as $producto) {
            $consultas->guardarDetallePedido($pdo, [
                'id' => $producto['id'],
                'cantidad' => $producto['cantidad'],
                'precio' => $producto['precio'],
                'comentario' => $producto['comentario']
            ], $pedidoId);
        }
        $consultas->actualizarTotalPedido($pdo, $total, $pedidoId);
        $pdo->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Pedido guardado correctamente',
            'pedido_id' => $pedidoId
        ]);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al guardar el pedido: ' . $e->getMessage()
    ]);
} 