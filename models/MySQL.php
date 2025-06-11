<?php
//clase para gestionar la conexion a la base de datos 

class MySql
{
    //datos de conexion

    private $ipServidor = "localhost";
    private $usuarioBase = "root";
    private $contrasena = "BD12345";
    private $nombreBaseDatos = "bd_cafe";

    private $conexion;

    public function getConexion()
    {
    return $this->conexion;
    }


    //metodo para conectar a la base de datos
    public function conectar()
    {
        $this->conexion = mysqli_connect(
            $this->ipServidor,
            $this->usuarioBase,
            $this->contrasena,
            $this->nombreBaseDatos
        );

        //validar si hubo un error en la conexion
        if (!$this->conexion) {
            die("Error al conectar a la base de datos: " . mysqli_connect_error());
        }

        //Establecer codificacion utf8
        mysqli_set_charset($this->conexion, "utf8");
    }

    //metodo para desconectar la base de datos
    public function desconectar()
    {
        if ($this->conexion) {
            mysqli_close($this->conexion);
        }
    }


    //metodo para ejecutar una consulta y devolver su resultado
    public function efectuarConsulta($consulta)
    {
        //Verificar que la codificacion sea utf8 antes de ejecutar
        mysqli_query($this->conexion, "SET NAMES 'utf8'");
        mysqli_query($this->conexion, "SET CHARACTER SET 'utf8'");

        $resultado = mysqli_query($this->conexion, $consulta);

        if (!$resultado) {
            $codigoError = mysqli_errno($this->conexion);
            $mensajeError = mysqli_error($this->conexion);
            throw new Exception("Error en la consulta: $mensajeError", $codigoError);
        }

        return $resultado;
    }
}
