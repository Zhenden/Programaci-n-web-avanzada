<h2><?php echo empty($facility) ? 'Crear instalación' : 'Editar instalación'; ?></h2>
<form method="post" action="">
    <label>Nombre<br><input type="text" name="nombre" value="<?php echo htmlspecialchars($facility['nombre'] ?? ''); ?>" required></label>
    <label>Tipo<br><input type="text" name="tipo" value="<?php echo htmlspecialchars($facility['tipo'] ?? ''); ?>" required></label>
    <label>Capacidad<br><input type="number" name="capacidad" value="<?php echo htmlspecialchars($facility['capacidad'] ?? 0); ?>" required></label>
    <button type="submit" class="btn"><?php echo empty($facility) ? 'Crear' : 'Guardar'; ?></button>
</form>