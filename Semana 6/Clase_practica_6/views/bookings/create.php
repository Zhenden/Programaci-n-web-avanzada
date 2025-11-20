<?php require_once 'views/layouts/header.php'; ?>

<h2>Reservar Habitación</h2>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card booking-form">
    <div class="card-header">
        <h3 class="card-title">Información de la Reserva</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?action=booking_store">
            <div class="form-group">
                <label for="room_id" class="form-label">Seleccionar Habitación</label>
                <select id="room_id" name="room_id" class="form-control" required>
                    <option value="">Seleccione una habitación</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['id'] ?>">
                            Habitación <?= $room['room_number'] ?> - <?= $room['room_type'] ?> - $<?= $room['room_price'] ?>/noche
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="date-inputs">
                <div class="form-group">
                    <label for="check_in_date" class="form-label">Fecha de Entrada</label>
                    <input type="date" id="check_in_date" name="check_in_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label for="check_out_date" class="form-label">Fecha de Salida</label>
                    <input type="date" id="check_out_date" name="check_out_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">💾 Confirmar Reserva</button>
                <a href="index.php?action=dashboard" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
// Set minimum checkout date based on checkin date
document.getElementById('check_in_date').addEventListener('change', function() {
    const checkin = this.value;
    if (checkin) {
        const date = new Date(checkin);
        date.setDate(date.getDate() + 1);
        const minCheckout = date.toISOString().split('T')[0];
        document.getElementById('check_out_date').min = minCheckout;
        
        // If checkout is before checkin, reset it
        const checkout = document.getElementById('check_out_date').value;
        if (checkout && checkout <= checkin) {
            document.getElementById('check_out_date').value = minCheckout;
        }
    }
});
</script>

<?php require_once 'views/layouts/footer.php'; ?>