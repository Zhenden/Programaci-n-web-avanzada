<h2>Instructores</h2>
<?php if (empty($instructors)): ?>
    <p>No hay instructores registrados.</p>
<?php else: ?>
    <div style="margin-bottom:12px"><a class="btn" href="?route=admin/create_instructor">Crear instructor</a></div>
    <table class="table">
        <thead><tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Especialidad</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($instructors as $i): ?>
            <tr>
                <td><?php echo $i['id']; ?></td>
                <td><?php echo htmlspecialchars($i['nombre']); ?></td>
                <td><?php echo htmlspecialchars($i['correo']); ?></td>
                <td><?php echo htmlspecialchars($i['especialidad'] ?? ''); ?></td>
                <td style="white-space:nowrap">
                    <a href="?route=admin/edit_instructor&id=<?php echo $i['id']; ?>" class="btn">Editar</a>
                    <a href="?route=admin/delete_instructor&id=<?php echo $i['id']; ?>" class="btn" onclick="return confirm('Confirmar eliminación?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>