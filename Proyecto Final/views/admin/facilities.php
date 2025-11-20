<h2>Instalaciones</h2>
<?php if (empty($facilities)): ?>
    <p>No hay instalaciones registradas.</p>
<?php else: ?>
    <div style="margin-bottom:12px"><a class="btn" href="?route=admin/create_facility">Crear instalación</a></div>
    <table class="table">
        <thead><tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Capacidad</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($facilities as $f): ?>
            <tr>
                <td><?php echo $f['id']; ?></td>
                <td><?php echo htmlspecialchars($f['nombre']); ?></td>
                <td><?php echo htmlspecialchars($f['tipo']); ?></td>
                <td><?php echo htmlspecialchars($f['capacidad']); ?></td>
                <td style="white-space:nowrap">
                    <a href="?route=admin/edit_facility&id=<?php echo $f['id']; ?>" class="btn">Editar</a>
                    <a href="?route=admin/delete_facility&id=<?php echo $f['id']; ?>" class="btn" onclick="return confirm('Confirmar eliminación?')">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>