<link rel="stylesheet" href="../assets/css/styles.css">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Comentarios por Plato</h1>
        <a href="index.php?action=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <!-- Filtro por plato -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="index.php" class="form-inline">
                <input type="hidden" name="action" value="comments">
                
                <div class="form-group mr-3">
                    <label for="dish_id" class="mr-2">Filtrar por plato:</label>
                    <select name="dish_id" id="dish_id" class="form-control">
                        <option value="">-- Todos los platos --</option>
                        <?php foreach ($dishes as $dish): ?>
                            <option value="<?= htmlspecialchars($dish['id']) ?>" 
                                    <?= (isset($selected_dish) && $selected_dish == $dish['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dish['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </form>
        </div>
    </div>
    
    <!-- Lista de comentarios -->
    <div class="card">
        <div class="card-header">
            <h2>Comentarios</h2>
        </div>
        <div class="card-body">
            <?php if (empty($comments)): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-comments fa-3x mb-3"></i>
                    <p>No hay comentarios para mostrar.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Comentario</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comments as $comment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($comment['id']) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($comment['username']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($comment['email']) ?></small>
                                    </td>
                                    <td>
                                        <div class="comment-text">
                                            <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <form method="POST" action="index.php?action=comments/delete" 
                                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar este comentario?')">
                                            <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id']) ?>">
                                            <input type="hidden" name="csrf_token" value="<?= SessionManager::get('csrf_token') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar comentario">
                                                <i class="fas fa-trash">Eliminar</i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
