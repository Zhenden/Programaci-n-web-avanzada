<h2>Miembros</h2>
<?php if (empty($members)): ?>
    <p>No hay miembros registrados.</p>
<?php else: ?>
    <div style="margin-bottom:12px"><a class="btn" href="?route=admin/create_member">Crear miembro</a></div>
    <table class="table">
        <thead><tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Fecha Nac.</th><th>Género</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($members as $m): ?>
            <tr>
                <td><?php echo $m['id']; ?></td>
                <td><?php echo htmlspecialchars($m['nombre']); ?></td>
                <td><?php echo htmlspecialchars($m['correo']); ?></td>
                <td><?php echo htmlspecialchars($m['fecha_nacimiento']); ?></td>
                <td><?php echo htmlspecialchars($m['género']); ?></td>
                <td style="white-space:nowrap">
                    <a href="?route=admin/edit_member&id=<?php echo $m['id']; ?>" class="btn">Editar</a>
                    <a href="?route=admin/delete_member&id=<?php echo $m['id']; ?>" class="btn" onclick="return confirm('Confirmar eliminación?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>