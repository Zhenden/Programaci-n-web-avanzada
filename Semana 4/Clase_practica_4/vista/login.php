<?php
require_once '../controlador/Auth.php';

$auth = new Auth();

// Si ya está logueado, redirigir al dashboard
if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, complete todos los campos';
    } else {
        $result = $auth->login($email, $password);
        
        if ($result['success']) {
            $success = $result['message'];
            header('Location: ' . $result['redirect']);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Calificaciones</title>
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
        }
        
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .alert-error {
            background: #fee;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .alert-success {
            background: #e8f5e8;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .demo-accounts {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 12px;
        }
        
        .demo-accounts h3 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .demo-account {
            margin-bottom: 5px;
            padding: 5px;
            background: white;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🎓 Sistema Escolar</h1>
            <p>Ingrese a su cuenta</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">📧 Correo Electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>
        
        <div class="demo-accounts">
            <h3>Cuentas de Demo:</h3>
            <div class="demo-account">
                <strong>Admin:</strong> carlos.mendoza@escuela.edu / password
            </div>
            <div class="demo-account">
                <strong>Docente:</strong> elena.castro@escuela.edu / password
            </div>
            <div class="demo-account">
                <strong>Estudiante:</strong> ana.garcia@escuela.edu / password
            </div>
        </div>
    </div>
</body>
</html>