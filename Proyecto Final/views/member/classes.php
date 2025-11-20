<h2>Clases disponibles</h2>
<?php if (empty($classes)): ?>
    <p>No hay clases programadas.</p>
<?php else: ?>
    <table class="table">
        <thead><tr><th>Nombre</th><th>Tipo</th><th>Instructor</th><th>Fecha/Hora</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($classes as $c): ?>
            <tr>
                <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                <td><?php echo htmlspecialchars($c['tipo']); ?></td>
                <td><?php echo htmlspecialchars($c['instructor_nombre'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($c['fecha_hora']); ?></td>
                <td>
                    <a class="btn" href="?route=member/reserve&id=<?php echo $c['id']; ?>">Reservar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>