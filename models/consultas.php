<?php

require_once __DIR__ . '/MySQL.php';
require_once __DIR__ . '/../config/config.php';

class ConsultasMesero
{
    private $mysql;

    public function __construct()
    {
        $this->mysql = new MySql();
    }

    // MESAS
    public function traerMesas()
    {
        $consulta = "SELECT m.*, 
            (SELECT COUNT(*) FROM tokens_mesa t WHERE t.mesas_idmesas = m.idmesas AND t.estado_token = 'activo' AND t.fecha_hora_expiracion > NOW()) as tiene_token_activo,
            (SELECT COUNT(*) FROM pedidos p WHERE p.mesas_idmesas = m.idmesas AND p.estados_idestados = 1) as tiene_pedido_activo
        FROM mesas m
        WHERE m.estados_idestados IN (1,4,3) ORDER BY m.nombre;";
        return $this->mysql->efectuarConsulta($consulta);
    }

    public function traerMesasOcupadas($pdo)
    {
        $stmt = $pdo->query("SELECT idmesas, nombre FROM mesas WHERE estados_idestados = 3");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarEstadoMesa($pdo, $mesaId, $nuevoEstado)
    {
        $stmt = $pdo->prepare("UPDATE mesas SET estados_idestados = ? WHERE idmesas = ?");
        $stmt->execute([$nuevoEstado, $mesaId]);
    }

    public function obtenerNombreMesa($pdo, $mesaId)
    {
        $stmt = $pdo->prepare("SELECT nombre FROM mesas WHERE idmesas = ?");
        $stmt->execute([$mesaId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['nombre'] : '';
    }

    // CATEGORIAS
    public function traerCategorias()
    {
        $consulta = "SELECT idcategorias, nombre_categoria FROM categorias WHERE estados_idestados = 1 ORDER BY nombre_categoria;";
        return $this->mysql->efectuarConsulta($consulta);
    }

    // PRODUCTOS
    public function traer_productos_por_categoria($categoria)
    {
        $consulta = "SELECT * FROM productos WHERE fk_categoria = ? AND estados_idestados = 1 ORDER BY nombre_producto;";
        $parametros = [$categoria];
        return $this->mysql->ejecutarSentenciaPreparada($consulta, "i", $parametros);
    }

    // PEDIDOS
    public function guardarPedido($pdo, $mesaId, $usuarioId, $token = null) {
        $stmt = $pdo->prepare("INSERT INTO pedidos (fecha_hora_pedido, total_pedido, estados_idestados, mesas_idmesas, usuarios_idusuarios, token_utilizado) VALUES (NOW(), 0, 1, ?, ?, ?)");
        $stmt->execute([$mesaId, $usuarioId, $token]);
        return $pdo->lastInsertId();
    }

    public function guardarDetallePedido($pdo, $detalle, $idPedido) {
        $stmt = $pdo->prepare("INSERT INTO detalle_pedidos (observaciones, precio_producto, cantidad_producto, subtotal, pedidos_idpedidos, productos_idproductos) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $detalle['comentario'],
            $detalle['precio'],
            $detalle['cantidad'],
            $detalle['precio'] * $detalle['cantidad'],
            $idPedido,
            $detalle['id']
        ]);
    }

    public function actualizarTotalPedido($pdo, $total, $idPedido) {
        $stmt = $pdo->prepare("UPDATE pedidos SET total_pedido = ? WHERE idpedidos = ?");
        $stmt->execute([$total, $idPedido]);
    }

    public function traerPedidosActivosPorMesa($pdo, $mesaId) {
        $stmt = $pdo->prepare("SELECT idpedidos, fecha_hora_pedido, total_pedido, token_utilizado FROM pedidos WHERE mesas_idmesas = ? AND estados_idestados = 1 ORDER BY fecha_hora_pedido DESC");
        $stmt->execute([$mesaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function traerDetallePedido($pdo, $pedidoId) {
        $stmt = $pdo->prepare("SELECT dp.productos_idproductos as id, pr.nombre_producto as nombre, dp.cantidad_producto as cantidad, dp.precio_producto as precio, dp.observaciones as comentario FROM detalle_pedidos dp JOIN productos pr ON pr.idproductos = dp.productos_idproductos WHERE dp.pedidos_idpedidos = ?");
        $stmt->execute([$pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarPedidosActivosAMesaLibre($pdo, $mesaId) {
        $stmt = $pdo->prepare("UPDATE pedidos SET estados_idestados = 4 WHERE mesas_idmesas = ? AND estados_idestados = 1");
        $stmt->execute([$mesaId]);
    }

    // TOKENS
    public function obtenerTokensPorMesa($pdo, $mesaId) {
        $stmt = $pdo->prepare("SELECT idtoken_mesa, token, fecha_hora_generacion, fecha_hora_expiracion, estado_token FROM tokens_mesa WHERE mesas_idmesas = ? ORDER BY fecha_hora_generacion DESC");
        $stmt->execute([$mesaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTokensActivosConMesa($pdo) {
        $stmt = $pdo->query("SELECT t.token, t.fecha_hora_generacion, t.fecha_hora_expiracion, t.estado_token, m.nombre as mesa_nombre, m.idmesas FROM tokens_mesa t JOIN mesas m ON t.mesas_idmesas = m.idmesas WHERE t.estado_token = 'activo' AND t.fecha_hora_expiracion > NOW() ORDER BY t.fecha_hora_generacion DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validarToken($pdo, $token) {
        $sql = "SELECT t.*, m.idmesas as mesa_id FROM tokens_mesa t JOIN mesas m ON t.mesas_idmesas = m.idmesas WHERE t.token = ? AND t.estado_token = 'activo' AND t.fecha_hora_expiracion > NOW()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$token]);
        $token_data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($token_data) {
            $token_data['expiracion_timestamp'] = strtotime($token_data['fecha_hora_expiracion']) * 1000;
        }
        return $token_data;
    }

    // PEDIDOS POR TOKEN (usuario mesa)
    public function traerPedidosPorMesaYToken($pdo, $mesaId, $token) {
        $stmt = $pdo->prepare("SELECT p.idpedidos, p.fecha_hora_pedido, p.total_pedido, p.token_utilizado FROM pedidos p WHERE p.mesas_idmesas = ? AND p.token_utilizado = ? AND p.estados_idestados = 1 ORDER BY p.fecha_hora_pedido DESC");
        $stmt->execute([$mesaId, $token]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CONFIRMAR PEDIDO (desde confirmar_pedido.php)
    public function confirmarPedidoCliente($pdo, $mesaId, $productos, $token = null) {
        try {
            $pdo->beginTransaction();
            // 1. Crear el pedido (con tipo_pedido y token_utilizado)
            $stmt = $pdo->prepare("INSERT INTO pedidos (fecha_hora_pedido, total_pedido, estados_idestados, mesas_idmesas, usuarios_idusuarios, tipo_pedido, token_utilizado) VALUES (NOW(), 0, 1, ?, 1, 'cliente', ?)");
            $stmt->execute([$mesaId, $token]);
            $pedido_id = $pdo->lastInsertId();

            // 2. Insertar los productos del pedido
            $stmt = $pdo->prepare("INSERT INTO detalle_pedidos (observaciones, precio_producto, cantidad_producto, subtotal, pedidos_idpedidos, productos_idproductos) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($productos as $producto) {
                $subtotal = $producto['precio'] * $producto['cantidad'];
                $stmt->execute([
                    $producto['comentario'] ?? null,
                    $producto['precio'],
                    $producto['cantidad'],
                    $subtotal,
                    $pedido_id,
                    $producto['id']
                ]);
            }

            // 3. Calcular y actualizar el total del pedido (usando subconsulta)
            $stmt = $pdo->prepare("UPDATE pedidos SET total_pedido = (SELECT SUM(subtotal) FROM detalle_pedidos WHERE pedidos_idpedidos = ?) WHERE idpedidos = ?");
            $stmt->execute([$pedido_id, $pedido_id]);

            // 4. Invalidar el token
            $stmt = $pdo->prepare("UPDATE tokens_mesa SET estado_token = 'usado' WHERE mesas_idmesas = ? AND estado_token = 'activo'");
            $stmt->execute([$mesaId]);

            $pdo->commit();
            return $pedido_id;
        } catch (Exception $e) {
            if (isset($pdo)) $pdo->rollBack();
            throw $e;
        }
    }

    // TOKENS: Insertar nuevo token para una mesa
    public function insertarTokenMesa($pdo, $token, $expiracion, $mesa_id, $usuario_id) {
        $stmt = $pdo->prepare("INSERT INTO tokens_mesa (token, fecha_hora_generacion, fecha_hora_expiracion, estado_token, mesas_idmesas, usuarios_idusuarios) VALUES (?, NOW(), ?, 'activo', ?, ?)");
        $stmt->execute([$token, $expiracion, $mesa_id, $usuario_id]);
        return $pdo->lastInsertId();
    }

    // TOKENS: Cancelar token por idtoken_mesa
    public function cancelarTokenPorId($pdo, $idtoken) {
        $stmt = $pdo->prepare("UPDATE tokens_mesa SET estado_token = 'cancelado' WHERE idtoken_mesa = ?");
        $stmt->execute([$idtoken]);
        return $stmt->rowCount();
    }

    // TOKENS: Cancelar token por valor de token
    public function cancelarTokenPorValor($pdo, $token) {
        $stmt = $pdo->prepare("UPDATE tokens_mesa SET estado_token = 'cancelado' WHERE token = ? AND estado_token = 'activo'");
        $stmt->execute([$token]);
        return $stmt->rowCount();
    }

    // USUARIOS: Obtener el primer usuario
    public function getPrimerUsuario($pdo) {
        $stmt = $pdo->query("SELECT idusuarios FROM usuarios LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // USUARIOS: Crear usuario por defecto
    public function crearUsuarioPorDefecto($pdo) {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, contraseña_usuario, email_usuario, estados_idestados, rol_idrol) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Mesero', 'password123', 'mesero@cafe.com', 1, 1]);
        return $pdo->lastInsertId();
    }

    // PEDIDOS: Cambiar un pedido específico a estado libre (4)
    public function liberarPedidoPorId($pdo, $pedido_id) {
        $stmt = $pdo->prepare("UPDATE pedidos SET estados_idestados = 4 WHERE idpedidos = ?");
        $stmt->execute([$pedido_id]);
        return $stmt->rowCount();
    }
}

?>