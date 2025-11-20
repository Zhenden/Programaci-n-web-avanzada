<h2>Historial de reservas</h2>
<?php if (empty($reservas)): ?>
    <p>No tienes reservas.</p>
<?php else: ?>
    <table class="table">
        <thead><tr><th>ID</th><th>Clase</th><th>Fecha/Hora</th><th>Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($reservas as $r): ?>
            <tr>
                <td><?php echo $r['id']; ?></td>
                <td><?php echo htmlspecialchars($r['clase_nombre']); ?></td>
                <td><?php echo htmlspecialchars($r['fecha_hora']); ?></td>
                <td style="white-space:nowrap">
                    <a href="?route=member/cancel_reservation&id=<?php echo $r['id']; ?>" class="btn" onclick="return confirm('Cancelar reserva?')">Cancelar</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>