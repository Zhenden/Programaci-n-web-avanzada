<h2>Login</h2>
<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<form method="post" action="?route=auth/login">
    <label>Usuario o correo<br><input type="text" name="email" required></label>
    <label>Contraseña<br><input type="password" name="password" required></label>
    <button type="submit" class="btn">Entrar</button>
</form>
<p>Admin demo: usa <strong>admin</strong> como usuario y <strong>admin</strong> como contraseña. Para miembros/instructores ingresa su correo y contraseña correspondiente.</p>