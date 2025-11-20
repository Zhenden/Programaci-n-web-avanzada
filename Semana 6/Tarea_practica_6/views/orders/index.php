<link rel="stylesheet" href="../assets/css/styles.css">

<div class="card fade-in">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2>Gestión de Pedidos</h2>
        <?php if(SessionManager::get('role_name') !== 'Chef'): ?>
            <a href="index.php?action=order_create" class="btn btn-primary">Nuevo Pedido</a>
        <?php endif; ?>
    </div>
    
    <?php if($orders && $orders->num_rows > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Plato</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['username'] ?? SessionManager::get('username', '')) ?></td>
                            <td><?= htmlspecialchars($row['dish_name']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if(in_array(SessionManager::get('role_name'), ['Administrator','Chef','Waiter'])): ?>
                                    <form action="index.php?action=order_update_status" method="POST" class="status-form">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <select name="status" class="form-control form-control-sm">
                                            <option value="Pending" <?= $row['status']==='Pending'?'selected':'' ?>>Pendiente</option>
                                            <option value="Preparing" <?= $row['status']==='Preparing'?'selected':'' ?>>Preparando</option>
                                            <option value="Ready to serve" <?= $row['status']==='Ready to serve'?'selected':'' ?>>Listo para servir</option>
                                            <option value="Served" <?= $row['status']==='Served'?'selected':'' ?>>Servido</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Actualizar</button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if(SessionManager::get('role_name')==='Administrator'): ?>
                                    <a href="index.php?action=order_delete&id=<?= htmlspecialchars($row['id']) ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('¿Estás seguro de eliminar este pedido?')">
                                        Eliminar
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <p>No hay pedidos registrados.</p>
        </div>
    <?php endif; ?>
</div>
