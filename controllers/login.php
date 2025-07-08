<?php
require_once '../models/consultas.php';
require_once '../config/config.php';
require_once '../config/security.php';
session_start();

header('Content-Type: application/json');

try {
    // Obtener datos del formulario POST
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos de acceso.']);
        exit;
    }
    $correo = SecurityUtils::sanitizeEmail($_POST['email']);
    $contrasena = SecurityUtils::sanitizePassword($_POST['password']);
    
    $pdo = config::conectar();
    $consultas = new ConsultasMesero();
    $usuario = $consultas->verificarCredencialesUsuario($pdo, $correo, $contrasena);
    
    if ($usuario) {
        if ((int)$usuario['rol_idrol'] === 2) {
            // Guardar datos mínimos en sesión
            $_SESSION['usuario'] = [
                'id' => $usuario['idusuarios'],
                'nombre' => SecurityUtils::escapeHtml($usuario['nombre_usuario']),
                'email' => SecurityUtils::escapeHtml($usuario['email_usuario']),
                'rol' => (int)$usuario['rol_idrol']
            ];
            // Generar token CSRF para la sesión
            SecurityUtils::generateCSRFToken();
            echo json_encode(['success' => true, 'redirect' => '../views/mesero.php']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Acceso solo permitido para meseros']);
        }
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 