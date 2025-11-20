<h2>Mis Clases</h2>
<div style="margin-bottom:12px"><a class="btn" href="?route=instructor/create_class">Crear clase</a></div>
<?php if (empty($classes)): ?>
    <p>No hay clases asignadas.</p>
<?php else: ?>
    <table class="table">
        <thead><tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Fecha/Hora</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($classes as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                <td><?php echo htmlspecialchars($c['tipo']); ?></td>
                <td><?php echo htmlspecialchars($c['fecha_hora']); ?></td>
                <td style="white-space:nowrap">
                    <a href="?route=instructor/edit_class&id=<?php echo $c['id']; ?>" class="btn">Editar</a>
                    <a href="?route=instructor/delete_class&id=<?php echo $c['id']; ?>" class="btn" onclick="return confirm('Confirmar eliminación?')">Eliminar</a>
                    <a href="?route=instructor/view_class&id=<?php echo $c['id']; ?>" class="btn">Ver asistentes</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>