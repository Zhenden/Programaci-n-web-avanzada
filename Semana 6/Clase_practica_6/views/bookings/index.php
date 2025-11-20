<?php require_once 'views/layouts/header.php'; ?>

<h2>Gestión de Reservas</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Habitación</th>
                <th>Tipo</th>
                <th>Fecha Reserva</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= $booking['id'] ?></td>
                    <td><?= htmlspecialchars($booking['user_name']) ?></td>
                    <td><?= htmlspecialchars($booking['room_number']) ?></td>
                    <td><?= htmlspecialchars($booking['room_type']) ?></td>
                    <td><?= date('d/m/Y', strtotime($booking['booking_date'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($booking['check_in_date'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($booking['check_out_date'])) ?></td>
                    <td>$<?= number_format($booking['total_price'], 2) ?></td>
                    <td>
                        <span class="room-status <?= $booking['status'] === 'confirmed' ? 'status-available' : ($booking['status'] === 'pending' ? 'status-warning' : 'status-occupied') ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($booking['status'] === 'pending'): ?>
                            <a href="index.php?action=booking_confirm&id=<?= $booking['id'] ?>" class="btn btn-success btn-sm">✓ Confirmar</a>
                        <?php endif; ?>
                        <?php if ($booking['status'] !== 'cancelled'): ?>
                            <a href="index.php?action=booking_cancel&id=<?= $booking['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de cancelar esta reserva?')">✗ Cancelar</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'views/layouts/footer.php'; ?>