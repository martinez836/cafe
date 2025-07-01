<?php
require_once '../config/config.php';
require_once '../models/consultas.php';
require_once '../config/security.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar que los datos JSON sean válidos
        $data = SecurityUtils::sanitizeJsonData($data);
        
        // Validar campos requeridos
        SecurityUtils::validateRequiredKeys($data, ['mesa_id', 'productos']);
        
        // Sanitizar entradas principales
        $mesa_id = SecurityUtils::sanitizeId($data['mesa_id'], 'ID de mesa');
        $token_utilizado = isset($data['token']) ? SecurityUtils::sanitizeToken($data['token']) : null;
        
        // Validar que productos sea un array
        if (!is_array($data['productos'])) {
            throw new Exception('Formato de productos inválido');
        }
        
        // Sanitizar cada producto
        $productos_sanitizados = [];
        foreach ($data['productos'] as $producto) {
            if (!is_array($producto)) {
                throw new Exception('Formato de producto inválido');
            }
            
            SecurityUtils::validateRequiredKeys($producto, ['id', 'cantidad', 'precio']);
            
            $productos_sanitizados[] = [
                'id' => SecurityUtils::sanitizeId($producto['id'], 'ID de producto'),
                'cantidad' => SecurityUtils::sanitizeQuantity($producto['cantidad']),
                'precio' => SecurityUtils::sanitizePrice($producto['precio']),
                'comentario' => isset($producto['comentario']) ? SecurityUtils::sanitizeComment($producto['comentario']) : ''
            ];
        }
        
        $pdo = config::conectar();
        $consultas = new ConsultasMesero();
        
        // Buscar pedido activo para la mesa
        $pedidoActivo = $consultas->traerPedidosActivosPorMesa($pdo, $mesa_id);
        
        if ($pedidoActivo && count($pedidoActivo) > 0) {
            // Hay pedido activo, actualizamos detalle_pedidos
            $pedido_id = (int)$pedidoActivo[0]['idpedidos'];
            
            // 1. Para cada producto recibido:
            foreach ($productos_sanitizados as $producto) {
                // 2. Verificar si ya existe en detalle_pedidos
                $detalleExistente = $consultas->traerDetallePedidoPorProducto($pdo, $pedido_id, $producto['id']);
                
                if ($detalleExistente) {
                    // 3. Si existe, actualizar cantidad
                    $nueva_cantidad = $detalleExistente['cantidad'] + $producto['cantidad'];
                    $consultas->actualizarCantidadDetallePedido($pdo, $pedido_id, $producto['id'], $nueva_cantidad);
                } else {
                    // 4. Si no existe, insertar nuevo detalle
                    $consultas->guardarDetallePedido($pdo, $producto, $pedido_id);
                }
            }
            
            // 5. Actualizar total del pedido
            $total_actual = $consultas->calcularTotalPedido($pdo, $pedido_id);
            $consultas->actualizarTotalPedido($pdo, $total_actual, $pedido_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Productos agregados al pedido existente',
                'pedido_id' => $pedido_id
            ]);
        } else {
            // No hay pedido activo, crear uno nuevo
            $pdo->beginTransaction();
            try {
                $pedido_id = $consultas->confirmarPedidoCliente($pdo, $mesa_id, $productos_sanitizados, $token_utilizado);
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Nuevo pedido creado correctamente',
                    'pedido_id' => $pedido_id
                ]);
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al procesar el pedido: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
} 