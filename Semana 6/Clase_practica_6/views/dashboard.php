<?php require_once 'views/layouts/header.php'; ?>

<div class="hero">
    <h1>Bienvenido, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
    <p>Rol: <?= htmlspecialchars($_SESSION['user_role']) ?></p>
</div>

<?php if (in_array($_SESSION['user_role'], ['Administrator', 'Hotel Manager', 'Receptionist'])): ?>
    <h2>Estadísticas del Hotel</h2>
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-number"><?= $totalRooms ?? 0 ?></div>
            <div class="stat-label">Total Habitaciones</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $availableRooms ?? 0 ?></div>
            <div class="stat-label">Habitaciones Disponibles</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $totalBookings ?? 0 ?></div>
            <div class="stat-label">Total Reservas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $totalUsers ?? 0 ?></div>
            <div class="stat-label">Total Usuarios</div>
        </div>
    </div>
    
    <?php if (!empty($recentBookings)): ?>
        <h2>Reservas Recientes</h2>
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Habitación</th>
                        <th>Cliente</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Estado</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['room_number']) ?> - <?= htmlspecialchars($booking['room_type']) ?></td>
                            <td><?= htmlspecialchars($booking['user_name']) ?></td>
                            <td><?= date('d/m/Y', strtotime($booking['check_in_date'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($booking['check_out_date'])) ?></td>
                            <td>
                                <span class="room-status <?= $booking['status'] === 'confirmed' ? 'status-available' : ($booking['status'] === 'pending' ? 'status-warning' : 'status-occupied') ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </td>
                            <td>$<?= number_format($booking['total_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($_SESSION['user_role'] === 'Customer'): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">🛏️ Hacer una Reserva</h3>
                </div>
                <div class="card-body">
                    <p>Reserva tu habitación favorita con nosotros</p>
                    <a href="index.php?action=booking_create" class="btn btn-primary">Reservar Ahora</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📋 Mis Reservas</h3>
                </div>
                <div class="card-body">
                    <p>Ver y gestionar tus reservas</p>
                    <a href="index.php?action=my_bookings" class="btn btn-success">Ver Mis Reservas</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (in_array($_SESSION['user_role'], ['Administrator', 'Hotel Manager'])): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📦 Gestión de Suministros</h3>
        </div>
        <div class="card-body">
            <p>Ver y gestionar suministros para el hotel</p>
            <a href="index.php?action=supplies" class="btn btn-primary">Ver Suministros</a>
            <?php if (in_array($_SESSION['user_role'], ['Administrator', 'Hotel Manager'])): ?>
                <a href="index.php?action=supplies_create" class="btn btn-success">Añadir Suministro</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'views/layouts/footer.php'; ?>