<link rel="stylesheet" href="../assets/css/styles.css">

<div class="card fade-in">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><?= isset($dish) ? 'Editar Plato' : 'Crear Nuevo Plato' ?></h2>
        <a href="index.php?action=dishes" class="btn btn-secondary">Volver al Menú</a>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <form method="POST" action="index.php?action=<?= isset($dish) ? 'dish_update' : 'dish_store' ?>">
                <?php if (isset($dish)): ?>
                    <input type="hidden" name="id" value="<?= $dish['id'] ?>">
                <?php endif; ?>
                
                <div class="form-group mb-3">
                    <label for="name">Nombre del Plato:</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= htmlspecialchars($dish['name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group mb-3">
                    <label for="description">Descripción:</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($dish['description'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group mb-3">
                    <label for="price">Precio:</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" id="price" name="price" 
                               value="<?= htmlspecialchars($dish['price'] ?? '') ?>" 
                               step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= isset($dish) ? 'Actualizar' : 'Crear' ?> Plato
                    </button>
                    <a href="index.php?action=dishes" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Información</h5>
                    <p class="card-text">
                        Complete los datos del plato. Todos los campos son obligatorios.
                    </p>
                    <?php if (isset($dish)): ?>
                        <p class="text-muted">
                            <small>ID del plato: <?= $dish['id'] ?></small>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>