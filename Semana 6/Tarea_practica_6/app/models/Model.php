<?php
/**
 * Model base con manejo de errores mejorado
 */
abstract class Model {
    protected $conn;
    
    public function __construct() {
        $this->conn = $this->getConnection();
    }
    
    /**
     * Obtiene conexión a base de datos con manejo de errores
     */
    protected function getConnection() {
        $conn = new mysqli("localhost", "root", "", "restaurante");
        
        if ($conn->connect_error) {
            throw new Exception("Error de conexión: " . $conn->connect_error);
        }
        
        $conn->set_charset('utf8mb4');
        return $conn;
    }
    
    /**
     * Ejecuta una consulta preparada con manejo de errores
     */
    protected function executePrepared($sql, $types = '', $params = []) {
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $this->conn->error);
        }
        
        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando consulta: " . $stmt->error);
        }
        
        return $stmt;
    }
    
    /**
     * Obtiene el último ID insertado
     */
    protected function getLastInsertId() {
        return $this->conn->insert_id;
    }
    
    /**
     * Cierra la conexión
     */
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}