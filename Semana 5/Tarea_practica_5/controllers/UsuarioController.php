<?php
// controllers/UsuarioController.php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Listar todos los usuarios (solo para administrador)
     */
    public function listar() {
        return $this->usuarioModel->obtenerTodos();
    }

    /**
     * Ver información de un usuario específico
     */
    public function ver($id) {
        return $this->usuarioModel->obtenerPorId($id);
    }

    /**
     * Crear un nuevo usuario
     */
    public function crear($nombre, $email, $password, $rol_id) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $this->usuarioModel->crear($nombre, $email, $hash, $rol_id);
    }

    /**
     * Eliminar un usuario
     */
    public function eliminar($id) {
        return $this->usuarioModel->eliminar($id);
    }

    /**
     * Autenticar usuario (login)
     */
    public function autenticar($email, $password) {
        $usuario = $this->usuarioModel->obtenerPorEmail($email);

        if ($usuario && password_verify($password, $usuario['password'])) {
            session_start();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre']     = $usuario['nombre'];
            $_SESSION['rol_id']     = $usuario['rol_id'];
            return true;
        }
        return false;
    }

    /**
     * Cerrar sesión
     */
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /Tarea_practica_5/index.php?action=login');
        exit;
    }
}
