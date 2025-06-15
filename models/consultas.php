<?php
require_once __DIR__ . '/MySQL.php'; 
require_once __DIR__ . '/../config/config.php';

class consultas
{
    private $mysql;

    public function __construct() {
        $this->mysql = new MySql();
    }

    public function traer_mesas()
    {
        $query = 'SELECT * FROM mesas';
        $stmt = $this->mysql->efectuarConsulta($query);
        return $stmt;
    }

    public function traer_categorias()
    {
        $query = 'SELECT * FROM categorias';
        $stmt = $this->mysql->efectuarConsulta($query);
        return $stmt;
    }
    public function traer_productos_por_categoria($categoria)
    {
        $query = 
        "select * from productos where fk_categoria = :categoria order by nombre_producto asc;";
        $parameters = [':categoria' => $categoria];
        $stmt = $this->mysql->ejecutarSentenciaPreparada($query, 's', $parameters);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

};?>