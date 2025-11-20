<h2>Panel de Miembro</h2>
<?php if (!empty($user)): ?>
    <p>Bienvenido, <?php echo htmlspecialchars($user['username']); ?>. Aquí podrás reservar clases y ver tu historial.</p>
    <p><a class="btn" href="?route=member/classes">Ver clases disponibles</a>
    <a class="btn" href="?route=member/history">Mi historial</a>
    <a class="btn" href="?route=member/profile">Mi perfil</a></p>

    <h3>Próximas reservas</h3>
    <?php
    require_once __DIR__ . '/../../app/models/Reserva.php';
    $userId = $user['id'] ?? null;
    $upcoming = $userId ? Reserva::findByMember($userId) : [];
    if (empty($upcoming)) {
        echo '<p>No tienes reservas próximas.</p>';
    } else {
        echo '<table class="table"><thead><tr><th>Clase</th><th>Fecha/Hora</th><th>Acciones</th></tr></thead><tbody>';
        foreach ($upcoming as $r) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($r['clase_nombre']) . '</td>';
            echo '<td>' . htmlspecialchars($r['fecha_hora']) . '</td>';
            echo '<td style="white-space:nowrap"><a class="btn" href="?route=member/cancel_reservation&id=' . $r['id'] . '" onclick="return confirm(\'Cancelar reserva?\')">Cancelar</a></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }
    ?>
<?php else: ?>
    <p>Accede para ver tu perfil y reservas.</p>
<?php endif; ?>