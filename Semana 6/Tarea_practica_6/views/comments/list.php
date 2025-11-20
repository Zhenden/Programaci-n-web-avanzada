<link rel="stylesheet" href="../assets/css/styles.css">

<div id="comments-section" class="comments-section mt-4">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-comments"></i> Comentarios</h3>
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
                <div class="comment-form">
                    <form method="POST" action="index.php?action=comments/create" class="comment-form">
                        <input type="hidden" name="dish_id" value="<?= htmlspecialchars($dish['id'] ?? $_GET['dish_id'] ?? '') ?>">
                        <input type="hidden" name="csrf_token" value="<?= SessionManager::get('csrf_token') ?>">
                        <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '') ?>">
                        <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'] ?? '') ?>">
                        
                        <div class="form-group">
                            <label for="comment"><i class="fas fa-edit"></i> Agregar comentario:</label>
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
                            <i class="fas fa-comment"></i> Publicar Comentario
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
                    <div class="no-comments">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>No hay comentarios aún. Sé el primero en comentar.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div id="comment-<?= htmlspecialchars($comment['id']) ?>" class="comment-item" data-comment-id="<?= htmlspecialchars($comment['id']) ?>">
                            <div class="comment-header">
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
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="editComment(<?= htmlspecialchars($comment['id']) ?>)"
                                                    title="Editar comentario">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteComment(<?= htmlspecialchars($comment['id']) ?>)"
                                                title="Eliminar comentario">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="comment-content" id="comment-content-<?= htmlspecialchars($comment['id']) ?>">
                                <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                            </div>
                            
                            <!-- Formulario de edición (oculto por defecto) -->
                            <div id="comment-edit-<?= htmlspecialchars($comment['id']) ?>" class="comment-edit-form" style="display: none;">
                                <form method="POST" action="index.php?action=comments/update">
                                    <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                    <input type="hidden" name="csrf_token" value="<?= SessionManager::get('csrf_token') ?>">
                                    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '') ?>">
                                    <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort'] ?? '') ?>">
                                    
                                    <div class="form-group">
                                        <label for="edit-comment-<?= htmlspecialchars($comment['id']) ?>">Editar comentario:</label>
                                        <textarea 
                                            name="comment" 
                                            id="edit-comment-<?= htmlspecialchars($comment['id']) ?>"
                                            class="form-control" 
                                            rows="3" 
                                            maxlength="1000" 
                                            required
                                        ><?= htmlspecialchars($comment['comment']) ?></textarea>
                                        <small class="form-text text-muted">Máximo 1000 caracteres</small>
                                    </div>
                                    
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save"></i> Guardar
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit(<?= htmlspecialchars($comment['id']) ?>)">
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
            
            // Mostrar notificación
            showNotification('¡Comentario publicado exitosamente!', 'success');
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
            
            // Mostrar notificación
            showNotification('Comentario actualizado correctamente', 'success');
        }
    }
    
    // Si se eliminó un comentario
    if (deletedCommentId) {
        showNotification('Comentario eliminado correctamente', 'success');
    }
});

// Función para mostrar notificaciones
function showNotification(message, type) {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Agregar al body
    document.body.appendChild(notification);
    
    // Remover después de 5 segundos
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 5000);
}

// Contador de caracteres para el textarea
document.addEventListener('DOMContentLoaded', function() {
    const commentTextarea = document.getElementById('comment');
    if (commentTextarea) {
        // Crear contador
        const counter = document.createElement('div');
        counter.className = 'form-text';
        counter.style.textAlign = 'right';
        counter.style.marginTop = '0.25rem';
        commentTextarea.parentNode.appendChild(counter);
        
        // Actualizar contador
        function updateCounter() {
            const length = commentTextarea.value.length;
            counter.textContent = `${length}/1000 caracteres`;
            counter.style.color = length > 900 ? '#e74c3c' : '#bdc3c7';
        }
        
        // Event listeners
        commentTextarea.addEventListener('input', updateCounter);
        commentTextarea.addEventListener('focus', updateCounter);
        
        // Inicializar
        updateCounter();
    }
});
</script>