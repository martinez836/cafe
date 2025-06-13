<?php

require_once __DIR__ . '/MySQL.php';
require_once __DIR__ . '/../config/config.php';

class ConsultasGraficas
{
    private $mysql;

    public function __construct()
    {
        $this->mysql = new MySql();
    }

    public function getVentasPorCategoria() {
        try {
            $sql = "
                SELECT
                    c.nombre_categoria AS categoria,
                    SUM(dp.cantidad_producto * p.precio) AS total_ventas
                FROM detalle_pedidos dp
                JOIN productos p ON dp.productos_idproductos = p.idproductos
                JOIN categorias c ON p.categoria_idcategoria = c.idcategoria
                JOIN pedidos ped ON dp.pedidos_idpedidos = ped.idpedidos
                WHERE ped.estados_idestados = 2 -- Solo pedidos completados
                GROUP BY c.nombre_categoria
                ORDER BY total_ventas DESC;
            ";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getVentasPorCategoria: " . $e->getMessage());
            return [];
        }
    }

    public function getProductosMasVendidos($limit = 5) {
        try {
            $sql = "
                SELECT
                    pr.nombre_producto AS producto,
                    SUM(dp.cantidad_producto) AS cantidad_vendida
                FROM detalle_pedidos dp
                JOIN productos pr ON dp.productos_idproductos = pr.idproductos
                JOIN pedidos ped ON dp.pedidos_idpedidos = ped.idpedidos
                WHERE ped.estados_idestados = 2 -- Solo pedidos completados
                GROUP BY pr.nombre_producto
                ORDER BY cantidad_vendida DESC
                LIMIT ?;
            ";
            $parametros = [$limit];
            $stmt = $this->mysql->ejecutarSentenciaPreparada($sql, "i", $parametros);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getProductosMasVendidos: " . $e->getMessage());
            return [];
        }
    }

    public function getTendenciaPedidosMensual() {
        try {
            $sql = "
                SELECT
                    DATE_FORMAT(fecha_hora_pedido, '%Y-%m') AS mes,
                    COUNT(idpedidos) AS total_pedidos
                FROM pedidos
                GROUP BY mes
                ORDER BY mes ASC;
            ";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getTendenciaPedidosMensual: " . $e->getMessage());
            return [];
        }
    }

    public function getIngresosAnuales() {
        try {
            $sql = "
                SELECT
                    YEAR(fecha_hora_pedido) AS año,
                    SUM(total) AS total_ingresos
                FROM pedidos
                WHERE estados_idestados = 2 -- Solo ingresos de pedidos completados
                GROUP BY año
                ORDER BY año ASC;
            ";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getIngresosAnuales: " . $e->getMessage());
            return [];
        }
    }
}

?>
