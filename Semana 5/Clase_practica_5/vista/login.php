<?php
// login.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Incluir configuración de base de datos
require_once 'BD/conexion.php';

// Verificar si ya está logueado
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Procesar formulario de login
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validaciones básicas
    if (empty($email) || empty($password)) {
        $error = 'Por favor completa todos los campos';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor ingresa un email válido';
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();

            // Buscar usuario por email
            $query = "SELECT id, nombre, email, contraseña, rol_id FROM usuarios WHERE email = ? LIMIT 1";
            $stmt = $db->prepare($query);
            
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    
                    // Verificar contraseña
                    if (password_verify($password, $user['contraseña'])) {
                        // Login exitoso
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['nombre'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['rol_id'];
                        
                        $success = 'Login exitoso! Redirigiendo...';
                        
                        // Redirigir después de 1 segundo
                        header("Refresh: 1; location: dashboard.php");
                    } else {
                        $error = 'Contraseña incorrecta';
                    }
                } else {
                    $error = 'Usuario no encontrado';
                }
                
                $stmt->close();
            } else {
                $error = 'Error en la consulta de base de datos';
            }
            
            $db->close();
            
        } catch (Exception $e) {
            $error = 'Error del sistema: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Tareas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .login-form {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control.error {
            border-color: #e74c3c;
            background-color: #fdf2f2;
        }

        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #fdf2f2;
            border: 1px solid #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background-color: #f0f9ff;
            border: 1px solid #b3e0ff;
            color: #004085;
        }

        .login-footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #e1e5e9;
            background-color: #f8f9fa;
        }

        .login-footer p {
            color: #6c757d;
            font-size: 14px;
        }

        .password-toggle {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            font-size: 14px;
        }

        .toggle-password:hover {
            color: #333;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            
            .login-form {
                padding: 20px;
            }
            
            .login-header {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>📋 Sistema de Tareas</h1>
            <p>Inicia sesión para gestionar tus tareas</p>
        </div>

        <form method="POST" action="index.php?action=login" class="login-form" id="loginForm">
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="usuario@ejemplo.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required
                >
                <div class="error-message" id="emailError">Por favor ingresa un email válido</div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-toggle">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Ingresa tu contraseña"
                        required
                    >
                    <button type="button" class="toggle-password" id="togglePassword">👁️</button>
                </div>
                <div class="error-message" id="passwordError">La contraseña es requerida</div>
            </div>

            <button type="submit" class="btn btn-primary" id="loginBtn">
                <span id="btnText">Iniciar Sesión</span>
            </button>
        </form>

        <div class="login-footer">
            <p>¿Necesitas una cuenta? Contacta al administrador</p>
        </div>
    </div>

    <script>
        class LoginApp {
            constructor() {
                this.form = document.getElementById('loginForm');
                this.emailInput = document.getElementById('email');
                this.passwordInput = document.getElementById('password');
                this.togglePasswordBtn = document.getElementById('togglePassword');
                this.loginBtn = document.getElementById('loginBtn');
                this.btnText = document.getElementById('btnText');
                this.emailError = document.getElementById('emailError');
                this.passwordError = document.getElementById('passwordError');

                this.initEvents();
            }

            initEvents() {
                this.form.addEventListener('submit', (e) => this.handleSubmit(e));
                this.togglePasswordBtn.addEventListener('click', () => this.togglePassword());
                
                // Validación en tiempo real
                this.emailInput.addEventListener('blur', () => this.validateEmail());
                this.passwordInput.addEventListener('blur', () => this.validatePassword());
                
                // Limpiar errores al escribir
                this.emailInput.addEventListener('input', () => this.clearError(this.emailInput, this.emailError));
                this.passwordInput.addEventListener('input', () => this.clearError(this.passwordInput, this.passwordError));
            }

            togglePassword() {
                const type = this.passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                this.passwordInput.setAttribute('type', type);
                this.togglePasswordBtn.textContent = type === 'password' ? '👁️' : '🔒';
            }

            validateEmail() {
                const email = this.emailInput.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (!email) {
                    this.showError(this.emailInput, this.emailError, 'El email es requerido');
                    return false;
                }
                
                if (!emailRegex.test(email)) {
                    this.showError(this.emailInput, this.emailError, 'Por favor ingresa un email válido');
                    return false;
                }
                
                this.clearError(this.emailInput, this.emailError);
                return true;
            }

            validatePassword() {
                const password = this.passwordInput.value.trim();
                
                if (!password) {
                    this.showError(this.passwordInput, this.passwordError, 'La contraseña es requerida');
                    return false;
                }
                
                if (password.length < 6) {
                    this.showError(this.passwordInput, this.passwordError, 'La contraseña debe tener al menos 6 caracteres');
                    return false;
                }
                
                this.clearError(this.passwordInput, this.passwordError);
                return true;
            }

            showError(input, errorElement, message) {
                input.classList.add('error');
                errorElement.textContent = message;
                errorElement.classList.add('show');
            }

            clearError(input, errorElement) {
                input.classList.remove('error');
                errorElement.classList.remove('show');
            }

            handleSubmit(e) {
                const isEmailValid = this.validateEmail();
                const isPasswordValid = this.validatePassword();
                
                if (!isEmailValid || !isPasswordValid) {
                    e.preventDefault();
                } else {
                    // Mostrar loading state
                    this.loginBtn.disabled = true;
                    this.btnText.textContent = 'Iniciando sesión...';
                }
            }
        }

        // Inicializar la aplicación cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', () => {
            new LoginApp();
        });

        // Manejar la tecla Enter en todo el formulario
        document.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>