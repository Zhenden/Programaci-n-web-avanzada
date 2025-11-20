<h2><?php echo empty($member) ? 'Crear miembro' : 'Editar miembro'; ?></h2>
<form method="post" action="">
    <label>Nombre<br><input type="text" name="nombre" value="<?php echo htmlspecialchars($member['nombre'] ?? ''); ?>" required></label>
    <label>Correo<br><input type="email" name="correo" value="<?php echo htmlspecialchars($member['correo'] ?? ''); ?>" required></label>
    <label>Contraseña (dejar en blanco para no cambiar)<br><input type="password" name="password"></label>
    <label>Fecha de nacimiento<br><input type="date" name="fecha_nacimiento" value="<?php echo htmlspecialchars($member['fecha_nacimiento'] ?? ''); ?>"></label>
    <label>Género<br><input type="text" name="género" value="<?php echo htmlspecialchars($member['género'] ?? ''); ?>"></label>
    <button type="submit" class="btn"><?php echo empty($member) ? 'Crear' : 'Guardar'; ?></button>
</form>