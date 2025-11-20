<?php
require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../models/Instructor.php';

class AuthController extends Controller
{
    public function loginForm()
    {
        $this->render('login');
    }

    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Primero intentar miembro
        if ($email) {
            $member = Member::findByEmail($email);
            if ($member && password_verify($password, $member['contraseña'])) {
                $_SESSION['user'] = ['id' => $member['id'], 'username' => $member['nombre'], 'role' => 'member', 'email' => $member['correo']];
                $this->redirect('?route=member/dashboard');
            }

            $instructor = Instructor::findByEmail($email);
            if ($instructor && password_verify($password, $instructor['contraseña'])) {
                $_SESSION['user'] = ['id' => $instructor['id'], 'username' => $instructor['nombre'], 'role' => 'instructor', 'email' => $instructor['correo']];
                $this->redirect('?route=instructor/dashboard');
            }
        }

        // Usuario admin de ejemplo (no está en la BD) - cambiar en producción
        if ($email === 'admin' && $password === 'admin') {
            $_SESSION['user'] = ['id' => 0, 'username' => 'admin', 'role' => 'admin', 'email' => 'admin'];
            $this->redirect('?route=admin/dashboard');
        }

        $this->render('login', ['error' => 'Credenciales inválidas']);
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        $this->redirect('?route=home');
    }
}
