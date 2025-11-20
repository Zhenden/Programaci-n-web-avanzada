<h2>Clases</h2>
<?php if (empty($classes)): ?>
    <p>No hay clases registradas.</p>
<?php else: ?>
    <div style="margin-bottom:12px"><a class="btn" href="?route=admin/create_class">Crear clase</a></div>
    <table class="table">
        <thead><tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Instructor</th><th>Fecha/Hora</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($classes as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                <td><?php echo htmlspecialchars($c['tipo']); ?></td>
                <td><?php echo htmlspecialchars($c['instructor_nombre'] ?? $c['instructor_id']); ?></td>
                <td><?php echo htmlspecialchars($c['fecha_hora']); ?></td>
                <td style="white-space:nowrap">
                    <a href="?route=admin/edit_class&id=<?php echo $c['id']; ?>" class="btn">Editar</a>
                    <a href="?route=admin/delete_class&id=<?php echo $c['id']; ?>" class="btn" onclick="return confirm('Confirmar eliminación?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>