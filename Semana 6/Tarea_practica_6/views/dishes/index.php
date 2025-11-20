<div class="card fade-in">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2>Menú del Restaurante</h2>
        <?php if (SessionManager::get('role_name') === 'Administrator'): ?>
            <a href="index.php?action=dish_create" class="btn btn-success">Agregar Plato</a>
        <?php endif; ?>
    </div>
    
    <?php if (empty($dishes)): ?>
        <div class="alert alert-info">No hay platos disponibles.</div>
    <?php else: ?>
        <div class="dishes-grid">
            <?php foreach ($dishes as $row): ?>
                <div class="dish-card">
                    <div class="dish-header">
                        <h3><?= htmlspecialchars($row['name']) ?></h3>
                        <span class="price">$<?= htmlspecialchars($row['price']) ?></span>
                    </div>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                    <?php if (isset($row['category']) && !empty($row['category'])): ?>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars($row['category']) ?></p>
                    <?php endif; ?>
                    
                    <div class="dish-actions">
                        <a href="index.php?action=dish&id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-primary">Ver Detalles</a>
                        
                        <?php if (SessionManager::get('role_name') === 'Administrator'): ?>
                            <a href="index.php?action=dish_edit&id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-warning">Editar</a>
                            <a href="index.php?action=dish_delete&id=<?= htmlspecialchars($row['id']) ?>" 
                               class="btn btn-danger" 
                               onclick="return confirm('¿Estás seguro de eliminar este plato?')">
                                Eliminar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>