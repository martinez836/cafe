<?php
require_once '../models/consultas.php';
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['mesa'])) throw new Exception('Mesa no especificada');
    $mesa = $data['mesa'];
    $pedido_id = isset($data['pedido_id']) ? $data['pedido_id'] : null;

    $pdo = config::conectar();
    $consultas = new ConsultasMesero();
    
    // Cambiar estado de la mesa a libre
    $consultas->actualizarEstadoMesa($pdo, $mesa, 4); // 4 = Libre
    
    // Si se especifica un pedido_id, cambiar solo ese pedido
    if ($pedido_id) {
        $consultas->liberarPedidoPorId($pdo, $pedido_id);
    } else {
        // Cambiar todos los pedidos activos de la mesa a libre
        $consultas->actualizarPedidosActivosAMesaLibre($pdo, $mesa);
    }

    echo json_encode(['success' => true, 'message' => 'Mesa liberada']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 