<?php
// controllers/PrestamoController.php
require_once __DIR__ . '/../models/Prestamo.php';

class PrestamoController {
    private $prestamoModel;

    public function __construct() {
        $this->prestamoModel = new Prestamo();
    }

    /**
     * Registrar un préstamo
     */
    public function registrarPrestamo($libro_id, $usuario_id) {
        return $this->prestamoModel->registrar($libro_id, $usuario_id);
    }

    /**
     * Marcar préstamo como devuelto
     */
    public function marcarDevuelto($id) {
        return $this->prestamoModel->marcarDevuelto($id);
    }

    /**
     * Listar préstamos según el rol del usuario
     * - lector → solo los suyos
     * - bibliotecario/admin → todos
     */
    public function listarPorUsuario($usuario_id, $rol) {
        return $this->prestamoModel->listarPorUsuario($usuario_id, $rol);
    }

    /**
     * Obtener un préstamo por ID
     */
    public function ver($id) {
        return $this->prestamoModel->obtenerPorId($id);
    }
}
