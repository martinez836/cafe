<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'bd_cafe');
define('DB_USER', 'root');  
define('DB_PASS', '');   

class config {
    public static function conectar() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }
}
?>
