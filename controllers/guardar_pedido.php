<?php
require_once '../models/consultas.php';
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['mesa_id']) || !isset($data['token']) || !isset($data['productos'])) {
        throw new Exception('Datos incompletos');
    }

    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $consultas = new ConsultasMesero();
    
    // Iniciar transacción
    $pdo->beginTransaction();

    try {
        // Guardar el pedido
        $pedidoId = $consultas->guardarPedido($pdo, $data['mesa_id'], 1); // 1 es el ID del usuario por defecto

        // Guardar los detalles del pedido
        foreach ($data['productos'] as $producto) {
            $consultas->guardarDetallePedido($pdo, [
                'id' => $producto['id'],
                'cantidad' => $producto['cantidad'],
                'precio' => $producto['precio'],
                'comentario' => $producto['comentario']
            ], $pedidoId);
        }

        // Actualizar el total del pedido
        $consultas->actualizarTotalPedido($pdo, $data['total'], $pedidoId);

        // Confirmar la transacción
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