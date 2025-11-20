
<div class="card fade-in">
    <div class="dish-detail">
        <h1><?= htmlspecialchars($dish['name']) ?></h1>
        <div class="dish-description">
            <?= nl2br(htmlspecialchars($dish['description'])) ?>
        </div>
        <div class="dish-price">
            <span class="price-label">Precio:</span>
            <span class="price-value">$<?= number_format($dish['price'], 2) ?></span>
        </div>
        
        <div class="dish-actions mt-2">
            <a href="index.php?action=order_create&dish_id=<?= htmlspecialchars($dish['id']) ?>" 
               class="btn btn-primary">
                Ordenar este plato
            </a>
            <a href="index.php?action=dishes" class="btn btn-secondary">
                Volver al menú
            </a>
        </div>
    </div>
</div>

<?php // include comentarios list
require __DIR__ . '/../comments/list.php'; ?>

