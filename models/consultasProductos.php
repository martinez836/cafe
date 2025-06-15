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
            $sql = "SELECT productos.*, categorias.nombre_categoria 
                    FROM productos
                    LEFT JOIN categorias ON productos.fk_categoria = categorias.idcategorias 
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
            return $this->mysql->ejecutarSentenciaPreparada($sql, 'sdiisi', $params);
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
}

?>
