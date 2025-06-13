<?php

require_once __DIR__ . '/MySQL.php';
require_once __DIR__ . '/../config/config.php';

class ConsultasProductos
{
    private $mysql;

    public function __construct()
    {
        $this->mysql = new MySql();
    }

    public function getAllProductos() {
        try {
            // Asumiendo que la tabla de productos se llama 'productos' y tiene campos como idproductos, nombre_producto, descripcion, precio, categoria_idcategoria
            // También asumiendo que hay una tabla 'categorias' con idcategoria y nombre_categoria
            $sql = "SELECT productos.idproductos, productos.nombre_producto, productos.precio_producto, categorias.nombre_categoria 
                        FROM productos JOIN categorias ON 
                        productos.fk_categoria = categorias.idcategorias;";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getAllProductos: " . $e->getMessage());
            return [];
        }
    }

    // Puedes añadir funciones para agregar, editar o eliminar productos aquí
}

?>
