<?php
require_once '../models/consultas.php';
require_once '../config/config.php';
header('Content-Type: application/json');
try {
    $pdo = config::conectar();
    $consultas = new ConsultasMesero();
    $mesas = $pdo->query("SELECT m.idmesas, m.nombre FROM mesas m JOIN tokens_mesa t ON t.mesas_idmesas = m.idmesas WHERE t.estado_token = 'activo' AND t.fecha_hora_expiracion > NOW() GROUP BY m.idmesas")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'mesas' => $mesas]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 