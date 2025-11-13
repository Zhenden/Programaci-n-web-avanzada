<?php
// models/Libro.php
require_once __DIR__ . '/../BD/conexion.php';

class Libro {
    private $conn;
    private $table = 'libros';

    public function __construct() {
        $this->conn = conectar();
    }

    /**
     * Obtener todos los libros
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtener un libro por su ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Agregar un nuevo libro
     */
    public function agregar($titulo, $autor, $isbn, $descripcion, $total_copias, $disponible) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (titulo, autor, isbn, descripcion, total_copias, disponible) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssii', $titulo, $autor, $isbn, $descripcion, $total_copias, $disponible);
        return $stmt->execute();
    }

    /**
     * Actualizar un libro
     */
    public function actualizar($id, $titulo, $autor, $isbn, $descripcion, $total_copias, $disponible) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET titulo=?, autor=?, isbn=?, descripcion=?, total_copias=?, disponible=? WHERE id=?");
        $stmt->bind_param('ssssiis', $titulo, $autor, $isbn, $descripcion, $total_copias, $disponible, $id);
        return $stmt->execute();
    }

    public function editar($id, $titulo, $autor, $disponible) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET titulo = ?, autor = ?, disponible = ? WHERE id = ?");
        $stmt->bind_param('ssii', $titulo, $autor, $disponible, $id);
        return $stmt->execute();
    }

    /**
     * Eliminar un libro
     */
    public function eliminar($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
