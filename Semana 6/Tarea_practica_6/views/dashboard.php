<link rel="stylesheet" href="../assets/css/styles.css">

<div class="dashboard fade-in">
    <div class="welcome-section">
        <h1 class="text-center mb-1">Bienvenido al Restaurante</h1>
        <p class="text-center text-muted">
            <?= htmlspecialchars(SessionManager::get('username')) ?> (<?= htmlspecialchars(SessionManager::get('role_name')) ?>)
        </p>
    </div>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>📋</h3>
            <p><a href="index.php?action=dishes" class="btn btn-primary">Ver Menú</a></p>
            <small>Explora nuestros deliciosos platos</small>
        </div>
        
        <div class="stat-card">
            <h3>🛒</h3>
            <p><a href="index.php?action=orders" class="btn btn-success">Ver Pedidos</a></p>
            <small>Revisa tus pedidos actuales</small>
        </div>
        
        <div class="stat-card">
            <h3>💬</h3>
            <p><a href="index.php?action=comments/user" class="btn btn-info">Mis Comentarios</a></p>
            <small>Ver tus comentarios en platos</small>
        </div>
        
        <?php if(SessionManager::get('role_name') === 'Administrator'): ?>
            <div class="stat-card">
                <h3>⚙️</h3>
                <p><a href="index.php?action=users" class="btn btn-warning">Administrar Usuarios</a></p>
                <small>Gestiona los usuarios del sistema</small>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="quick-actions">
        <h3 class="text-center mb-2">Acciones Rápidas</h3>
        <div class="actions-grid">
            <a href="index.php?action=dishes" class="action-card">
                <h4>🍽️ Menú</h4>
                <p>Ver todos los platos disponibles</p>
            </a>
            
            <a href="index.php?action=orders" class="action-card">
                <h4>📋 Pedidos</h4>
                <p>Gestionar pedidos del restaurante</p>
            </a>
            
            <?php if(SessionManager::get('role_name') !== 'Chef'): ?>
                <a href="index.php?action=order_create" class="action-card">
                    <h4>➕ Nuevo Pedido</h4>
                    <p>Crear un nuevo pedido</p>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>