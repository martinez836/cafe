<?php

require_once __DIR__ . '/MySQL.php';
require_once __DIR__ . '/../config/config.php';

class ConsultasDashboard
{
    private $mysql;

    public function __construct()
    {
        $this->mysql = new MySql();
    }

    public function getTotalPedidos() {
        try {
            $sql = "SELECT COUNT(idpedidos) AS total_pedidos FROM pedidos;";
            $result = $this->mysql->efectuarConsulta($sql);
            return $result[0]['total_pedidos'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getTotalPedidos: " . $e->getMessage());
            return 0;
        }
    }

    public function getIngresosMesActual() {
        try {
            $sql = "SELECT SUM(total) AS ingresos_mes FROM pedidos WHERE MONTH(fecha_hora_pedido) = MONTH(NOW()) AND YEAR(fecha_hora_pedido) = YEAR(NOW()) AND estados_idestados = 2;";
            $result = $this->mysql->efectuarConsulta($sql);
            return $result[0]['ingresos_mes'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getIngresosMesActual: " . $e->getMessage());
            return 0;
        }
    }

    public function getNuevosUsuariosMesActual() {
        try {
            // La tabla `usuarios` no tiene una columna `fecha_registro`. Si tienes una columna de fecha de creación de usuario,
            // por favor, actualiza esta consulta con el nombre correcto de la columna.
            // Por ahora, devolveremos el conteo total de usuarios para evitar el error.
            $sql = "SELECT COUNT(idusuarios) AS nuevos_usuarios FROM usuarios; "; // Quitamos la condición de fecha
            $result = $this->mysql->efectuarConsulta($sql);
            return $result[0]['nuevos_usuarios'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getNuevosUsuariosMesActual: " . $e->getMessage());
            return 0;
        }
    }

    public function getVentasDiarias() {
        try {
            $sql = "SELECT DATE(fecha_hora_pedido) as fecha, SUM(total) as total_ventas FROM pedidos WHERE estados_idestados = 2 GROUP BY DATE(fecha_hora_pedido) ORDER BY fecha ASC LIMIT 7;";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getVentasDiarias: " . $e->getMessage());
            return [];
        }
    }

    public function getUltimosPedidos($limit = 5) {
        try {
            $sql = "SELECT p.idpedidos, m.nombre AS nombre_mesa, p.estados_idestados AS status_id FROM pedidos p JOIN mesas m ON p.mesas_idmesas = m.idmesas ORDER BY p.fecha_hora_pedido DESC LIMIT ?;";
            $parametros = [$limit];
            $stmt = $this->mysql->ejecutarSentenciaPreparada($sql, "i", $parametros);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getUltimosPedidos: " . $e->getMessage());
            return [];
        }
    }

    // Asumiendo que no hay una tabla 'comentarios' directamente, 
    // esta función podría ser para un futuro desarrollo o basada en observaciones de pedidos.
    public function getComentariosRecientes($limit = 3) {
        // Por ahora, devolveremos un array vacío o datos estáticos ya que no hay una tabla 'comentarios'.
        // Si tienes una tabla de comentarios o un campo de observaciones en los pedidos que se pueda usar, 
        // podemos ajustar esta consulta.
        return []; 
    }
}

?>
