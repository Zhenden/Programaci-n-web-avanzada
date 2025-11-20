<h2><?php echo empty($instructor) ? 'Crear instructor' : 'Editar instructor'; ?></h2>
<form method="post" action="">
    <label>Nombre<br><input type="text" name="nombre" value="<?php echo htmlspecialchars($instructor['nombre'] ?? ''); ?>" required></label>
    <label>Correo<br><input type="email" name="correo" value="<?php echo htmlspecialchars($instructor['correo'] ?? ''); ?>" required></label>
    <label>Contraseña (dejar en blanco para no cambiar)<br><input type="password" name="password"></label>
    <label>Especialidad<br><input type="text" name="especialidad" value="<?php echo htmlspecialchars($instructor['especialidad'] ?? ''); ?>"></label>
    <button type="submit" class="btn"><?php echo empty($instructor) ? 'Crear' : 'Guardar'; ?></button>
</form>