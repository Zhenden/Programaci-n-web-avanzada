<?php
// config/database.php

class Database {
    private $host = "localhost";
    private $db_name = "Tarea_equipos";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            
            if ($this->conn->connect_error) {
                throw new Exception("Error de conexión: " . $this->conn->connect_error);
            }
            
            $this->conn->set_charset("utf8");
            
        } catch (Exception $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

// Función helper para conexión
function conectar() {
    $database = new Database();
    return $database->getConnection();
}
?>