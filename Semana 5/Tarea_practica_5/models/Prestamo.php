<?php
// models/Prestamo.php
require_once __DIR__ . '/../BD/conexion.php';

class Prestamo {

    
    private $conn;
    private $table = 'prestamos';

    public function __construct() {
        $this->conn = conectar();
    }

    /**
     * Registrar un nuevo préstamo
     */
    public function registrar($libro_id, $usuario_id) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (libro_id, usuario_id, fecha_prestamo, estado) VALUES (?, ?, NOW(), 'prestado')");
        $stmt->bind_param('ii', $libro_id, $usuario_id);
        return $stmt->execute();
    }

    /**
     * Marcar préstamo como devuelto
     */
    public function marcarDevuelto($id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET estado = 'devuelto', fecha_devolucion = NOW() WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    /**
     * Listar préstamos según rol:
     * - Lector: solo los suyos
     * - Bibliotecario/Admin: todos
     */
    public function listarPorUsuario($usuario_id, $rol_id) {
        if ($rol_id === '1' || $rol_id === '2') {
            $sql = "SELECT p.*, l.titulo FROM {$this->table} p 
                    JOIN libros l ON p.libro_id = l.id 
                    ORDER BY p.fecha_prestamo DESC";
            $result = $this->conn->query($sql);
        } else {
            $stmt = $this->conn->prepare("SELECT p.*, l.titulo FROM {$this->table} p 
                                          JOIN libros l ON p.libro_id = l.id 
                                          WHERE p.usuario_id = ? 
                                          ORDER BY p.fecha_prestamo DESC");
            $stmt->bind_param('i', $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtener préstamo por ID
     */
    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function usuarioTieneLibroPrestado($usuario_id, $libro_id) {
    return $this->prestamoModel->usuarioTieneLibroPrestado($usuario_id, $libro_id);
}
}
