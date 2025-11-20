<?php
require_once 'BaseController.php';

class AuthController extends BaseController {
    
    public function showLogin() {
        // If user is logged in, redirect to dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('index.php?action=dashboard');
            return;
        }
        
        $this->render('auth/login');
    }
    
    public function showRegister() {
        // If user is logged in, redirect to dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('index.php?action=dashboard');
            return;
        }
        
        $userModel = new User();
        $roles = $userModel->getRoles();
        
        $this->render('auth/register', ['roles' => $roles]);
    }
    
    public function login() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $this->debug('Login attempt', ['email' => $email]);
        
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Por favor complete todos los campos';
            $this->redirect('index.php?action=login');
            return;
        }
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Credenciales inválidas';
            $this->redirect('index.php?action=login');
            return;
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role_name'];
        $_SESSION['role_id'] = $user['role_id'];
        
        $this->redirect('index.php?action=dashboard');
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=register');
            return;
        }
        
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $roleId = $_POST['role_id'] ?? 4; // Default to Customer
        
        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Por favor complete todos los campos';
            $this->redirect('index.php?action=register');
            return;
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email inválido';
            $this->redirect('index.php?action=register');
            return;
        }
        
        $userModel = new User();
        
        // Check if email exists
        if ($userModel->findByEmail($email)) {
            $_SESSION['error'] = 'El email ya está registrado';
            $this->redirect('index.php?action=register');
            return;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Create user
        if ($userModel->create($name, $email, $hashedPassword, $roleId)) {
            $_SESSION['success'] = 'Usuario registrado exitosamente. Por favor inicie sesión.';
            $this->redirect('index.php?action=login');
        } else {
            $_SESSION['error'] = 'Error al registrar usuario';
            $this->redirect('index.php?action=register');
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('index.php?action=login');
    }
}