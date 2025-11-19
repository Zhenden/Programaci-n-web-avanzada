<?php
// controllers/LibroController.php
require_once __DIR__ . '/../models/Libro.php';

class LibroController {
    private $libroModel;

    public function __construct() {
        $this->libroModel = new Libro();
    }

    /**
     * Listar todos los libros
     */
    public function listar() {
        return $this->libroModel->obtenerTodos();
    }

    /**
     * Ver detalles de un libro
     */
    public function ver($id) {
        return $this->libroModel->obtenerPorId($id);
    }

    /**
     * Agregar un nuevo libro
     */
    public function agregar($titulo, $autor, $total_copias) {
        $disponible = $total_copias; // Al agregar, todas las copias están disponibles
        return $this->libroModel->agregar($titulo, $autor, $total_copias, $disponible);
    }

    /**
     * Actualizar información de un libro
     */
    public function actualizar($id, $titulo, $autor, $total_copias, $disponible) {
        return $this->libroModel->actualizar($id, $titulo, $autor, $total_copias, $disponible);
    }

        public function editar($id, $titulo, $autor, $disponible)
    {
        // forzamos tipos mínimos
        $id = (int) $id;
        $disponible = (int) $disponible;
        return $this->libroModel->editar($id, $titulo, $autor, $disponible);
    }


    /**
     * Eliminar un libro
     */
    public function eliminar($id) {
        return $this->libroModel->eliminar($id);
    }
}
?>