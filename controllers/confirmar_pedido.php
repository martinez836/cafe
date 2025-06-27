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
            // Buscar pedido activo para la mesa
            $pedidoActivo = $consultas->traerPedidosActivosPorMesa($pdo, $data['mesa_id']);
            if ($pedidoActivo && count($pedidoActivo) > 0) {
                // Hay pedido activo, actualizamos detalle_pedidos
                $pedido_id = $pedidoActivo[0]['idpedidos'];
                // 1. Para cada producto recibido:
                foreach ($data['productos'] as $producto) {
                    // Buscar si ya existe en detalle_pedidos
                    $stmt = $pdo->prepare("SELECT iddetalle_pedidos, cantidad_producto FROM detalle_pedidos WHERE pedidos_idpedidos = ? AND productos_idproductos = ? AND observaciones = ?");
                    $stmt->execute([$pedido_id, $producto['id'], $producto['comentario'] ?? null]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        // Si existe, sumamos la cantidad
                        $nuevaCantidad = $row['cantidad_producto'] + $producto['cantidad'];
                        $nuevoSubtotal = $nuevaCantidad * $producto['precio'];
                        $stmtUp = $pdo->prepare("UPDATE detalle_pedidos SET cantidad_producto = ?, subtotal = ? WHERE iddetalle_pedidos = ?");
                        $stmtUp->execute([$nuevaCantidad, $nuevoSubtotal, $row['iddetalle_pedidos']]);
                    } else {
                        // Si no existe, insertamos nuevo
                        $stmtIns = $pdo->prepare("INSERT INTO detalle_pedidos (observaciones, precio_producto, cantidad_producto, subtotal, pedidos_idpedidos, productos_idproductos) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmtIns->execute([
                            $producto['comentario'] ?? null,
                            $producto['precio'],
                            $producto['cantidad'],
                            $producto['precio'] * $producto['cantidad'],
                            $pedido_id,
                            $producto['id']
                        ]);
                    }
                    // Descontar stock
                    $stmtStock = $pdo->prepare("UPDATE productos SET stock_producto = stock_producto - ? WHERE idproductos = ?");
                    $stmtStock->execute([$producto['cantidad'], $producto['id']]);
                }
                // Actualizar total del pedido
                $consultas->actualizarTotalPedido($pdo, array_sum(array_map(function($p){return $p['precio']*$p['cantidad'];}, $data['productos'])), $pedido_id);
                echo json_encode([
                    'success' => true,
                    'message' => 'Pedido actualizado exitosamente',
                    'pedido_id' => $pedido_id
                ]);
            } else {
                // No hay pedido activo, crear uno nuevo (como antes)
                $pedido_id = $consultas->confirmarPedidoCliente($pdo, $data['mesa_id'], $data['productos'], $token_utilizado);
                echo json_encode([
                    'success' => true,
                    'message' => 'Pedido confirmado exitosamente',
                    'pedido_id' => $pedido_id
                ]);
            }
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