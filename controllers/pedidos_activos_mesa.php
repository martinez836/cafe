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
        $consultas = new ConsultasMesero();
        $pedidos = $consultas->traerPedidosActivosPorMesa($pdo, $data['mesa_id']);
        $resultado = [];
        foreach ($pedidos as $pedido) {
            $productos = $consultas->traerDetallePedido($pdo, $pedido['idpedidos']);
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