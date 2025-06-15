<?php

require_once __DIR__ . '/MySQL.php';
require_once __DIR__ . '/../config/config.php';

class ConsultasInventario
{
    private $mysql;

    public function __construct()
    {
        $this->mysql = new MySql();
    }

    public function getAllInventario() {
        try {
            // Asumiendo que la tabla de inventario se llama 'inventario' y tiene campos como idproducto, nombre_producto, cantidad, unidad_medida
            $sql = "SELECT inventario.idinventario, inventario.articulo, inventario.stock, unidad_medida.nombre, unidad_medida.abreviatura, unidad_medida.idunidad_medida
            FROM inventario
            JOIN unidad_medida ON inventario.unidad_medida_idunidad_medida = unidad_medida.idunidad_medida where estados_idestados = 1;";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getAllInventario: " . $e->getMessage());
            return [];
        }
    }

    public function traerUnidadMedida()
    {
        $sql = "select * from unidad_medida";
        try {
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error traerUnidadMedida: " . $e->getMessage());
            return [];
        }
    }
    public function insertarArticulo($articulo,$stock,$unidadMedida)
    {
        $estado = 1;
        $sql = "insert into inventario (articulo,stock,estados_idestados,unidad_medida_idunidad_medida) values (?,?,?,?)";
        try {
            //code...
            $parametros = [$articulo, $stock,$estado, $unidadMedida];
            $stmt = $this->mysql->ejecutarSentenciaPreparada($sql, "sdii", $parametros);
            if ($stmt->rowCount() > 0) {
                return true; // Artículo insertado correctamente
            } else {
                return false; // No se insertó ningún artículo
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function editarArticulo($idInventario, $articulo, $stock, $unidadMedida)
    {
        $sql = "UPDATE inventario SET articulo = ?, stock = ?, unidad_medida_idunidad_medida = ? WHERE idinventario = ?";
        try {
            $parametros = [$articulo, $stock, $unidadMedida, $idInventario];
            $stmt = $this->mysql->ejecutarSentenciaPreparada($sql, "sdii", $parametros);
            if ($stmt->rowCount() > 0) {
                return true; // Artículo actualizado correctamente
            } else {
                return false; // No se actualizó ningún artículo
            }
        } catch (\Throwable $th) {
            error_log("Error editarArticulo: " . $th->getMessage());
            return false;
        }
    }

    public function eliminarArticulo($idInventario)
    {
        $sql = "update inventario set estados_idestados = 2 where idinventario = ?";
        try {
            $parametros = [$idInventario];
            $stmt = $this->mysql->ejecutarSentenciaPreparada($sql, "i", $parametros);
            if ($stmt->rowCount() > 0) {
                return true; // Artículo eliminado correctamente
            } else {
                return false; // No se eliminó ningún artículo
            }
        } catch (\Throwable $th) {
            error_log("Error eliminarArticulo: " . $th->getMessage());
            return false;
        }
    }
}

?>
