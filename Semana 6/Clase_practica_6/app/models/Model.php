<?php
class Model {
    protected $conn;
    
    public function __construct() {
        $this->conn = $this->getConnection();
    }
    
    protected function getConnection() {
        $conn = new mysqli("localhost", "root", "", "reservas_hotel");
        
        if ($conn->connect_error) {
            throw new Exception("Error de conexión: " . $conn->connect_error);
        }
        
        $conn->set_charset('utf8mb4');
        return $conn;
    }
    
    /**
     * Check if a column exists in a table within the current database
     */
    protected function columnExists($table, $column) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
        if (!$stmt) return false;
        $stmt->bind_param('ss', $table, $column);
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        // Use bind_result to avoid get_result() and commands-out-of-sync issues
        $stmt->bind_result($cnt);
        $stmt->store_result();
        $found = false;
        if ($stmt->fetch()) {
            $found = ($cnt > 0);
        }
        $stmt->close();
        return $found;
    }
}