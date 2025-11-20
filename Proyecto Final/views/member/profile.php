<h2>Mi perfil</h2>
<form method="post" action="">
    <label>Nombre<br><input type="text" name="nombre" value="<?php echo htmlspecialchars($member['nombre'] ?? ''); ?>" required></label>
    <label>Correo<br><input type="email" name="correo" value="<?php echo htmlspecialchars($member['correo'] ?? ''); ?>" required></label>
    <label>Nueva contraseña (dejar en blanco para no cambiar)<br><input type="password" name="password"></label>
    <button class="btn" type="submit">Guardar</button>
</form>