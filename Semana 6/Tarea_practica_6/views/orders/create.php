<div class="card fade-in">
    <h2 class="mb-2">Crear Nuevo Pedido</h2>
    
    <form action="index.php?action=order_store" method="POST">
        <div class="form-group">
            <label for="dish_id">Selecciona un Plato</label>
            <select name="dish_id" id="dish_id" class="form-control" required>
                <option value="">-- Selecciona un plato --</option>
                <?php foreach($dishes as $r): ?>
                    <option value="<?= htmlspecialchars($r['id']) ?>">
                        <?= htmlspecialchars($r['name']) ?> - $<?= number_format($r['price'],2) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="quantity">Cantidad</label>
            <input type="number" name="quantity" id="quantity" value="1" min="1" max="10" 
                   class="form-control" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enviar Pedido</button>
            <a href="index.php?action=orders" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>