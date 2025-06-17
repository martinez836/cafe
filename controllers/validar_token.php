<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    
    if (empty($token)) {
        echo json_encode([
            'success' => false,
            'message' => 'Token no proporcionado',
            'debug' => 'No token in POST'
        ]);
        exit;
    }
    
    try {
        // Establecer la zona horaria
        date_default_timezone_set('America/Bogota');
        
        $pdo = config::conectar();
        $sql = "SELECT t.*, m.idmesas as mesa_id FROM tokens_mesa t JOIN mesas m ON t.mesas_idmesas = m.idmesas WHERE t.token = ? AND t.estado_token = 'activo' AND t.fecha_hora_expiracion > NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);
        $token_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($token_data) {
            // Convertir la fecha de expiración a timestamp
            $expiracion_timestamp = strtotime($token_data['fecha_hora_expiracion']);
            
            echo json_encode([
                'success' => true,
                'mesa_id' => $token_data['mesa_id'],
                'expiracion' => $token_data['fecha_hora_expiracion'],
                'expiracion_timestamp' => $expiracion_timestamp * 1000,
                'debug' => 'Token found and valid',
                'sql' => $sql,
                'input_token' => $token
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Token inválido o expirado',
                'debug' => 'No matching token',
                'sql' => $sql,
                'input_token' => $token
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al validar el token: ' . $e->getMessage(),
            'debug' => 'PDOException',
            'exception' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido',
        'debug' => 'Not POST'
    ]);
} 