<?php
require_once '../models/consultas.php';
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['mesa_id']) || !isset($data['token'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Datos incompletos: mesa_id y token son requeridos'
        ]);
        exit;
    }
    
    try {
        $pdo = config::conectar();
        $consultas = new ConsultasMesero();
        $pedidos = $consultas->traerPedidosPorMesaYToken($pdo, $data['mesa_id'], $data['token']);
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
            'pedidos' => $resultado,
            'debug' => [
                'mesa_id' => $data['mesa_id'],
                'token' => $data['token'],
                'pedidos_encontrados' => count($resultado)
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al cargar los pedidos del usuario: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
} 