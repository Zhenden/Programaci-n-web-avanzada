<?php require_once 'views/layouts/header.php'; ?>

<h2>Gestión de Habitaciones</h2>

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

<div class="mb-3">
    <a href="index.php?action=room_create" class="btn btn-primary">➕ Nueva Habitación</a>
</div>

<div class="row">
    <?php foreach ($rooms as $room): ?>
        <div class="col-md-4">
            <div class="room-card">
                <div class="room-header">
                    <div class="room-number">Habitación <?= htmlspecialchars($room['room_number']) ?></div>
                    <div class="room-type"><?= htmlspecialchars($room['room_type']) ?></div>
                </div>
                <div class="room-body">
                    <div class="room-price">$<?= number_format($room['room_price'], 2) ?> / noche</div>
                    <div class="mb-3">
                        <span class="room-status <?= $room['is_available'] ? 'status-available' : 'status-occupied' ?>">
                            <?= $room['is_available'] ? 'Disponible' : 'Ocupada' ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="index.php?action=room_edit&id=<?= $room['id'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                        <a href="index.php?action=room_delete&id=<?= $room['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar esta habitación?')">🗑️ Eliminar</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'views/layouts/footer.php'; ?>