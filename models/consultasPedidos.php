<?php

require_once __DIR__ . '/MySQL.php';
require_once __DIR__ . '/../config/config.php';

class ConsultasPedidos
{
    private $mysql;

    public function __construct()
    {
        $this->mysql = new MySql();
    }

    public function getAllPedidos() {
        try {
            $sql = "
                SELECT
                    p.idpedidos,
                    p.fecha_hora_pedido,
                    m.nombre AS nombre_mesa,
                    e.estado AS estado_pedido,
                    u.nombre_usuario AS nombre_usuario -- Usar el nombre de columna correcto de la tabla usuarios
                FROM pedidos p
                JOIN mesas m ON p.mesas_idmesas = m.idmesas
                JOIN estados e ON p.estados_idestados = e.idestados
                JOIN usuarios u ON p.usuarios_idusuarios = u.idusuarios
                ORDER BY p.fecha_hora_pedido DESC;
            ";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getAllPedidos: " . $e->getMessage());
            return [];
        }
    }
}

?>
