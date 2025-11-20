<?php
class BaseController {
    
    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    protected function debug($message, $data = null) {
    $log = "[DEBUG " . date('Y-m-d H:i:s') . "] " . $message;

    if ($data !== null) {
        $log .= " → " . print_r($data, true);
    }

    error_log($log);
}

    
    /**
     * Check user role
     */
    protected function checkRole($roles) {
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $userRole = $_SESSION['user_role'] ?? '';
        
        if (is_array($roles)) {
            if (!in_array($userRole, $roles)) {
                die('Acceso denegado. Se requiere rol: ' . implode(', ', $roles));
            }
        } else {
            if ($userRole !== $roles) {
                die('Acceso denegado. Se requiere rol: ' . $roles);
            }
        }
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
    
    /**
     * Render view
     */
    protected function render($view, $data = []) {
        $path = "views/$view.php";

        if (!file_exists($path)) {
            error_log("[RENDER ERROR] No se encontró la vista: $path");
            die("ERROR: La vista <b>$path</b> no existe.");
        }

        extract($data);
        require_once $path;
    }

    
    /**
     * Get current user ID
     */
    protected function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user role
     */
    protected function getCurrentUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
}