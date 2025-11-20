<h2>Clase: <?php echo htmlspecialchars($class['nombre']); ?></h2>
<p>Tipo: <?php echo htmlspecialchars($class['tipo']); ?></p>
<p>Fecha/Hora: <?php echo htmlspecialchars($class['fecha_hora']); ?></p>
<p><a class="btn" href="?route=instructor/classes">Volver</a></p>

<h3>Asistentes</h3>
<?php if (empty($attendees)): ?>
    <p>No hay asistentes en esta clase.</p>
<?php else: ?>
    <table class="table">
        <thead><tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Fecha/Hora Reserva</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($attendees as $a): ?>
            <tr>
                <td><?php echo $a['miembro_id']; ?></td>
                <td><?php echo htmlspecialchars($a['miembro_nombre']); ?></td>
                <td><?php echo htmlspecialchars($a['miembro_correo']); ?></td>
                <td><?php echo htmlspecialchars($a['fecha_hora']); ?></td>
                <td><a class="btn" href="?route=instructor/delete_reservation&id=<?php echo $a['id']; ?>" onclick="return confirm('Eliminar reserva?')">Eliminar</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>