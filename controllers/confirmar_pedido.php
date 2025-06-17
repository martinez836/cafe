<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['mesa_id']) && isset($data['productos']) && is_array($data['productos'])) {
        try {
            $pdo = config::conectar();
            $pdo->beginTransaction();
            
            // 1. Crear el pedido
            $stmt = $pdo->prepare("INSERT INTO pedidos (fecha_hora_pedido, total_pedido, estados_idestados, mesas_idmesas, usuarios_idusuarios, tipo_pedido, token_utilizado) VALUES (NOW(), 0, 1, ?, 1, 'cliente', ?)");
            $token_utilizado = isset($data['token']) ? $data['token'] : null;
            $stmt->execute([$data['mesa_id'], $token_utilizado]);
            $pedido_id = $pdo->lastInsertId();
            
            // 2. Insertar los productos del pedido
            $stmt = $pdo->prepare("INSERT INTO detalle_pedidos (observaciones, precio_producto, cantidad_producto, subtotal, pedidos_idpedidos, productos_idproductos) VALUES (?, ?, ?, ?, ?, ?)");
            
            foreach ($data['productos'] as $producto) {
                $subtotal = $producto['precio'] * $producto['cantidad'];
                $stmt->execute([
                    $producto['comentario'] ?? null,
                    $producto['precio'],
                    $producto['cantidad'],
                    $subtotal,
                    $pedido_id,
                    $producto['id']
                ]);
            }
            
            // 3. Calcular y actualizar el total del pedido
            $stmt = $pdo->prepare("UPDATE pedidos SET total_pedido = (SELECT SUM(subtotal) FROM detalle_pedidos WHERE pedidos_idpedidos = ?) WHERE idpedidos = ?");
            $stmt->execute([$pedido_id, $pedido_id]);
            
            // 4. Invalidar el token
            $stmt = $pdo->prepare("UPDATE tokens_mesa SET estado_token = 'usado' WHERE mesas_idmesas = ? AND estado_token = 'activo'");
            $stmt->execute([$data['mesa_id']]);
            
            $pdo->commit();
            
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