
<div id="comments-section" class="comments-section mt-4">
    <div class="card">
        <div class="card-header">
            <h3>Comentarios</h3>
        </div>
        <div class="card-body">
            <?php
            // Obtener comentarios si no están proporcionados
            if (!isset($comments)) {
                $commentModel = new Comment();
                $dishId = $dish['id'] ?? $_GET['dish_id'] ?? 0;
                $comments = $commentModel->getByDishId($dishId);
            }
            ?>
            
            <?php if (SessionManager::get('user_id')): ?>
                <!-- Formulario para agregar comentario -->
                <div class="comment-form mb-4">
                    <form method="POST" action="index.php?action=comments/create" class="comment-form">
                        <input type="hidden" name="dish_id" value="<?= htmlspecialchars($dish['id'] ?? $_GET['dish_id'] ?? '') ?>">
                        <input type="hidden" name="csrf_token" value="<?= SessionManager::get('csrf_token') ?>">
                        <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '') ?>">
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'] ?? '') ?>">
                        
                        <div class="form-group">
                            <label for="comment">Agregar comentario:</label>
                            <textarea 
                                name="comment" 
                                id="comment" 
                                class="form-control" 
                                rows="3" 
                                placeholder="Escribe tu comentario aquí..." 
                                maxlength="1000" 
                                required
                            ></textarea>
                            <small class="form-text text-muted">Máximo 1000 caracteres</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-comment"></i> Comentar
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <a href="index.php?action=login">Inicia sesión</a> para dejar un comentario
                </div>
            <?php endif; ?>
            
            <!-- Lista de comentarios -->
            <div class="comments-list">
                <?php if (empty($comments)): ?>
                    <div class="no-comments text-center text-muted py-4">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>No hay comentarios aún. Sé el primero en comentar.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div id="comment-<?= htmlspecialchars($comment['id']) ?>" class="comment-item mb-3 p-3 border rounded" data-comment-id="<?= htmlspecialchars($comment['id']) ?>">
                            <div class="comment-header d-flex justify-content-between align-items-start mb-2">
                                <div class="comment-author">
                                    <strong><?= htmlspecialchars($comment['username']) ?></strong>
                                    <small class="text-muted ml-2">
                                        <i class="fas fa-clock"></i> 
                                        <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                                    </small>
                                </div>
                                
                                <?php if (SessionManager::get('user_id') == $comment['user_id'] || SessionManager::get('role_name') == 'Administrator'): ?>
                                    <div class="comment-actions">
                                        <?php if (SessionManager::get('user_id') == $comment['user_id']): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editComment(<?= htmlspecialchars($comment['id']) ?>)"
                                                    title="Editar comentario">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteComment(<?= htmlspecialchars($comment['id']) ?>)"
                                                title="Eliminar comentario">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="comment-content" id="comment-content-<?= htmlspecialchars($comment['id']) ?>">
                                <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                            </div>
                            
                            <!-- Formulario de edición (oculto por defecto) -->
                            <div id="comment-edit-<?= htmlspecialchars($comment['id']) ?>" class="comment-edit-form" style="display: none;">
                                <form method="POST" action="index.php?action=comments/update" class="mt-2">
                                    <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= SessionManager::get('csrf_token') ?>">
                                    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '') ?>">
                                    <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'] ?? '') ?>">
                                    
                                    <div class="form-group">
                                        <textarea 
                                            name="comment" 
                                            class="form-control form-control-sm" 
                                            rows="3" 
                                            maxlength="1000" 
                                            required
                                        ><?= htmlspecialchars($comment['comment']) ?></textarea>
                                        <small class="form-text text-muted">Máximo 1000 caracteres</small>
                                    </div>
                                    
                                    <div class="btn-group btn-group-sm">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="cancelEdit(<?= htmlspecialchars($comment['id']) ?>)">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para eliminar comentarios -->
<form id="delete-comment-form" method="POST" action="index.php?action=comments/delete" style="display: none;">
    <input type="hidden" name="comment_id" id="delete-comment-id">
    <input type="hidden" name="csrf_token" value="<?= SessionManager::get('csrf_token') ?>">
    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '') ?>">
    <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'] ?? '') ?>">
</form>

<script>
function deleteComment(commentId) {
    if (confirm('¿Estás seguro de que quieres eliminar este comentario?')) {
        document.getElementById('delete-comment-id').value = commentId;
        document.getElementById('delete-comment-form').submit();
    }
}

function editComment(commentId) {
    // Ocultar contenido del comentario y mostrar formulario de edición
    document.getElementById('comment-content-' + commentId).style.display = 'none';
    document.getElementById('comment-edit-' + commentId).style.display = 'block';
    
    // Enfocar el textarea
    const textarea = document.querySelector('#comment-edit-' + commentId + ' textarea');
    if (textarea) {
        textarea.focus();
        textarea.select();
    }
}

function cancelEdit(commentId) {
    // Mostrar contenido del comentario y ocultar formulario de edición
    document.getElementById('comment-content-' + commentId).style.display = 'block';
    document.getElementById('comment-edit-' + commentId).style.display = 'none';
}

// Manejar redirecciones con anclas y efectos visuales
document.addEventListener('DOMContentLoaded', function() {
    // Obtener parámetros de URL
    const urlParams = new URLSearchParams(window.location.search);
    const newCommentId = urlParams.get('new_comment');
    const updatedCommentId = urlParams.get('comment_updated');
    const deletedCommentId = urlParams.get('comment_deleted');
    
    // Si hay un nuevo comentario, hacer scroll suave y resaltar
    if (newCommentId) {
        const newCommentElement = document.getElementById('comment-' + newCommentId);
        if (newCommentElement) {
            // Scroll suave al comentario
            newCommentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Añadir clase de resaltado
            newCommentElement.classList.add('comment-highlight');
            // Remover el resaltado después de 3 segundos
            setTimeout(() => {
                newCommentElement.classList.remove('comment-highlight');
            }, 3000);
        }
    }
    
    // Si hay un comentario actualizado, resaltarlo
    if (updatedCommentId) {
        const updatedCommentElement = document.getElementById('comment-' + updatedCommentId);
        if (updatedCommentElement) {
            updatedCommentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            updatedCommentElement.classList.add('comment-updated');
            setTimeout(() => {
                updatedCommentElement.classList.remove('comment-updated');
            }, 3000);
        }
    }
});

// Función para mostrar notificaciones
function showNotification(message, type) {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Estilos CSS para la notificación
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        transition: all 0.3s ease;
        max-width: 300px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    `;
    
    // Colores según el tipo
    if (type === 'success') {
        notification.style.backgroundColor = '#28a745';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#dc3545';
    }
    
    // Agregar al body
    document.body.appendChild(notification);
    
    // Remover después de 5 segundos
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}
</script>

<style>
.comments-section {
    margin-top: 2rem;
}

.comment-form {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.comment-item {
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.comment-item:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.comment-highlight {
    animation: highlight 3s ease-in-out;
    border-left: 4px solid #28a745;
    background-color: #f8fff8;
}

.comment-updated {
    animation: highlight 3s ease-in-out;
    border-left: 4px solid #ffc107;
    background-color: #fffbf0;
}

@keyframes highlight {
    0% {
        background-color: #e8f5e8;
        transform: scale(1.02);
    }
    50% {
        background-color: #f0f8f0;
        transform: scale(1.01);
    }
    100% {
        background-color: transparent;
        transform: scale(1);
    }
}

.comment-author {
    font-size: 0.9rem;
}

.comment-content {
    font-size: 0.95rem;
    line-height: 1.5;
}

.no-comments {
    background-color: #f8f9fa;
    border-radius: 0.5rem;
}

.comment-actions .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.comment-edit-form {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
}

.comment-edit-form .form-control {
    font-size: 0.9rem;
}

.comment-edit-form .btn-group {
    margin-top: 0.5rem;
}

/* Estilos para notificaciones */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    z-index: 1000;
    transition: all 0.3s ease;
    max-width: 300px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.notification-success {
    background-color: #28a745;
}

.notification-error {
    background-color: #dc3545;
}

/* Animación de entrada para notificaciones */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.notification {
    animation: slideInRight 0.3s ease-out;
}
</style>

