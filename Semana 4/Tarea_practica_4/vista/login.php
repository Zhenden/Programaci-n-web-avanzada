<?php
// views/login.php
// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?action=dashboard');
    exit;
}

$message = $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';
?>
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
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 1.8em;
        }

        .login-header p {
            color: #666;
            font-size: 0.9em;
        }

        .logo {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-1px);
        }

        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9em;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .login-footer p {
            color: #666;
            font-size: 0.9em;
        }

        .demo-accounts {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 0.8em;
        }

        .demo-accounts h4 {
            margin-bottom: 10px;
            color: #333;
        }

        .demo-account {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .demo-account:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .loading {
            display: none;
            text-align: center;
            margin-bottom: 15px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">📋</div>
            <h1>Sistema de Tareas</h1>
            <p>Inicia sesión para gestionar tus tareas</p>
        </div>

        <?php if ($message): ?>
            <div class="message success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" onsubmit="submitLogin(event)">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required placeholder="Tu contraseña">
            </div>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Iniciando sesión...</p>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">Iniciar Sesión</button>
        </form>

        <!-- Cuentas de demostración -->
        <div class="demo-accounts">
            <h4>💡 Cuentas de Demo</h4>
            <div class="demo-account">
                <span><strong>Admin:</strong> admin@sistema.com</span>
                <span>password</span>
            </div>
            <div class="demo-account">
                <span><strong>Usuario:</strong> maria@demo.com	</span>
                <span>password</span>
            </div>
        </div>

        <div class="login-footer">
            <p>Sistema de Gestión de Tareas v1.0</p>
        </div>
    </div>

    <script>
        function submitLogin(event) {
            event.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            
            // Mostrar loading
            submitBtn.disabled = true;
            loading.style.display = 'block';
            
            const formData = new FormData(document.getElementById('loginForm'));
            
            fetch('index.php?action=login', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir al dashboard
                    window.location.href = data.redirect;
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.disabled = false;
                    loading.style.display = 'none';
                }
            })
            .catch(async error => {
                const response = await fetch('index.php?action=login', { method: 'POST', body: formData });
                const text = await response.text();
                console.error('Respuesta recibida (no JSON):', text);
                alert('Error: la respuesta del servidor no es válida JSON.');
                submitBtn.disabled = false;
                loading.style.display = 'none';
            });

        }

        // Auto-focus en el campo email
        document.getElementById('email').focus();

        // Permitir enviar con Enter
        document.getElementById('loginForm').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                submitLogin(e);
            }
        });
    </script>
</body>
</html>