<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Ver Suministro</h1>
        <a href="index.php?action=supplies" class="btn btn-secondary">Volver a Suministros</a>
    </div>

    <div class="card">
        <div class="card-body">
            <h4><?= htmlspecialchars($supply['name']) ?></h4>
            <p><strong>Cantidad:</strong> <?= htmlspecialchars($supply['quantity'] ?? 'N/A') ?></p>
            <?php if (isset($supply['status'])): ?>
                <p><strong>Estado:</strong> <?= htmlspecialchars($supply['status']) ?></p>
            <?php endif; ?>
            <?php if (isset($supply['created_at'])): ?>
                <p><strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($supply['created_at'])) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
