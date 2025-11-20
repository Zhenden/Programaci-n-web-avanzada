<?php
class AuthController extends BaseController {
    public function showLogin(){
        // If user is already logged in, redirect to dashboard
        if(SessionManager::get('user_id')){
            $this->redirect('index.php?action=dashboard');
            return;
        }
        $this->view('auth/login');
    }
    
    public function login(){
        $userModel = new User();
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        if (!$email) {
            SessionManager::set('error', 'Email inválido');
            $this->redirect('index.php?action=login');
            return;
        }
        
        $user = $userModel->findByEmail($email);
        
        if($user && password_verify($password, $user['password'])){
            SessionManager::set('user_id', $user['id']);
            SessionManager::set('username', $user['username']);
            SessionManager::set('role_name', $user['role_name']);
            $this->redirect('index.php?action=dashboard');
        } else {
            SessionManager::set('error', 'Credenciales inválidas');
            $this->redirect('index.php?action=login');
        }
    }
    
    public function logout(){
        SessionManager::destroy();
        $this->redirect('index.php?action=login');
    }
    
    public function showRegister(){
        // If user is already logged in, redirect to dashboard
        if(SessionManager::get('user_id')){
            $this->redirect('index.php?action=dashboard');
            return;
        }
        $this->view('auth/register');
    }
    
    public function register(){
        $userModel = new User();
        
        // Validación básica
        $username = trim($_POST['username'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || !$email || strlen($password) < 6) {
            SessionManager::set('error', 'Datos inválidos. La contraseña debe tener al menos 6 caracteres.');
            $this->redirect('index.php?action=register');
            return;
        }
        
        $userId = $userModel->create($username, $email, $password);
        
        if ($userId) {
            SessionManager::set('success', 'Usuario creado exitosamente');
            $this->redirect('index.php?action=login');
        } else {
            SessionManager::set('error', 'Error al crear usuario');
            $this->redirect('index.php?action=register');
        }
    }
}