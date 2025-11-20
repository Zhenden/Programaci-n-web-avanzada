<?php
 require_once 'Model.php';
 class Role extends Model {
 
    /**
     * Obtiene todos los roles
     */
    public function all() {
        $result = $this->conn->query("SELECT * FROM roles ORDER BY name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Obtiene un rol por ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM roles WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Obtiene un rol por nombre
     */
    public function getByName($name) {
        $stmt = $this->conn->prepare("SELECT * FROM roles WHERE name=?");
        $stmt->bind_param('s', $name);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
 }