<?php
require_once '../models/consultas.php';
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $consultas = new ConsultasMesero();
    
    // Obtener mesas ocupadas
    $mesasOcupadas = $consultas->traerMesasOcupadas($pdo);
    
    $pedidos = [];
    foreach ($mesasOcupadas as $mesa) {
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