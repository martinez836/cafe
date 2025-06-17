<?php
require_once '../models/consultas.php';
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['mesa_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Datos incompletos: mesa_id es requerido'
        ]);
        exit;
    }

    try {
        $pdo = config::conectar();

        $stmt = $pdo->prepare("
            SELECT p.idpedidos, p.fecha_hora_pedido, p.total_pedido, p.token_utilizado
            FROM pedidos p 
            WHERE p.mesas_idmesas = ? 
            AND p.estados_idestados = 1
            ORDER BY p.fecha_hora_pedido DESC
        ");
        $stmt->execute([$data['mesa_id']]);
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];
        foreach ($pedidos as $pedido) {
            $stmt = $pdo->prepare("
                SELECT dp.productos_idproductos as id, 
                       pr.nombre_producto as nombre, 
                       dp.cantidad_producto as cantidad, 
                       dp.precio_producto as precio, 
                       dp.observaciones as comentario
                FROM detalle_pedidos dp
                JOIN productos pr ON pr.idproductos = dp.productos_idproductos
                WHERE dp.pedidos_idpedidos = ?
            ");
            $stmt->execute([$pedido['idpedidos']]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $resultado[] = [
                'pedido_id' => $pedido['idpedidos'],
                'fecha_hora' => $pedido['fecha_hora_pedido'],
                'total_pedido' => $pedido['total_pedido'],
                'token_utilizado' => $pedido['token_utilizado'],
                'productos' => $productos
            ];
        }

        echo json_encode([
            'success' => true,
            'pedidos' => $resultado
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al cargar los pedidos activos: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
} 