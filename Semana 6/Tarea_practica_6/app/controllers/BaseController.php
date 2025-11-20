<?php
require_once __DIR__ . '/../autoload.php';

class BaseController {
    protected $db = null;
    
    /**
     * Obtiene la conexión a BD (singleton por request)
     */
    protected function getDB() {
        if ($this->db === null) {
            require_once __DIR__ . '/../../config/database.php';
            $this->db = conectar();
        }
        return $this->db;
    }
    
    /**
     * Cierra la conexión a BD
     */
    protected function closeDB() {
        if ($this->db !== null) {
            $this->db->close();
            $this->db = null;
        }
    }
    protected function view($path, $data = []) {
        extract($data);
        require __DIR__ . '/../../views/layouts/header.php';
        require __DIR__ . '/../../views/' . $path . '.php';
        require __DIR__ . '/../../views/layouts/footer.php';
    }
    
    protected function redirect($url) {
        $this->closeDB(); // Cerrar BD antes de redirigir
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Redirige con parámetros de URL y ancla opcional
     * @param string $baseUrl URL base
     * @param array $params Parámetros para agregar a la URL
     * @param string|null $anchor Ancla opcional
     */
    protected function redirectWithParams($baseUrl, $params = [], $anchor = null) {
        // Debug: log the input parameters
        error_log("DEBUG redirectWithParams: baseUrl=$baseUrl, params=" . json_encode($params) . ", anchor=$anchor");
        
        // Construir query string
        $queryString = '';
        if (!empty($params)) {
            // Separar la URL base de los parámetros existentes
            $urlParts = parse_url($baseUrl);
            $existingParams = [];
            if (isset($urlParts['query'])) {
                parse_str($urlParts['query'], $existingParams);
            }
            
            // Combinar parámetros existentes con nuevos
            $allParams = array_merge($existingParams, $params);
            
            // Reconstruir la URL base sin query string
            $cleanUrl = (isset($urlParts['scheme']) ? $urlParts['scheme'] . '://' : '') .
                       (isset($urlParts['host']) ? $urlParts['host'] : '') .
                       (isset($urlParts['path']) ? $urlParts['path'] : '');
            
            $queryString = '?' . http_build_query($allParams);
            $baseUrl = $cleanUrl . $queryString;
        }
        
        // Agregar ancla si se proporciona
        if ($anchor) {
            $baseUrl .= '#' . $anchor;
        }
        
        error_log("DEBUG redirectWithParams: final URL=$baseUrl");
        $this->redirect($baseUrl);
    }
    
    protected function authRequired(){
        if(!SessionManager::isAuthenticated()){
            $this->redirect('index.php?action=login');
        }
    }
    
    protected function checkRole(array $allowed){
        $userRole = SessionManager::getRole();
        if(!$userRole || !in_array($userRole, $allowed)){
            die('No autorizado');
        }
    }
    
    /**
     * Ejecuta una consulta preparada con manejo de errores mejorado
     */
    protected function executePrepared($sql, $types, $params) {
        try {
            $conn = $this->getDB();
            
            // Limpiar resultados pendientes para evitar "Commands out of sync"
            while ($conn->more_results() && $conn->next_result()) {
                if ($result = $conn->store_result()) {
                    $result->free();
                }
            }
            
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $conn->error);
            }
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando consulta: " . $stmt->error);
            }
            
            return $stmt;
            
        } catch (Exception $e) {
            error_log("Error en executePrepared: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Genera un token CSRF
     */
    protected function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valida un token CSRF
     */
    protected function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}