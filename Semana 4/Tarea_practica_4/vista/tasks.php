<?php
// views/tasks.php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
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

        .header h1 {
            color: #333;
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
            gap: 5px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Filtros */
        .filters {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-group label {
            font-size: 0.9em;
            font-weight: 500;
            color: #555;
        }

        select, input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .search-box {
            display: flex;
            gap: 10px;
        }

        .search-box input {
            min-width: 250px;
        }

        /* Grid de tareas */
        .tasks-grid {
            display: grid;
            gap: 20px;
        }

        .task-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .task-card.completed {
            border-left-color: #28a745;
            opacity: 0.8;
        }

        .task-card.overdue {
            border-left-color: #dc3545;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .task-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .task-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7em;
            font-weight: bold;
            text-transform: uppercase;
        }

        .priority-alta { background: #f8d7da; color: #721c24; }
        .priority-media { background: #fff3cd; color: #856404; }
        .priority-baja { background: #d1ecf1; color: #0c5460; }

        .status-completada { background: #d4edda; color: #155724; }
        .status-pendiente { background: #fff3cd; color: #856404; }

        .due-date {
            color: #6c757d;
            font-size: 0.9em;
        }

        .due-date.overdue {
            color: #dc3545;
            font-weight: bold;
        }

        .task-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .task-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8em;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4em;
            margin-bottom: 15px;
            opacity: 0.5;
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
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .filters {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                flex-direction: column;
            }

            .search-box input {
                min-width: auto;
            }

            .task-header {
                flex-direction: column;
                gap: 10px;
            }

            .task-actions {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>📝 Gestión de Tareas</h1>
                <p>Administra y organiza tus tareas de manera eficiente</p>
            </div>
            <div class="nav-buttons">
                <a href="index.php?action=dashboard" class="btn btn-secondary">← Dashboard</a>
                <button class="btn btn-primary" onclick="showCreateModal()">➕ Nueva Tarea</button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters">
            <div class="filter-group">
                <label>Estado</label>
                <select id="filterEstado" onchange="filterTasks()">
                    <option value="">Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="completada">Completada</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Prioridad</label>
                <select id="filterPrioridad" onchange="filterTasks()">
                    <option value="">Todas</option>
                    <option value="alta">Alta</option>
                    <option value="media">Media</option>
                    <option value="baja">Baja</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Fecha Vencimiento</label>
                <input type="date" id="filterFecha" onchange="filterTasks()">
            </div>
            <div class="search-box">
                <div class="filter-group">
                    <label>Buscar</label>
                    <input type="text" id="searchInput" placeholder="Buscar en títulos y descripciones..." onkeyup="searchTasks()">
                </div>
                <button class="btn btn-secondary" onclick="clearFilters()">Limpiar</button>
            </div>
        </div>

        <!-- Lista de Tareas -->
        <div class="tasks-grid" id="tasksContainer">
            <?php if (empty($tasks)): ?>
                <div class="empty-state">
                    <div>📭</div>
                    <h3>No hay tareas</h3>
                    <p>Crea tu primera tarea para comenzar</p>
                    <button class="btn btn-primary" onclick="showCreateModal()" style="margin-top: 15px;">Crear Primera Tarea</button>
                </div>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <?php
                    $isOverdue = $task['fecha_vencimiento'] && strtotime($task['fecha_vencimiento']) < time() && $task['estado'] == 'pendiente';
                    $cardClass = $task['estado'] == 'completada' ? 'completed' : ($isOverdue ? 'overdue' : '');
                    ?>
                    <div class="task-card <?php echo $cardClass; ?>" data-task-id="<?php echo $task['id']; ?>">
                        <div class="task-header">
                            <div>
                                <div class="task-title"><?php echo htmlspecialchars($task['titulo']); ?></div>
                                <div class="task-meta">
                                    <span class="badge priority-<?php echo $task['prioridad']; ?>">
                                        <?php echo ucfirst($task['prioridad']); ?>
                                    </span>
                                    <span class="badge status-<?php echo $task['estado']; ?>">
                                        <?php echo ucfirst($task['estado']); ?>
                                    </span>
                                    <?php if ($user_role == 'admin'): ?>
                                        <span class="badge">Asignada a: <?php echo htmlspecialchars($task['usuario_nombre']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($task['fecha_vencimiento']): ?>
                                        <span class="due-date <?php echo $isOverdue ? 'overdue' : ''; ?>">
                                            📅 <?php echo date('d/m/Y', strtotime($task['fecha_vencimiento'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($task['descripcion'])): ?>
                            <div class="task-description">
                                <?php echo htmlspecialchars($task['descripcion']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="task-actions">
                            <?php if ($task['estado'] == 'pendiente'): ?>
                                <button class="btn btn-success btn-sm" onclick="completeTask(<?php echo $task['id']; ?>)">
                                    ✅ Completar
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" onclick="pendingTask(<?php echo $task['id']; ?>)">
                                    ⏳ Pendiente
                                </button>
                            <?php endif; ?>
                            
                            <button class="btn btn-primary btn-sm" onclick="editTask(<?php echo $task['id']; ?>)">
                                ✏️ Editar
                            </button>
                            
                            <button class="btn btn-danger btn-sm" onclick="deleteTask(<?php echo $task['id']; ?>)">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para Crear/Editar Tarea -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nueva Tarea</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="taskForm" onsubmit="submitTaskForm(event)">
                <input type="hidden" id="taskId" name="id">
                
                <div class="form-group">
                    <label for="titulo">Título *</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" placeholder="Describe los detalles de la tarea..."></textarea>
                </div>

                <?php if ($user_role == 'admin'): ?>
                    <div class="form-group">
                        <label for="usuario_id">Asignar a</label>
                        <select id="usuario_id" name="usuario_id" required>
                            <option value="">Seleccionar usuario...</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>">
                                    <?php echo htmlspecialchars($user['nombre']); ?> (<?php echo $user['email']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo $user_id; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="prioridad">Prioridad</label>
                    <select id="prioridad" name="prioridad">
                        <option value="baja">Baja</option>
                        <option value="media" selected>Media</option>
                        <option value="alta">Alta</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Crear Tarea</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentTasks = <?php echo json_encode($tasks); ?>;

        // Modal functions
        function showCreateModal() {
            document.getElementById('modalTitle').textContent = 'Nueva Tarea';
            document.getElementById('taskForm').reset();
            document.getElementById('taskId').value = '';
            document.getElementById('submitBtn').textContent = 'Crear Tarea';
            document.getElementById('taskModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('taskModal').style.display = 'none';
        }

        function editTask(taskId) {
            const task = currentTasks.find(t => t.id == taskId);
            if (!task) return;

            document.getElementById('modalTitle').textContent = 'Editar Tarea';
            document.getElementById('taskId').value = task.id;
            document.getElementById('titulo').value = task.titulo;
            document.getElementById('descripcion').value = task.descripcion || '';
            document.getElementById('prioridad').value = task.prioridad;
            document.getElementById('fecha_vencimiento').value = task.fecha_vencimiento || '';
            
            <?php if ($user_role == 'admin'): ?>
                document.getElementById('usuario_id').value = task.usuario_id;
            <?php endif; ?>

            document.getElementById('submitBtn').textContent = 'Actualizar Tarea';
            document.getElementById('taskModal').style.display = 'flex';
        }

        // Form submission
        function submitTaskForm(event) {
            event.preventDefault();
            
            const formData = new FormData(document.getElementById('taskForm'));
            const isEdit = formData.get('id') !== '';
            
            fetch(`index.php?action=task_${isEdit ? 'update' : 'create'}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error de conexión: ' + error);
            });
        }

        // Task actions
        function completeTask(taskId) {
            if (!confirm('¿Marcar esta tarea como completada?')) return;
            
            const formData = new FormData();
            formData.append('id', taskId);
            
            fetch('index.php?action=task_complete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function pendingTask(taskId) {
            if (!confirm('¿Marcar esta tarea como pendiente?')) return;
            
            const formData = new FormData();
            formData.append('id', taskId);
            
            fetch('index.php?action=task_pending', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        function deleteTask(taskId) {
            if (!confirm('¿Estás seguro de eliminar esta tarea? Esta acción no se puede deshacer.')) return;
            
            const formData = new FormData();
            formData.append('id', taskId);
            
            fetch('index.php?action=task_delete', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }

        // Filter functions
        function filterTasks() {
            const estado = document.getElementById('filterEstado').value;
            const prioridad = document.getElementById('filterPrioridad').value;
            const fecha = document.getElementById('filterFecha').value;
            
            // En una implementación real, aquí harías una petición al servidor
            // Por ahora solo recargamos la página con los filtros
            const params = new URLSearchParams();
            if (estado) params.append('estado', estado);
            if (prioridad) params.append('prioridad', prioridad);
            if (fecha) params.append('fecha_vencimiento', fecha);
            
            window.location.href = 'index.php?action=task_index&' + params.toString();
        }

        function searchTasks() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                // En una implementación real, aquí harías una petición AJAX
                console.log('Buscando:', searchTerm);
            }
        }

        function clearFilters() {
            document.getElementById('filterEstado').value = '';
            document.getElementById('filterPrioridad').value = '';
            document.getElementById('filterFecha').value = '';
            document.getElementById('searchInput').value = '';
            window.location.href = 'index.php?action=task_index';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('taskModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>