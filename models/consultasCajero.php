<?php
require_once __DIR__ . '/MySQL.php';
require_once __DIR__ . '/../config/config.php';
class consultasCajero
{
    private $mysql;

    public function __construct() {
        $this->mysql = new MySql();
    }

    public function traerPedidosPendientes()
    {
        try {
            //code...
            $query = 
            "
                SELECT
                p.idpedidos,
                p.fecha_hora_pedido,
                m.nombre AS nombre_mesa,
                u.nombre_usuario,
                dp.producto,
                dp.precio_producto,
                dp.cantidad_producto,
                dp.subtotal,
                pr.nombre_producto,
                pr.precio_producto AS precio_actual
                FROM pedidos p
                JOIN detalle_pedidos dp ON p.idpedidos = dp.pedidos_idpedidos
                JOIN productos pr ON dp.productos_idproductos = pr.idproductos
                JOIN mesas m ON p.mesas_idmesas = m.idmesas
                JOIN usuarios u on p.usuarios_idusuarios = u.idusuarios
                WHERE p.estados_idestados = 1
                ORDER BY p.idpedidos DESC;
            ";
             $stmt = $this->mysql->efectuarConsulta($query);
             return $stmt;

        } catch (Exception $e) {
            //throw $th;
            throw new Exception("Error al traer pedidos pendientes: " . $e->getMessage());
        }
        
    }

    public function cambiarEstadoPedido($idPedido, $nuevoEstado)
    {
        $query = "UPDATE pedidos SET estados_idestados = :nuevoEstado WHERE idpedidos = :idPedido";
        $parameters = [
            ':nuevoEstado' => $nuevoEstado,
            ':idPedido' => $idPedido
        ];
        $stmt = $this->mysql->ejecutarSentenciaPreparada($query, 'ii', $parameters);
        return $stmt;
    }
}