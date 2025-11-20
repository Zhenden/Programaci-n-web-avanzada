<?php require_once 'views/layouts/header.php'; ?>

<h2>Mis Reservas</h2>

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

<?php if (empty($bookings)): ?>
    <div class="card">
        <div class="card-body text-center">
            <h3>No tienes reservas aún</h3>
            <p>¡Haz tu primera reserva y disfruta de nuestras habitaciones de lujo!</p>
            <a href="index.php?action=booking_create" class="btn btn-primary">Hacer una Reserva</a>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($bookings as $booking): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Habitación <?= htmlspecialchars($booking['room_number']) ?> - <?= htmlspecialchars($booking['room_type']) ?></h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Fecha de Reserva:</strong> <?= date('d/m/Y', strtotime($booking['booking_date'])) ?></p>
                        <p><strong>Check-in:</strong> <?= date('d/m/Y', strtotime($booking['check_in_date'])) ?></p>
                        <p><strong>Check-out:</strong> <?= date('d/m/Y', strtotime($booking['check_out_date'])) ?></p>
                        <p><strong>Total:</strong> $<?= number_format($booking['total_price'], 2) ?></p>
                        <p><strong>Estado:</strong> 
                            <span class="room-status <?= $booking['status'] === 'confirmed' ? 'status-available' : ($booking['status'] === 'pending' ? 'status-warning' : 'status-occupied') ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </p>
                        
                        <?php if ($booking['status'] !== 'cancelled'): ?>
                            <div class="mt-3">
                                <a href="index.php?action=booking_cancel&id=<?= $booking['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de cancelar esta reserva?')">Cancelar Reserva</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'views/layouts/footer.php'; ?>