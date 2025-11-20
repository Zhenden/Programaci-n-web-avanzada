<link rel="stylesheet" href="../assets/css/styles.css">

<div class="card fade-in">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Gestión de Usuarios</h2>
        <a href="index.php?action=user_create" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    </div>
    
    <!-- Sistema de notificaciones -->
    <?php if ($success = SessionManager::get('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php SessionManager::remove('success'); ?>
    <?php endif; ?>
    
    <?php if ($error = SessionManager::get('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php SessionManager::remove('error'); ?>
    <?php endif; ?>
    
    <!-- Formulario de búsqueda -->
    <form method="GET" action="index.php" class="mb-3" role="search" aria-label="Búsqueda de usuarios">
        <input type="hidden" name="action" value="users">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o email..." value="<?= htmlspecialchars($search) ?>" aria-label="Campo de búsqueda">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit" aria-label="Buscar usuarios">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <?php if (!empty($search)): ?>
                    <a href="index.php?action=users" class="btn btn-secondary" aria-label="Limpiar búsqueda">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </form>
    
    <!-- Tabla de usuarios -->
    <div class="table-responsive">
        <?php if (empty($users)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No se encontraron usuarios.
            </div>
        <?php else: ?>
            <table class="table table-hover" role="table" aria-label="Tabla de usuarios">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre de Usuario</th>
                        <th scope="col">Email</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Fecha de Creación</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge badge-<?= $this->getRoleBadgeClass($user['role_name']) ?>" role="status">
                                    <?= htmlspecialchars($user['role_name']) ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Acciones del usuario">
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')"
                                            <?= $user['id'] == SessionManager::get('user_id') ? 'disabled' : '' ?>
                                            aria-label="Eliminar usuario <?= htmlspecialchars($user['username']) ?>">
                                        <i class="fas fa-trash" aria-hidden="true"></i>Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Paginación -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Paginación de usuarios">
            <ul class="pagination justify-content-center">
                <!-- Página anterior -->
                <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?action=users&search=<?= urlencode($search) ?>&page=<?= $current_page - 1 ?>" 
                       aria-label="Ir a la página anterior"<?= $current_page <= 1 ? ' aria-disabled="true" tabindex="-1"' : '' ?>>
                        Anterior
                    </a>
                </li>
                
                <!-- Números de página -->
                <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?action=users&search=<?= urlencode($search) ?>&page=<?= $i ?>" 
                           aria-label="Ir a la página <?= $i ?>"<?= $i == $current_page ? ' aria-current="page"' : '' ?>>
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <!-- Página siguiente -->
                <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?action=users&search=<?= urlencode($search) ?>&page=<?= $current_page + 1 ?>" 
                       aria-label="Ir a la página siguiente"<?= $current_page >= $total_pages ? ' aria-disabled="true" tabindex="-1"' : '' ?>>
                        Siguiente
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
    
    <!-- Información de resultados -->
    <div class="text-center text-muted mt-3">
        Mostrando <?= count($users) ?> de <?= $total_users ?> usuarios
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar al usuario <strong id="deleteUserName"></strong>?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form method="POST" action="index.php?action=user_delete" id="deleteForm">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId, userName) {
    console.log('Opening modal for user:', userId, userName); // Debug
    
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserName').textContent = userName;
    
    // Remove aria-hidden to make modal accessible when shown
    const modal = document.getElementById('deleteModal');
    modal.removeAttribute('aria-hidden');
    
    // Show modal using vanilla JavaScript
    modal.classList.add('show');
    modal.style.display = 'block';
    document.body.classList.add('modal-open');
    
    // Create backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.id = 'modalBackdrop';
    document.body.appendChild(backdrop);
    
    // Focus on the cancel button for better accessibility
    const cancelButton = modal.querySelector('.btn-secondary');
    if (cancelButton) {
        cancelButton.focus();
    }
}

// Handle modal close for accessibility
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up modal handlers'); // Debug
    
    const modal = document.getElementById('deleteModal');
    if (!modal) {
        console.error('Modal not found');
        return;
    }
    
    // Add event listeners for close buttons
    const closeButtons = modal.querySelectorAll('[data-dismiss="modal"]');
    console.log('Found close buttons:', closeButtons.length); // Debug
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Close button clicked'); // Debug
            closeModal();
        });
    });
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            console.log('Clicked outside modal'); // Debug
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            console.log('Escape key pressed'); // Debug
            closeModal();
        }
    });
});

function closeModal() {
    console.log('Closing modal'); // Debug
    
    const modal = document.getElementById('deleteModal');
    const backdrop = document.getElementById('modalBackdrop');
    
    modal.classList.remove('show');
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    
    if (backdrop) {
        backdrop.remove();
    }
}

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('alert-dismissible')) {
                const closeBtn = alert.querySelector('.close');
                if (closeBtn) {
                    closeBtn.click();
                }
            }
        });
    }, 5000);
});
</script>