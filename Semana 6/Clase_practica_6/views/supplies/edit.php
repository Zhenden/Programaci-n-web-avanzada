<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Editar Suministro</h1>
        <a href="index.php?action=supplies" class="btn btn-secondary">Volver a Suministros</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php?action=supply_update" method="POST">
                <input type="hidden" name="id" value="<?= $supply['id'] ?>">
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($supply['name']) ?>">
                </div>
                <?php if (isset($supply['quantity'])): ?>
                <div class="form-group">
                    <label for="quantity">Cantidad</label>
                    <input type="number" id="quantity" name="quantity" class="form-control" min="0" value="<?= htmlspecialchars($supply['quantity']) ?>">
                </div>
                <?php endif; ?>
                <?php if (isset($supply['status'])): ?>
                <div class="form-group">
                    <label for="status">Estado</label>
                    <select id="status" name="status" class="form-control">
                        <option value="requested" <?= $supply['status'] === 'requested' ? 'selected' : '' ?>>Solicitado</option>
                        <option value="offered" <?= $supply['status'] === 'offered' ? 'selected' : '' ?>>Ofertado</option>
                        <option value="delivered" <?= $supply['status'] === 'delivered' ? 'selected' : '' ?>>Entregado</option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="form-group mt-3">
                    <button class="btn btn-primary">Guardar</button>
                    <a href="index.php?action=supplies" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
