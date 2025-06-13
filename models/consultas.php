<?php
require_once __DIR__ . '/MySQL.php'; 
require_once __DIR__ . '/../config/config.php';

class consultas
{
    public $mysql;

    public function __construct()
    {
        $this->mysql = new MySQL();
    }

    public function traer_mesas()
    {
        $this->mysql->conectar();
        $consulta = 
        "select nombre from mesas;";
        $resultado = $this->mysql->efectuarConsulta($consulta);
            $this->mysql->desconectar();
            return $resultado;
    }

    public function traer_categorias()
    {
        $this->mysql->conectar();
        $consulta = 
        "select * from categorias;";
        $resultado = $this->mysql->efectuarConsulta($consulta);
            $this->mysql->desconectar();
            return $resultado;
    }

    public function traerPedidosPendinetes()
    {
        $this->mysql->conectar();
        $consulta = 
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
            ORDER BY p.fecha_hora_pedido DESC;
        ";
        $resultado = $this->mysql->efectuarConsulta($consulta);
        $this->mysql->desconectar();
        return $resultado;
    }
    public function traer_productos_por_categoria($categoria)
    {
        $this->mysql->conectar();
        $consulta = 
        "select * from productos where fk_categoria = '$categoria' order by nombre_producto asc;";
        $resultado = $this->mysql->efectuarConsulta($consulta);
        $this->mysql->desconectar();
        return $resultado;
    }

};?>