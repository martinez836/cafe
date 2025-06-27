<?php
require_once '../models/consultas.php';
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    $pdo = config::conectar();
    $consultas = new ConsultasMesero();
    
    // Obtener todas las mesas
    $mesas = $pdo->query("SELECT idmesas, nombre FROM mesas")->fetchAll(PDO::FETCH_ASSOC);
    
    $pedidos = [];
    foreach ($mesas as $mesa) {
        // Obtener pedidos activos de la mesa
        $pedidosMesa = $consultas->traerPedidosActivosPorMesa($pdo, $mesa['idmesas']);
        
        foreach ($pedidosMesa as $pedido) {
            // Obtener detalles del pedido
            $detalles = $consultas->traerDetallePedido($pdo, $pedido['idpedidos']);
            
            $pedidos[] = [
                'mesa_id' => $mesa['idmesas'],
                'mesa_nombre' => $mesa['nombre'],
                'pedido_id' => $pedido['idpedidos'],
                'productos' => $detalles
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'pedidos' => $pedidos
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar los pedidos activos: ' . $e->getMessage()
    ]);
} 