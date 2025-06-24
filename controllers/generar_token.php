<?php
require_once '../models/consultas.php';
require_once '../config/config.php';

// Establecer la zona horaria para Colombia
date_default_timezone_set('America/Bogota');

header('Content-Type: application/json');

try {
    // Procesar cancelación de token antes de requerir mesa_id
    if (isset($_POST['cancelar_token'])) {
        $idtoken = intval($_POST['cancelar_token']);
        $pdo = config::conectar();
        $consultas = new ConsultasMesero();
        $consultas->cancelarTokenPorId($pdo, $idtoken);
        echo json_encode(['success' => true, 'message' => 'Token cancelado correctamente']);
        exit;
    }
    if (isset($_POST['cancelar_token_por_valor'])) {
        $token = $_POST['cancelar_token_por_valor'];
        $pdo = config::conectar();
        $consultas = new ConsultasMesero();
        $consultas->cancelarTokenPorValor($pdo, $token);
        echo json_encode(['success' => true, 'message' => 'Token cancelado correctamente']);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['mesa_id'])) {
        $mesa_id = intval($_GET['mesa_id']);
        $pdo = config::conectar();
        $consultas = new ConsultasMesero();
        $tokens = $consultas->obtenerTokensPorMesa($pdo, $mesa_id);
        echo json_encode(['success' => true, 'tokens' => $tokens]);
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['activos'])) {
        $pdo = config::conectar();
        $consultas = new ConsultasMesero();
        $tokens = $consultas->obtenerTokensActivosConMesa($pdo);
        echo json_encode(['success' => true, 'tokens' => $tokens]);
        exit;
    }
    
    // Obtener mesa_id desde POST (form-urlencoded) o JSON
    $mesa_id = null;
    if (isset($_POST['mesa_id'])) {
        $mesa_id = intval($_POST['mesa_id']);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if (isset($data['mesa'])) {
            $mesa_id = intval($data['mesa']);
        } elseif (isset($data['mesa_id'])) {
            $mesa_id = intval($data['mesa_id']);
        }
    }
    
    if (!$mesa_id) throw new Exception('Mesa no especificada');
    
    $pdo = config::conectar();
    $consultas = new ConsultasMesero();
    // Verificar si existe al menos un usuario en la tabla usuarios
    $usuario = $consultas->getPrimerUsuario($pdo);
    if (!$usuario) {
        // Si no hay usuarios, crear uno por defecto con la estructura correcta
        $usuario_id = $consultas->crearUsuarioPorDefecto($pdo);
    } else {
        $usuario_id = $usuario['idusuarios'];
    }

    // Generar token de 4 dígitos
    $token = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

    // Tiempo de expiración: 15 minutos desde ahora
    $expiracion = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Usar función del modelo para insertar el token
    $consultas = new ConsultasMesero();
    $consultas->insertarTokenMesa($pdo, $token, $expiracion, $mesa_id, $usuario_id);

    echo json_encode(['success' => true, 'token' => $token, 'expira' => $expiracion]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 