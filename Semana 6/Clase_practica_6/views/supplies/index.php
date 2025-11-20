<?php include 'views/layouts/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="page-header">
                <h1>Gestión de Suministros</h1>
                <p class="lead">Administre las necesidades de suministros del hotel</p>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-6">
            <?php if (in_array($_SESSION['user_role'], ['Administrator', 'Hotel Manager'])): ?>
                <a href="index.php?action=supply_create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Suministro
                </a>
            <?php endif; ?>
        </div>
        <div class="col-md-6 text-end">
            <span class="badge bg-info">Total: <?= count($supplies) ?> suministros</span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php if (empty($supplies)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay suministros registrados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Estado</th>
                                <th>Fecha de Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($supplies as $supply): ?>
                                <tr>
                                    <td><?= htmlspecialchars($supply['id']) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($supply['name']) ?></strong>
                                        <!-- description not in schema -->
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($supply['quantity']) ?></span>
                                    </td>

                                    <td>
                                        <?php 
                                        $status = $supply['status'] ?? 'unknown';
                                        $statusClass = 'secondary';
                                        $statusText = ucfirst($status);
                                        if ($status === 'requested') { $statusClass = 'warning'; $statusText = 'Solicitado'; }
                                        if ($status === 'offered') { $statusClass = 'info'; $statusText = 'Ofertado'; }
                                        if ($status === 'delivered') { $statusClass = 'success'; $statusText = 'Entregado'; }
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($supply['created_at'])) ?></td>
                                        <td>
                                        <div class="btn-group" role="group">
                                            <?php if (in_array($_SESSION['user_role'], ['Administrator', 'Hotel Manager'])): ?>
                                                <a href="index.php?action=supply_view&id=<?= $supply['id'] ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                                <a href="index.php?action=supply_edit&id=<?= $supply['id'] ?>" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <a href="index.php?action=supply_delete&id=<?= $supply['id'] ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('¿Está seguro de eliminar este suministro?')">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </a>
                                                <a href="index.php?action=supply_offer&id=<?= $supply['id'] ?>" class="btn btn-sm btn-info">Ofertar</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Resumen de Suministros
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 class="text-warning">
                                    <?= count(array_filter($supplies, fn($s) => $s['status'] === 'requested')) ?>
                                </h4>
                                <p class="text-muted mb-0">Solicitados</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 class="text-info">
                                    <?= count(array_filter($supplies, fn($s) => $s['status'] === 'offered')) ?>
                                </h4>
                                <p class="text-muted mb-0">Ofertados</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 class="text-success">
                                    <?= count(array_filter($supplies, fn($s) => $s['status'] === 'delivered')) ?>
                                </h4>
                                <p class="text-muted mb-0">Entregados</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.5rem;
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }
    
    .page-header h1 {
        margin: 0;
        font-weight: 700;
    }
    
    .page-header .lead {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        
        .btn-group .btn {
            margin-bottom: 5px;
            margin-right: 0;
        }
        
        .page-header {
            padding: 1.5rem;
            text-align: center;
        }
    }
</style>

<?php include 'views/layouts/footer.php'; ?>