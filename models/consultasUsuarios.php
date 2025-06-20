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
            $sql = "SELECT usuarios.idusuarios, usuarios.nombre_usuario, usuarios.email_usuario, roles.nombre_rol, roles.idrol,usuarios.estados_idestados
                        FROM usuarios JOIN roles ON 
                        usuarios.rol_idrol = roles.idrol where usuarios.estados_idestados = 5;";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error getAllUsuarios: " . $e->getMessage());
            return [];
        }
    }

    public function traerRoles()
    {
        try {
            // Asumiendo que la tabla de roles se llama 'roles' y tiene campos como idrol, nombre_rol
            $sql = "SELECT idrol, nombre_rol FROM roles";
            return $this->mysql->efectuarConsulta($sql);
        } catch (Exception $e) {
            error_log("Error traerRoles: " . $e->getMessage());
            return [];
        }
    }

    public function insertarUsuarios($nombre_usuario,$contrasena_usuario,$email_usuario,$rol_idrol)
    {
        try {
            //code...
            $sql = "insert into usuarios(nombre_usuario,contrasena_usuario,email_usuario,estados_idestados,rol_idrol)
            values (?,?,?,?,?)";
            $parametros = [$nombre_usuario, $contrasena_usuario, $email_usuario, 5, $rol_idrol];
            $stmt = $this->mysql->ejecutarSentenciaPreparada($sql, "sssii", $parametros);
            if ($stmt->rowCount() > 0) {
                return true; // Usuario insertado correctamente
            } else {
                return false; // No se insertó ningún usuario
            }
        } catch (\Throwable $th) {
            //throw $th;
            error_log("Error insertarUsuarios: " . $th->getMessage());
            return false;
        }
    }

    public function eliminarUsuario($id)
    {
        try{
            $sql = "update usuarios set estados_idestados = ? where idusuarios = ?";
            $parametros = [2,$id];
            $stm = $this->mysql->ejecutarSentenciaPreparada($sql, "ii", $parametros);
            if($stm->rowCount() > 0) 
            {
                return true;
            }
            else{
                return false;
            }
        }
        catch (Exception $e) {
            error_log("Error Eliminar: " . $e->getMessage());
            return [];
        }
    }

    public function editarUsuario($id,$nombre,$email,$rol)
    {
        try {
            $sql = "update usuarios set nombre_usuario = ?, email_usuario = ?, rol_idrol = ? where idusuarios = ?";
            $parametros = [$nombre,$email,$rol,$id];
            $stm = $this->mysql->ejecutarSentenciaPreparada($sql,'ssii', $parametros);  
            if($stm->rowCount() > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error Editar: " . $e->getMessage());
            return [];
        }
    }
}

?>
