<?php
require_once __DIR__ . '/MySQL.php'; 

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