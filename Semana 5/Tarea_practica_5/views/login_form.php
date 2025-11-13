<!-- views/login_form.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca Online - Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* tus estilos actuales */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(120deg, #2c7be5, #1b5fc1);
        }
        .login-box {
            background: #fff;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .error-message { color: red; text-align: center; margin-bottom: 1rem; }
        .input-group { margin-bottom: 1.2rem; }
        .input-group label { display: block; font-weight: bold; margin-bottom: 0.5rem; }
        .input-group input { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 10px; }
        .btn-login { width: 100%; background: #2c7be5; color: white; border: none; border-radius: 10px; padding: 0.75rem; cursor: pointer; font-weight: bold; }
        .btn-login:hover { background: #1b5fc1; }
        .footer { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: #777; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Biblioteca Online</h2>

        <?php if (!empty($error)): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login">
            <div class="input-group">
                <label for="email">Correo electrónico</label>
                <input type="email" name="email" id="email" placeholder="ejemplo@correo.com" required>
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Iniciar sesión</button>
        </form>
            <div>
                <small style="color: #555; text-align: center; display: block; margin-top: 1rem;">
                    Cuentas demo: <br> 
                    admin@biblioteca.local / password (Administrador) <br>
                    bibl@biblioteca.local / password (Bibliotecario) <br>
                    juan@gmail.com / password (Usuario)
                </small>
            </div>
        <div class="footer">
            <p> Hecho usando el patron mvc (me cuesta adaptarme a laravel)    </p>
        </div>
    </div>
</body>
</html>