<?php
// models/Usuario.php
require_once __DIR__ . '/../BD/conexion.php';

class Usuario {
    private $conn;
    private $table = 'usuarios';

    public function __construct() {
        $this->conn = conectar(); // función definida en BD/conexion.php
    }

    /**
     * Obtener todos los usuarios (para el administrador)
     */
    public function obtenerTodos() {
        $sql = "SELECT id, nombre, email, rol_id FROM {$this->table} ORDER BY id ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Obtener usuario por email (para login)
     */
    public function obtenerPorEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Crear un nuevo usuario
     */
    public function crear($nombre, $email, $password, $rol_id) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (nombre, email, password, rol_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $nombre, $email, $password, $rol_id);
        return $stmt->execute();
    }

    /**
     * Eliminar usuario por ID
     */
    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
