<?php

require_once __DIR__ . '/MySQL.php';
require_once __DIR__ . '/../config/config.php';

class ConsultasUsuarios
{
    private $mysql;

    public function __construct()
    {
        $this->mysql = new MySql();
    }

    public function getAllUsuarios() {
        try {
            // Asumiendo que la tabla de usuarios se llama 'usuarios' y tiene campos como idusuarios, nombre_usuario, email_usuario, rol_idrol
            $sql = "SELECT usuarios.idusuarios, usuarios.nombre_usuario, usuarios.email_usuario, roles.nombre_rol 
                        FROM usuarios JOIN roles ON 
                        usuarios.rol_idrol = roles.idrol;";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getAllUsuarios: " . $e->getMessage());
            return [];
        }
    }
}

?>
