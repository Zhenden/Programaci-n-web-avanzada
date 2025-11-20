<h2><?php echo empty($class) ? 'Crear clase' : 'Editar clase'; ?></h2>
<form method="post" action="">
    <label>Nombre<br><input type="text" name="nombre" value="<?php echo htmlspecialchars($class['nombre'] ?? ''); ?>" required></label>
    <label>Tipo<br><input type="text" name="tipo" value="<?php echo htmlspecialchars($class['tipo'] ?? ''); ?>" required></label>
    <label>Fecha y hora<br><input type="datetime-local" name="fecha_hora" value="<?php echo !empty($class['fecha_hora']) ? date('Y-m-d\TH:i', strtotime($class['fecha_hora'])) : ''; ?>"></label>
    <button type="submit" class="btn"><?php echo empty($class) ? 'Crear' : 'Guardar'; ?></button>
    <button type="button" class="btn" onclick="window.location.href='?route=instructor/dashboard';">Cancelar</button>
</form>