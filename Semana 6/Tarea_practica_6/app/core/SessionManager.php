<?php
/**
 * SessionManager - Gestor centralizado de sesiones
 * Previene múltiples session_start() y centraliza el manejo de sesiones
 */
class SessionManager {
    private static $started = false;
    
    /**
     * Inicia la sesión si no está iniciada
     */
    public static function start() {
        if (!self::$started && session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                session_start();
                self::$started = true;
            }
        }
    }
    
    /**
     * Obtiene un valor de sesión
     */
    public static function get($key, $default = null) {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }
    
    /**
     * Establece un valor en sesión
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Verifica si existe una clave en sesión
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Elimina un valor de sesión
     */
    public static function remove($key) {
        self::start();
        unset($_SESSION[$key]);
    }
    
    /**
     * Destruye la sesión completamente
     */
    public static function destroy() {
        self::start();
        session_unset();
        session_destroy();
        self::$started = false;
    }
    
    /**
     * Obtiene el ID de usuario actual
     */
    public static function getUserId() {
        return self::get('user_id');
    }
    
    /**
     * Obtiene el rol del usuario actual
     */
    public static function getRole() {
        return self::get('role_name');
    }
    
    /**
     * Verifica si el usuario está autenticado
     */
    public static function isAuthenticated() {
        return self::has('user_id');
    }
}