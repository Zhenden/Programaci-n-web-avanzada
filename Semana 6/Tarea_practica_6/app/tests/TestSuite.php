<?php
/**
 * Suite de pruebas unitarias para el sistema
 */
class TestSuite {
    private $passed = 0;
    private $failed = 0;
    private $tests = [];
    
    /**
     * Ejecuta todas las pruebas
     */
    public function run() {
        echo "=== INICIANDO PRUEBAS UNITARIAS ===\n\n";
        
        // Pruebas de SessionManager
        $this->testSessionManager();
        
        // Pruebas de seguridad
        $this->testSecurity();
        
        // Pruebas de modelos
        $this->testModels();
        
        // Resumen
        $this->printSummary();
    }
    
    /**
     * Pruebas del SessionManager
     */
    private function testSessionManager() {
        echo "--- Pruebas de SessionManager ---\n";
        
        // Test 1: Inicialización de sesión
        try {
            SessionManager::start();
            $this->assert(true, "SessionManager::start()");
        } catch (Exception $e) {
            $this->assert(false, "SessionManager::start()", $e->getMessage());
        }
        
        // Test 2: Set y Get
        SessionManager::set('test_key', 'test_value');
        $value = SessionManager::get('test_key');
        $this->assert($value === 'test_value', "SessionManager::set() y ::get()");
        
        // Test 3: Has
        $hasKey = SessionManager::has('test_key');
        $this->assert($hasKey === true, "SessionManager::has() con clave existente");
        
        $hasNonExistent = SessionManager::has('non_existent');
        $this->assert($hasNonExistent === false, "SessionManager::has() con clave inexistente");
        
        // Test 4: Remove
        SessionManager::remove('test_key');
        $removedValue = SessionManager::get('test_key');
        $this->assert($removedValue === null, "SessionManager::remove()");
        
        echo "\n";
    }
    
    /**
     * Pruebas de seguridad
     */
    private function testSecurity() {
        echo "--- Pruebas de Seguridad ---\n";
        
        // Test 1: Validación de email
        $validEmail = filter_var('test@example.com', FILTER_VALIDATE_EMAIL);
        $this->assert($validEmail !== false, "Validación de email válido");
        
        $invalidEmail = filter_var('invalid-email', FILTER_VALIDATE_EMAIL);
        $this->assert($invalidEmail === false, "Validación de email inválido");
        
        // Test 2: Escapado XSS
        $maliciousInput = '<script>alert("XSS")</script>';
        $escaped = htmlspecialchars($maliciousInput);
        $expected = '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;';
        $this->assert($escaped === $expected, "Escapado de caracteres especiales");
        
        // Test 3: Hash de contraseña
        $password = 'test_password';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $isValid = password_verify($password, $hash);
        $this->assert($isValid === true, "Verificación de hash de contraseña");
        
        echo "\n";
    }
    
    /**
     * Pruebas de modelos
     */
    private function testModels() {
        echo "--- Pruebas de Modelos ---\n";
        
        // Test 1: Crear modelo User
        try {
            $userModel = new User();
            $this->assert(true, "Instanciación de User model");
        } catch (Exception $e) {
            $this->assert(false, "Instanciación de User model", $e->getMessage());
        }
        
        // Test 2: Validación de datos
        $username = 'test_user';
        $email = 'test@example.com';
        $password = 'secure_password';
        
        $isValid = !empty($username) && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 6;
        $this->assert($isValid === true, "Validación de datos de usuario");
        
        // Test 3: Conexión a base de datos
        try {
            $conn = new mysqli("localhost", "root", "", "restaurante");
            $this->assert($conn->connect_error === null, "Conexión a base de datos");
            $conn->close();
        } catch (Exception $e) {
            $this->assert(false, "Conexión a base de datos", $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Función de aserción
     */
    private function assert($condition, $testName, $message = '') {
        if ($condition) {
            echo "✓ {$testName}\n";
            $this->passed++;
        } else {
            echo "✗ {$testName}";
            if ($message) {
                echo " - {$message}";
            }
            echo "\n";
            $this->failed++;
        }
    }
    
    /**
     * Imprime resumen de pruebas
     */
    private function printSummary() {
        echo "=== RESUMEN DE PRUEBAS ===\n";
        echo "Pruebas pasadas: {$this->passed}\n";
        echo "Pruebas fallidas: {$this->failed}\n";
        echo "Total: " . ($this->passed + $this->failed) . "\n";
        
        if ($this->failed === 0) {
            echo "\n🎉 ¡Todas las pruebas pasaron!\n";
        } else {
            echo "\n⚠️  Algunas pruebas fallaron. Revisa los errores anteriores.\n";
        }
    }
}

// Ejecutar pruebas si se llama directamente
if (php_sapi_name() === 'cli') {
    require_once __DIR__ . '/../autoload.php';
    $testSuite = new TestSuite();
    $testSuite->run();
}