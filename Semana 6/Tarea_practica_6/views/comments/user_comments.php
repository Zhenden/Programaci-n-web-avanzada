   <style>
    :root {
        --rojo-primario: #e74c3c;
        --rojo-oscuro: #c0392b;
        --naranja-principal: #e67e22;
        --naranja-claro: #f39c12;
        --negro: #1a1a1a;
        --negro-claro: #2c3e50;
        --gris-oscuro: #34495e;
        --blanco: #ecf0f1;
        --gris-texto: #bdc3c7;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .mt-4 {
        margin-top: 2rem;
    }
    
    .mb-4 {
        margin-bottom: 2rem;
    }
    
    .d-flex {
        display: flex;
    }
    
    .justify-content-between {
        justify-content: space-between;
    }
    
    .align-items-center {
        align-items: center;
    }
    
    .align-items-start {
        align-items: flex-start;
    }
    
    .card {
        background: var(--negro-claro);
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, var(--rojo-primario) 0%, var(--naranja-principal) 100%);
        color: var(--blanco);
        padding: 1.5rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .card-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .card-body {
        padding: 2rem;
    }
    
    h1 {
        color: var(--blanco);
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
        text-align: center;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, var(--gris-oscuro) 0%, var(--negro-claro) 100%);
        color: var(--blanco);
        box-shadow: 0 4px 15px rgba(52, 73, 94, 0.4);
    }
    
    .btn-secondary:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(52, 73, 94, 0.6);
        color: var(--blanco);
        text-decoration: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--rojo-primario) 0%, var(--naranja-principal) 100%);
        color: var(--blanco);
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
    }
    
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(231, 76, 60, 0.6);
        color: var(--blanco);
        text-decoration: none;
    }
    
    .btn-outline-danger {
        background: transparent;
        border: 2px solid var(--rojo-oscuro);
        color: var(--rojo-oscuro);
        padding: 0.4rem 0.8rem;
    }
    
    .btn-outline-danger:hover {
        background: var(--rojo-oscuro);
        color: var(--blanco);
        transform: translateY(-2px);
    }
    
    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
    }
    
    .text-center {
        text-align: center;
    }
    
    .text-muted {
        color: var(--gris-texto) !important;
    }
    
    .py-4 {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
    
    .mb-1 {
        margin-bottom: 0.5rem;
    }
    
    .mb-2 {
        margin-bottom: 1rem;
    }
    
    .mb-3 {
        margin-bottom: 1.5rem;
    }
    
    .p-3 {
        padding: 1.5rem;
    }
    
    .border {
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    
    .rounded {
        border-radius: 8px;
    }
    
    .d-inline {
        display: inline;
    }
    
    /* Lista de comentarios */
    .comments-list {
        margin-top: 1rem;
    }
    
    .comment-item {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .comment-item:hover {
        box-shadow: 0 4px 15px rgba(231, 76, 60, 0.2);
        transform: translateY(-2px);
        border-color: rgba(231, 76, 60, 0.3);
    }
    
    .comment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }
    
    .dish-info h5 {
        color: var(--naranja-claro);
        margin-bottom: 0.5rem;
        font-size: 1.2rem;
        font-weight: 600;
    }
    
    .dish-info a {
        color: var(--naranja-claro);
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .dish-info a:hover {
        color: var(--naranja-principal);
        text-decoration: none;
        transform: translateX(5px);
    }
    
    .dish-info small {
        color: var(--gris-texto);
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .comment-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .comment-content {
        color: var(--blanco);
        line-height: 1.6;
        font-size: 1rem;
        background-color: rgba(255, 255, 255, 0.02);
        padding: 1rem;
        border-radius: 6px;
        border-left: 3px solid var(--rojo-primario);
    }
    
    /* Estado vacío */
    .text-center i {
        color: var(--gris-texto);
        margin-bottom: 1rem;
    }
    
    .text-center p {
        color: var(--gris-texto);
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }
    
    /* Utilidades para iconos */
    .fa-utensils, .fa-clock, .fa-trash, .fa-arrow-left, .fa-comments {
        opacity: 0.9;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .comment-header {
            flex-direction: column;
            gap: 1rem;
        }
        
        .comment-actions {
            align-self: flex-end;
        }
        
        h1 {
            font-size: 2rem;
        }
        
        .d-flex {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .justify-content-between {
            justify-content: flex-start;
        }
    }
    </style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-comments"></i> Mis Comentarios</h1>
        <a href="index.php?action=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Comentarios Realizados</h2>
        </div>
        <div class="card-body">
            <?php if (empty($comments)): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-comments fa-3x mb-3"></i>
                    <p>No has realizado ningún comentario aún.</p>
                    <a href="index.php?action=dishes" class="btn btn-primary">
                        <i class="fas fa-utensils"></i> Explorar Platos
                    </a>
                </div>
            <?php else: ?>
                <div class="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item">
                            <div class="comment-header">
                                <div class="dish-info">
                                    <h5 class="mb-1">
                                        <a href="index.php?action=dish&id=<?= htmlspecialchars($comment['dish_id']) ?>">
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
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar comentario">
                                            <i class="fas fa-trash"></i> Eliminar
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

<script>
// Efectos adicionales de interactividad
document.addEventListener('DOMContentLoaded', function() {
    // Añadir efecto de carga suave
    const commentItems = document.querySelectorAll('.comment-item');
    commentItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.5s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Confirmación mejorada para eliminar
    const deleteForms = document.querySelectorAll('form[action*="comments/delete"]');
    deleteForms.forEach(form => {
        form.onsubmit = function(e) {
            e.preventDefault();
            
            if (confirm('¿Estás seguro de que quieres eliminar este comentario?\nEsta acción no se puede deshacer.')) {
                // Añadir efecto visual antes de enviar
                const commentItem = form.closest('.comment-item');
                if (commentItem) {
                    commentItem.style.transition = 'all 0.3s ease';
                    commentItem.style.opacity = '0';
                    commentItem.style.transform = 'translateX(100px)';
                    
                    setTimeout(() => {
                        form.submit();
                    }, 300);
                } else {
                    form.submit();
                }
            }
            return false;
        };
    });
    
    // Efecto hover mejorado para enlaces de platos
    const dishLinks = document.querySelectorAll('.dish-info a');
    dishLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});
</script>