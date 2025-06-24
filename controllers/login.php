<?php
require_once '../models/consultas.php';
require_once '../config/config.php';
session_start();

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['correo']) || !isset($data['contrasena'])) {
        throw new Exception('Datos incompletos');
    }
    $pdo = config::conectar();
    $consultas = new ConsultasMesero();
    $usuario = $consultas->verificarCredencialesUsuario($pdo, $data['correo'], $data['contrasena']);
    if ($usuario) {
        // Guardar datos mínimos en sesión
        $_SESSION['usuario'] = [
            'id' => $usuario['idusuarios'],
            'nombre' => $usuario['nombre_usuario'],
            'email' => $usuario['email_usuario'],
            'rol' => $usuario['rol_idrol']
        ];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 