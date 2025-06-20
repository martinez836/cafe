<?php
require_once '../models/consultas.php';
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
        $consultas = new ConsultasMesero();
        $token_data = $consultas->validarToken($pdo, $token);
        
        if ($token_data) {
            echo json_encode([
                'success' => true,
                'mesa_id' => $token_data['mesa_id'],
                'expiracion' => $token_data['fecha_hora_expiracion'],
                'expiracion_timestamp' => $token_data['expiracion_timestamp'],
                'debug' => 'Token found and valid',
                'input_token' => $token
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Token inválido o expirado',
                'debug' => 'No matching token',
                'input_token' => $token
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al validar el token: ' . $e->getMessage(),
            'debug' => 'Exception',
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