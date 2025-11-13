<?php
// controllers/AuthController.php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    public function login($email, $password) {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorEmail($email);

        if ($usuario && password_verify($password, $usuario['password'])) {
            session_start();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['rol_id'] = $usuario['rol_id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_start();
        session_destroy();
    }
}

?>
