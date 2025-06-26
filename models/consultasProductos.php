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
            $sql = "SELECT productos.*, categorias.nombre_categoria, estados.estado AS nombre_estado
                    FROM productos
                    LEFT JOIN categorias ON productos.fk_categoria = categorias.idcategorias
                    LEFT JOIN estados ON productos.estados_idestados = estados.idestados
                    ORDER BY productos.idproductos DESC";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            throw new Exception('Error al obtener productos: ' . $e->getMessage());
        }
    }

    public function getProducto($id) {
        try {
            $sql = "SELECT * FROM productos WHERE idproductos = ?";
            $params = [$id];
            $stmt = $this->mysql->ejecutarSentenciaPreparada($sql, 'i', $params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?? null;
        } catch (Exception $e) {
            throw new Exception('Error al obtener producto: ' . $e->getMessage());
        }
    }

    public function createProducto($data) {
        try {
            $sql = "INSERT INTO productos (nombre_producto, precio_producto, stock_producto, fk_categoria, estados_idestados) 
                    VALUES (?, ?, ?, ?, 1)";
            $params = [
                $data['nombre'],
                $data['precio'],
                $data['stock'],
                $data['categoria']
            ];
            return $this->mysql->ejecutarSentenciaPreparada($sql, 'sdii', $params);
        } catch (Exception $e) {
            throw new Exception('Error al crear producto: ' . $e->getMessage());
        }
    }

    public function updateProducto($data) {
        try {
            $sql = "UPDATE productos 
                    SET nombre_producto = ?, 
                        precio_producto = ?, 
                        stock_producto = ?, 
                        fk_categoria = ?, 
                        estados_idestados = ? 
                    WHERE idproductos = ?";
            $params = [
                $data['nombre'],
                $data['precio'],
                $data['stock'],
                $data['categoria'],
                $data['estado'],
                $data['id']
            ];
            return $this->mysql->ejecutarSentenciaPreparada($sql, 'sdiiis', $params);
        } catch (Exception $e) {
            throw new Exception('Error al actualizar producto: ' . $e->getMessage());
        }
    }

    public function deleteProducto($id) {
        try {
            $sql = "UPDATE productos SET estados_idestados = 2 WHERE idproductos = ?";
            $params = [$id];
            return $this->mysql->ejecutarSentenciaPreparada($sql, 'i', $params);
        } catch (Exception $e) {
            throw new Exception('Error al eliminar producto: ' . $e->getMessage());
        }
    }

    public function getProductosBajoStock() {
        try {
            $sql = "SELECT productos.*, categorias.nombre_categoria 
                    FROM productos
                    LEFT JOIN categorias ON productos.fk_categoria = categorias.idcategorias 
                    WHERE productos.stock_producto IS NOT NULL 
                    AND productos.stock_producto <= 10 
                    AND productos.estados_idestados = 5
                    ORDER BY productos.stock_producto ASC";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            throw new Exception('Error al obtener productos con bajo stock: ' . $e->getMessage());
        }
    }

    public function getProductosSinStock() {
        try {
            $sql = "SELECT productos.*, categorias.nombre_categoria 
                    FROM productos
                    LEFT JOIN categorias ON productos.fk_categoria = categorias.idcategorias 
                    WHERE (productos.stock_producto IS NULL OR productos.stock_producto = 0)
                    AND productos.estados_idestados = 5
                    ORDER BY productos.nombre_producto ASC";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            throw new Exception('Error al obtener productos sin stock: ' . $e->getMessage());
        }
    }

    public function getResumenStock() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_productos,
                        COUNT(CASE WHEN stock_producto IS NOT NULL AND stock_producto > 0 THEN 1 END) as con_stock,
                        COUNT(CASE WHEN stock_producto IS NOT NULL AND stock_producto <= 10 AND stock_producto > 0 THEN 1 END) as bajo_stock,
                        COUNT(CASE WHEN stock_producto IS NULL OR stock_producto = 0 THEN 1 END) as sin_stock
                    FROM productos 
                    WHERE estados_idestados = 5";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            throw new Exception('Error al obtener resumen de stock: ' . $e->getMessage());
        }
    }
}

?>
