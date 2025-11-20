<h2>Reservar clase</h2>
<?php if (empty($clase)): ?>
    <p>Clase no encontrada.</p>
<?php else: ?>
    <p>Clase: <strong><?php echo htmlspecialchars($clase['nombre']); ?></strong></p>
    <p>Fecha/Hora: <?php echo htmlspecialchars($clase['fecha_hora']); ?></p>
    <form method="post" action="">
        <input type="hidden" name="clase_id" value="<?php echo $clase['id']; ?>">
        <label>Fecha y hora de la reserva (puedes usar la fecha de la clase)<br>
            <input type="datetime-local" name="fecha_hora" value="<?php echo !empty($clase['fecha_hora']) ? date('Y-m-d\TH:i', strtotime($clase['fecha_hora'])) : ''; ?>">
        </label>
        <button class="btn" type="submit">Confirmar reserva</button>
    </form>
<?php endif; ?>