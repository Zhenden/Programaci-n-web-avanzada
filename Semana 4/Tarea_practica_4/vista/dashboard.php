<?php
// vista/dashboard.php - VERSIÓN FINAL LIMPIA Y CORREGIDA

// NOTA: NO incluir session_start(), require_once ni lógica PHP aquí
// Todos los datos deben ser pasados desde el controlador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Tareas</title>
    <style>
        /* === ESTILOS COMPLETOS Y ORGANIZADOS === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .user-details h1 {
            margin-bottom: 5px;
            color: #333;
            font-size: 1.5em;
        }

        .user-role {
            background: #e9ecef;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            color: #6c757d;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-total { color: #6c757d; }
        .stat-completed { color: #28a745; }
        .stat-pending { color: #ffc107; }
        .stat-overdue { color: #dc3545; }

        .stat-label {
            font-size: 0.9em;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sections-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .sections-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .nav-buttons {
                width: 100%;
                justify-content: center;
            }
        }

        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section h2 {
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 10px;
            font-size: 1.3em;
        }

        .task-list {
            list-style: none;
        }

        .task-item {
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .task-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.1);
            transform: translateX(5px);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 8px;
        }

        .task-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 1.1em;
        }

        .task-meta {
            display: flex;
            gap: 10px;
            font-size: 0.8em;
            color: #6c757d;
            flex-wrap: wrap;
        }

        .priority {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7em;
            font-weight: bold;
        }

        .priority-alta { background: #f8d7da; color: #721c24; }
        .priority-media { background: #fff3cd; color: #856404; }
        .priority-baja { background: #d1ecf1; color: #0c5460; }

        .status {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7em;
            font-weight: bold;
        }

        .status-completada { background: #d4edda; color: #155724; }
        .status-pendiente { background: #fff3cd; color: #856404; }

        .task-due {
            color: #dc3545;
            font-weight: bold;
        }

        .task-description {
            color: #666;
            font-size: 0.9em;
            margin-top: 8px;
            line-height: 1.4;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3em;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 20px;
        }

        .action-btn {
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 0.9em;
        }

        .action-btn:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
            transform: translateY(-2px);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #333;
            font-size: 1.4em;
        }

        .close {
            font-size: 24px;
            cursor: pointer;
            color: #6c757d;
        }

        .close:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        /* Mensajes flash */
        .flash-message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .flash-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .flash-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- === HEADER === -->
        <div class="header">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo isset($user_name) ? htmlspecialchars(strtoupper(substr($user_name, 0, 1))) : 'U'; ?>
                </div>
                <div class="user-details">
                    <h1>Hola, <?php echo htmlspecialchars($user_name ?? 'Usuario'); ?></h1>
                </div>
            </div>
            
            <div class="nav-buttons">
                <!-- === BOTÓN GESTIONAR TAREAS CORREGIDO === -->
                <a href="index.php?action=task_index" class="btn btn-primary">📝 Gestionar Tareas</a>
                
                <a href="index.php?action=logout" class="btn btn-danger">🚪 Cerrar Sesión</a>
            </div>
        </div>

        <!-- === MENSAJES FLASH === -->
        <?php if (!empty($message)): ?>
            <div class="flash-message flash-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="flash-message flash-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- === ESTADÍSTICAS === -->
        <div class="stats-grid">
            <div class="stat-card" onclick="filterByType('all')">
                <div class="stat-number stat-total"><?php echo $stats['total'] ?? 0; ?></div>
                <div class="stat-label">Total de Tareas</div>
            </div>
            <div class="stat-card" onclick="filterByType('completed')">
                <div class="stat-number stat-completed"><?php echo $stats['completadas'] ?? 0; ?></div>
                <div class="stat-label">Completadas</div>
            </div>
            <div class="stat-card" onclick="filterByType('pending')">
                <div class="stat-number stat-pending"><?php echo $stats['pendientes'] ?? 0; ?></div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-card" onclick="filterByType('overdue')">
                <div class="stat-number stat-overdue"><?php echo $stats['vencidas'] ?? 0; ?></div>
                <div class="stat-label">Vencidas</div>
            </div>
        </div>

        <!-- === SECCIONES PRINCIPALES === -->
        <div class="sections-grid">
            <!-- Tareas próximas a vencer -->
            <div class="section">
                <h2>📅 Tareas Próximas a Vencer</h2>
                <?php if (!empty($upcoming_tasks)): ?>
                    <ul class="task-list">
                        <?php foreach ($upcoming_tasks as $task_item): ?>
                            <li class="task-item" onclick="viewTask(<?php echo $task_item['id']; ?>)">
                                <div class="task-header">
                                    <div>
                                        <div class="task-title"><?php echo htmlspecialchars($task_item['titulo']); ?></div>
                                        <div class="task-meta">
                                            <span class="priority priority-<?php echo $task_item['prioridad']; ?>">
                                                <?php echo ucfirst($task_item['prioridad']); ?>
                                            </span>
                                            <span class="task-due">
                                                📅 <?php echo date('d/m/Y', strtotime($task_item['fecha_vencimiento'])); ?>
                                            </span>
                                            <?php if (($user_role ?? '') == 'admin'): ?>
                                                <span>👤 <?php echo htmlspecialchars($task_item['usuario_nombre']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($task_item['descripcion'])): ?>
                                    <div class="task-description">
                                        <?php echo htmlspecialchars(substr($task_item['descripcion'], 0, 100)); ?>...
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <div>🎉</div>
                        <p>No hay tareas próximas a vencer</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tareas recientes -->
            <div class="section">
                <h2>📝 Tareas Recientes</h2>
                <?php if (!empty($recent_tasks)): ?>
                    <ul class="task-list">
                        <?php foreach ($recent_tasks as $task_item): ?>
                            <li class="task-item" onclick="viewTask(<?php echo $task_item['id']; ?>)">
                                <div class="task-header">
                                    <div>
                                        <div class="task-title"><?php echo htmlspecialchars($task_item['titulo']); ?></div>
                                        <div class="task-meta">
                                            <span class="status status-<?php echo $task_item['estado']; ?>">
                                                <?php echo ucfirst($task_item['estado']); ?>
                                            </span>
                                            <span class="priority priority-<?php echo $task_item['prioridad']; ?>">
                                                <?php echo ucfirst($task_item['prioridad']); ?>
                                            </span>
                                            <?php if (($user_role ?? '') == 'admin'): ?>
                                                <span>👤 <?php echo htmlspecialchars($task_item['usuario_nombre']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($task_item['descripcion'])): ?>
                                    <div class="task-description">
                                        <?php echo htmlspecialchars(substr($task_item['descripcion'], 0, 100)); ?>...
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <div>📭</div>
                        <p>No hay tareas recientes</p>
                        <button class="action-btn" onclick="showQuickTaskModal()">➕ Crear Tarea</button>
                    </div>
                <?php endif; ?>

                <!-- Acciones rápidas -->
                <div class="quick-actions">
                    <a href="index.php?action=task_index" class="action-btn">📋 Ver Todas las Tareas</a>
                    <a href="index.php?action=task_index&estado=pendiente" class="action-btn">⏳ Tareas Pendientes</a>
                    <a href="index.php?action=task_index&estado=completada" class="action-btn">✅ Tareas Completadas</a>
                    <button class="action-btn" onclick="showCreateTaskModal()">➕ Nueva Tarea</button>
                </div>
            </div>
        </div>

        <!-- Leyenda de prioridades -->
        <div class="section">
            <h2>🎯 Leyenda de Prioridades</h2>
            <div style="display: flex; gap: 15px; margin-top: 15px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 15px; height: 15px; background: #f8d7da; border-radius: 3px;"></div>
                    <span>Alta</span>
                </div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 15px; height: 15px; background: #fff3cd; border-radius: 3px;"></div>
                    <span>Media</span>
                </div>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <div style="width: 15px; height: 15px; background: #d1ecf1; border-radius: 3px;"></div>
                    <span>Baja</span>
                </div>
            </div>
        </div>

        <!-- Modal para crear tarea rápida -->
        <div id="quickTaskModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>➕ Nueva Tarea Rápida</h2>
                    <span class="close" onclick="closeQuickTaskModal()">&times;</span>
                </div>
                <form id="quickTaskForm" onsubmit="submitQuickTask(event)">
                    <div class="form-group">
                        <label for="quickTitulo">Título *</label>
                        <input type="text" id="quickTitulo" name="titulo" required placeholder="¿Qué necesitas hacer?">
                    </div>

                    <div class="form-group">
                        <label for="quickPrioridad">Prioridad</label>
                        <select id="quickPrioridad" name="prioridad">
                            <option value="baja">Baja</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quickFecha">Fecha de Vencimiento</label>
                        <input type="date" id="quickFecha" name="fecha_vencimiento">
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeQuickTaskModal()">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear Tarea</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<!-- Modal rápido -->
<div id="quickTaskModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>➕ Nueva Tarea Rápida</h2>
            <span class="close" onclick="closeQuickTaskModal()">&times;</span>
        </div>
        <form id="quickTaskForm" onsubmit="submitQuickTask(event)">
            <div class="form-group">
                <label for="quickTitulo">Título *</label>
                <input type="text" id="quickTitulo" name="titulo" required placeholder="¿Qué necesitas hacer?">
            </div>

            <div class="form-group">
                <label for="quickPrioridad">Prioridad</label>
                <select id="quickPrioridad" name="prioridad">
                    <option value="baja">Baja</option>
                    <option value="media" selected>Media</option>
                    <option value="alta">Alta</option>
                </select>
            </div>

            <div class="form-group">
                <label for="quickFecha">Fecha de Vencimiento</label>
                <input type="date" id="quickFecha" name="fecha_vencimiento">
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeQuickTaskModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear Tarea</button>
            </div>
        </form>
    </div>
</div>

    <script>
        // === JAVASCRIPT COMPLETO Y CORREGIDO ===

        // Funcionalidad de las tarjetas de estadísticas
        function filterByType(type) {
            let url = 'index.php?action=task_index';
            
            switch(type) {
                case 'completed':
                    url += '&estado=completada';
                    break;
                case 'pending':
                    url += '&estado=pendiente';
                    break;
                case 'overdue':
                    url += '&filtro=vencidas';
                    break;
                default:
                    break;
            }
            
            window.location.href = url;
        }

            // Ver tarea (redirige a la página de tareas)
            function viewTask(taskId) {
                window.location.href = `index.php?action=task_index&ver=${taskId}`;
            }

        function showCreateTaskModal() {
            document.getElementById('quickTaskModal').style.display = 'flex';
            document.getElementById('quickTitulo').focus();
        }

        function closeQuickTaskModal() {
            document.getElementById('quickTaskModal').style.display = 'none';
            document.getElementById('quickTaskForm').reset();
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('quickTaskModal');
            if (event.target === modal) {
                closeQuickTaskModal();
            }
        }

        // Enviar tarea rápida (AJAX CORREGIDO)
        function submitQuickTask(event) {
        event.preventDefault();

        const formData = new FormData(document.getElementById('quickTaskForm'));

        // Agregar usuario_id si no es admin
        <?php if (($user_role ?? '') != 'admin'): ?>
            formData.append('usuario_id', '<?php echo $user_id ?? ''; ?>');
        <?php endif; ?>

        fetch('index.php?action=task_create', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeQuickTaskModal();
                window.location.reload();
            } else {
                alert('❌ Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error de conexión');
        });
        }

        // Atajos de teclado
        document.addEventListener('keydown', function(event) {
            // Ctrl + N para nueva tarea rápida
            if (event.ctrlKey && event.key === 'n') {
                event.preventDefault();
                showQuickTaskModal();
            }
            
            // Escape para cerrar modal
            if (event.key === 'Escape') {
                closeQuickTaskModal();
            }
        });

        // Mostrar notificación de bienvenida
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('login') === 'success') {
                setTimeout(() => {
                    alert('🎉 ¡Bienvenido de nuevo, <?php echo htmlspecialchars($user_name ?? ''); ?>!');
                }, 500);
            }
        });

        // Actualizar estadísticas cada 2 minutos
        setInterval(() => {
            console.log('Actualizando estadísticas...');
            // En producción, aquí iría una petición AJAX real
        }, 120000);
    </script>
</body>
</html>