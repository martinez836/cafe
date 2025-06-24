<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$mysqli = new mysqli('localhost', 'root', '', 'bd_cafe');
if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: Por favor, inténtalo más tarde.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['correo']) || !isset($_POST['codigo']) || !isset($_POST['nueva_contrasena'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos necesarios para actualizar la contraseña.']);
        exit;
    }

    $correo = $_POST['correo'];
    $codigo = $_POST['codigo'];
    $nueva_contrasena = $_POST['nueva_contrasena'];

    // Validación mínima en backend
    if (empty($nueva_contrasena)) {
        echo json_encode(['success' => false, 'message' => 'La nueva contraseña no puede estar vacía.']);
        exit;
    }
    if (strlen($nueva_contrasena) < 5) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 5 caracteres.']);
        exit;
    }

    // Verificar que el código de recuperación sea válido
    $stmt = $mysqli->prepare("SELECT * FROM recuperacion WHERE correo_recuperacion = ? AND codigo_recuperacion = ?");
    $stmt->bind_param("ss", $correo, $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // El código es válido, actualizar la contraseña
        $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);

        // Actualizar la contraseña del usuario
        $stmt = $mysqli->prepare("UPDATE usuarios SET contrasena_usuario = ? WHERE email_usuario = ?");
        $stmt->bind_param("ss", $nueva_contrasena_hash, $correo);
        $exito_actualizacion = $stmt->execute();

        if ($exito_actualizacion) {
            // Eliminar el código de recuperación usado
            $stmt = $mysqli->prepare("DELETE FROM recuperacion WHERE correo_recuperacion = ? AND codigo_recuperacion = ?");
            $stmt->bind_param("ss", $correo, $codigo);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Tu contraseña ha sido actualizada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Hubo un error al actualizar la contraseña. Por favor, inténtalo de nuevo.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'El código de recuperación no es válido o ha expirado. Por favor, solicita un nuevo código.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no válido.']);
} 