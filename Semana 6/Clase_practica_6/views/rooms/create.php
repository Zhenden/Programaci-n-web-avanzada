<?php require_once 'views/layouts/header.php'; ?>

<h2>Crear Nueva Habitación</h2>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información de la Habitación</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?action=room_store">
            <div class="form-group">
                <label for="room_number" class="form-label">Número de Habitación</label>
                <input type="number" id="room_number" name="room_number" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="room_type" class="form-label">Tipo de Habitación</label>
                <select id="room_type" name="room_type" class="form-control" required>
                    <option value="">Seleccione un tipo</option>
                    <option value="Single">Single</option>
                    <option value="Double">Double</option>
                    <option value="Suite">Suite</option>
                    <option value="Deluxe">Deluxe</option>
                    <option value="Presidential">Presidential</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price" class="form-label">Precio por Noche ($)</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" min="0" required>
            </div>
            
            <!-- Description removed: not present in DB schema -->
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">💾 Guardar Habitación</button>
                <a href="index.php?action=rooms" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>