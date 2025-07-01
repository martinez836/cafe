<?php
require_once '../models/consultas.php';
require_once '../config/config.php';
require_once '../config/security.php';
session_start();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar que los datos JSON sean válidos
    $data = SecurityUtils::sanitizeJsonData($data);
    
    // Validar campos requeridos
    SecurityUtils::validateRequiredKeys($data, ['correo', 'contrasena']);
    
    // Sanitizar y validar entradas
    $correo = SecurityUtils::sanitizeEmail($data['correo']);
    $contrasena = SecurityUtils::sanitizePassword($data['contrasena']);
    
    $pdo = config::conectar();
    $consultas = new ConsultasMesero();
    $usuario = $consultas->verificarCredencialesUsuario($pdo, $correo, $contrasena);
    
    if ($usuario) {
        // Guardar datos mínimos en sesión
        $_SESSION['usuario'] = [
            'id' => $usuario['idusuarios'],
            'nombre' => SecurityUtils::escapeHtml($usuario['nombre_usuario']),
            'email' => SecurityUtils::escapeHtml($usuario['email_usuario']),
            'rol' => (int)$usuario['rol_idrol']
        ];
        
        // Generar token CSRF para la sesión
        SecurityUtils::generateCSRFToken();
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 