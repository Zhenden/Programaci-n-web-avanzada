<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mis Comentarios</h1>
        <a href="index.php?action=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Comentarios realizados</h2>
        </div>
        <div class="card-body">
            <?php if (empty($comments)): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-comments fa-3x mb-3"></i>
                    <p>No has realizado ningún comentario aún.</p>
                    <a href="index.php?action=dishes" class="btn btn-primary">
                        <i class="fas fa-utensils"></i> Explorar platos
                    </a>
                </div>
            <?php else: ?>
                <div class="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item mb-3 p-3 border rounded">
                            <div class="comment-header d-flex justify-content-between align-items-start mb-2">
                                <div class="dish-info">
                                    <h5 class="mb-1">
                                        <a href="index.php?action=dish&id=<?= htmlspecialchars($comment['dish_id']) ?>" 
                                           class="text-decoration-none">
                                            <i class="fas fa-utensils"></i> 
                                            <?= htmlspecialchars($comment['dish_name']) ?>
                                        </a>
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> 
                                        <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                                    </small>
                                </div>
                                
                                <div class="comment-actions">
                                    <form method="POST" action="index.php?action=comments/delete" 
                                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar este comentario?')"
                                          class="d-inline">
                                        <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                        <input type="hidden" name="csrf_token" value="<?= SessionManager::get('csrf_token') ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar comentario">
                                            <i class="fas fa-trash"></i>Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="comment-content">
                                <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.comment-item {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.comment-item:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.comment-content {
    font-size: 0.95rem;
    line-height: 1.5;
}

.dish-info a {
    color: #495057;
    transition: color 0.3s ease;
}

.dish-info a:hover {
    color: #007bff;
    text-decoration: none;
}

.comment-actions .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}
</style>