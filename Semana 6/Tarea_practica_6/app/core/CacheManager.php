<?php
/**
 * CacheManager - Sistema de caché para mejorar el rendimiento y manejar errores de caché
 */
class CacheManager {
    private static $instance = null;
    private $cacheDir;
    private $cacheEnabled;
    private $defaultTTL = 3600; // 1 hora por defecto
    
    private function __construct() {
        $this->cacheDir = __DIR__ . '/../../cache/';
        $this->cacheEnabled = true;
        $this->ensureCacheDirectory();
    }
    
    /**
     * Obtiene la instancia singleton
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Asegura que el directorio de caché existe y tiene permisos
     */
    private function ensureCacheDirectory() {
        if (!is_dir($this->cacheDir)) {
            if (!mkdir($this->cacheDir, 0777, true)) {
                $this->logError("No se pudo crear el directorio de caché: " . $this->cacheDir);
                $this->cacheEnabled = false;
            }
        }
        
        // Verificar permisos de escritura
        if (!is_writable($this->cacheDir)) {
            if (!chmod($this->cacheDir, 0777)) {
                $this->logError("No se pueden establecer permisos de escritura en: " . $this->cacheDir);
                $this->cacheEnabled = false;
            }
        }
    }
    
    /**
     * Obtiene un valor del caché
     */
    public function get($key) {
        if (!$this->cacheEnabled) {
            return null;
        }
        
        try {
            $filename = $this->getCacheFilename($key);
            
            if (!file_exists($filename)) {
                $this->logDebug("Caché miss para clave: $key");
                return null;
            }
            
            $data = file_get_contents($filename);
            if ($data === false) {
                $this->logError("Error leyendo archivo de caché: $filename");
                return null;
            }
            
            $cached = unserialize($data);
            if ($cached === false) {
                $this->logError("Error deserializando datos del caché: $key");
                return null;
            }
            
            // Verificar expiración
            if (time() > $cached['expires']) {
                $this->logDebug("Caché expirado para clave: $key");
                $this->delete($key);
                return null;
            }
            
            $this->logDebug("Caché hit para clave: $key");
            return $cached['data'];
            
        } catch (Exception $e) {
            $this->logError("Excepción en get(): " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Guarda un valor en el caché
     */
    public function set($key, $data, $ttl = null) {
        if (!$this->cacheEnabled) {
            return false;
        }
        
        try {
            $ttl = $ttl ?? $this->defaultTTL;
            $cached = [
                'data' => $data,
                'expires' => time() + $ttl,
                'created' => time(),
                'key' => $key
            ];
            
            $filename = $this->getCacheFilename($key);
            $serialized = serialize($cached);
            
            // Verificar capacidad de almacenamiento antes de escribir
            $free = @disk_free_space($this->cacheDir);
            if ($free !== false && $free < (strlen($serialized) + 1024)) {
                $this->logError("Espacio insuficiente en el directorio de caché: disponible {$free} bytes");
                return false;
            }
            
            if (file_put_contents($filename, $serialized, LOCK_EX) === false) {
                $this->logError("Error escribiendo archivo de caché: $filename");
                return false;
            }
            
            $this->logDebug("Datos guardados en caché para clave: $key (TTL: $ttl)");
            return true;
            
        } catch (Exception $e) {
            $this->logError("Excepción en set(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un valor del caché
     */
    public function delete($key) {
        if (!$this->cacheEnabled) {
            return false;
        }
        
        try {
            $filename = $this->getCacheFilename($key);
            
            if (file_exists($filename)) {
                if (unlink($filename)) {
                    $this->logDebug("Caché eliminado para clave: $key");
                    return true;
                } else {
                    $this->logError("Error eliminando archivo de caché: $filename");
                    return false;
                }
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->logError("Excepción en delete(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Limpia todo el caché
     */
    public function clear() {
        if (!$this->cacheEnabled) {
            return false;
        }
        
        try {
            $files = glob($this->cacheDir . '*.cache');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            
            $this->logInfo("Caché completamente limpiado");
            return true;
            
        } catch (Exception $e) {
            $this->logError("Excepción en clear(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene el nombre de archivo para una clave de caché
     */
    private function getCacheFilename($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }
    
    /**
     * Registra errores del caché
     */
    private function logError($message) {
        error_log("[CacheManager ERROR] " . $message);
    }
    
    /**
     * Registra información del caché
     */
    private function logInfo($message) {
        error_log("[CacheManager INFO] " . $message);
    }
    
    /**
     * Registra mensajes de debug del caché
     */
    private function logDebug($message) {
        if (defined('DEBUG_MODE') && constant('DEBUG_MODE')) {
            error_log("[CacheManager DEBUG] " . $message);
        }
    }
    
    /**
     * Verifica si el caché está habilitado
     */
    public function isEnabled() {
        return $this->cacheEnabled;
    }
    
    /**
     * Obtiene estadísticas del caché
     */
    public function getStats() {
        if (!$this->cacheEnabled) {
            return ['enabled' => false];
        }
        
        try {
            $files = glob($this->cacheDir . '*.cache');
            $totalFiles = count($files);
            $totalSize = 0;
            
            foreach ($files as $file) {
                $totalSize += filesize($file);
            }
            
            return [
                'enabled' => true,
                'total_files' => $totalFiles,
                'total_size_bytes' => $totalSize,
                'total_size_mb' => round($totalSize / 1024 / 1024, 2),
                'cache_dir' => $this->cacheDir
            ];
            
        } catch (Exception $e) {
            $this->logError("Excepción en getStats(): " . $e->getMessage());
            return ['enabled' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Elimina entradas de caché cuyo key comience con un prefijo
     */
    public function deleteByPrefix($prefix) {
        if (!$this->cacheEnabled) {
            return false;
        }
        
        try {
            $files = glob($this->cacheDir . '*.cache');
            $deleted = 0;
            foreach ($files as $file) {
                $data = @file_get_contents($file);
                if ($data === false) {
                    continue;
                }
                $cached = @unserialize($data);
                if (!is_array($cached) || !isset($cached['key'])) {
                    continue;
                }
                if (strpos($cached['key'], $prefix) === 0) {
                    @unlink($file);
                    $deleted++;
                }
            }
            $this->logInfo("Caché invalidado por prefijo '{$prefix}': {$deleted} entradas eliminadas");
            return true;
        } catch (Exception $e) {
            $this->logError("Excepción en deleteByPrefix(): " . $e->getMessage());
            return false;
        }
    }
}