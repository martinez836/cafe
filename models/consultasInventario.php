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
            $sql = "SELECT inventario.idinventario, inventario.articulo, inventario.stock, unidad_medida.nombre, unidad_medida.abreviatura FROM inventario JOIN unidad_medida ON inventario.unidad_medida_idunidad_medida = unidad_medida.idunidad_medida;";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getAllInventario: " . $e->getMessage());
            return [];
        }
    }

    // Puedes añadir funciones para agregar, editar o eliminar ítems de inventario aquí
}

?>
