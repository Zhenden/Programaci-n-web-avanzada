<?php
// vista/dashboard.php

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

require_once __DIR__ . '/../BD/conexion.php';
require_once __DIR__ . '/../modelos/Tarea.php';
require_once __DIR__ . '/../modelos/Usuario.php';

$database = new Database();
$db = $database->getConnection();

$tareaModel = new Tarea($db);
$usuarioModel = new Usuario($db);

$currentUserId = (int)$_SESSION['user_id'];
$currentUserRole = (int)$_SESSION['user_role']; // 1=Admin, 2=Gerente, 3=Miembro
$isAdmin = ($currentUserRole === 1);

// Filtros básicos
$filterUser = $isAdmin && isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null;
$filterEstado = $_GET['estado'] ?? '';

$filters = [];
if (!$isAdmin)              $filters['usuario_id'] = $currentUserId;
elseif ($filterUser)        $filters['usuario_id'] = $filterUser;
if ($filterEstado)          $filters['estado'] = $filterEstado;

$tareas = $tareaModel->getAll($filters);
$usuarios = $isAdmin ? $usuarioModel->getAll() : [];
$stats = $tareaModel->getStats($isAdmin ? $filterUser : $currentUserId);
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Dashboard - Gestión de Tareas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/Clase_practica_5/css/estilo.css">

</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php?action=dashboard">Gestión de Tareas</a>
        <div class="text-white ms-auto me-3">
            <?= htmlspecialchars($_SESSION['user_name']) ?> 
            <span class="badge bg-secondary"><?= $isAdmin ? 'Admin' : ($currentUserRole == 2 ? 'Gerente' : 'Miembro') ?></span>
          </div>
          <?php if ($isAdmin): ?>
          <button class="btn btn-outline-info btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalUsuarios">👥 Administrar usuarios</button>
      <?php endif; ?>
        <a class="btn btn-outline-light btn-sm" href="index.php?action=logout">Cerrar sesión</a>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Listado de tareas</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearTarea">+ Nueva tarea</button>
    </div>

    <!-- Estadísticas -->
    <div class="row text-center mb-4">
        <div class="col-md-4">
            <div class="card border-primary"><div class="card-body">
                <h6>Total</h6><h2><?= $stats['total'] ?? 0 ?></h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning"><div class="card-body">
                <h6>Pendientes</h6><h2><?= $stats['pendientes'] ?? 0 ?></h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-success"><div class="card-body">
                <h6>completados</h6><h2><?= $stats['completados'] ?? 0 ?></h2>
            </div></div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Asignado a</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$tareas): ?>
                    <tr><td colspan="6" class="text-center text-muted">No hay tareas</td></tr>
                <?php endif; ?>
                <?php foreach ($tareas as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td><?= htmlspecialchars($t['titulo']) ?></td>
                        <td><?= nl2br(htmlspecialchars($t['descripcion'])) ?></td>
                        <td><?= htmlspecialchars($t['usuario_nombre'] ?? '—') ?></td>
                        <td>
                        <span class="badge estado-badge <?= $t['estado'] == 'completado' ? 'bg-success' : 'bg-warning' ?>">
                            <?= ucfirst($t['estado']) ?>
                        </span>
                        </td>


                        <td>
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="editarTarea(<?= $t['id'] ?>, '<?= htmlspecialchars(addslashes($t['titulo'])) ?>', '<?= htmlspecialchars(addslashes($t['descripcion'])) ?>', <?= $t['usuario_id'] ?? 'null' ?>, '<?= $t['estado'] ?>')">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="eliminarTarea(<?= $t['id'] ?>)">
                                Eliminar
                            </button>
                            <?php if ($t['estado'] == 'pendiente'): ?>
                                <button class="btn btn-sm btn-outline-success"
                                        onclick="cambiarEstado(<?= $t['id'] ?>, 'completado')">
                                    Completar
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-warning"
                                        onclick="cambiarEstado(<?= $t['id'] ?>, 'pendiente')">
                                    Reabrir
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal CREAR -->
<div class="modal fade" id="modalCrearTarea" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formCrearTarea">
        <div class="modal-header">
          <h5 class="modal-title">Crear nueva tarea</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control"></textarea>
          </div>
          <?php if ($isAdmin): ?>
          <div class="mb-3">
            <label class="form-label">Asignar a usuario</label>
            <select name="usuario_id" class="form-select" required>
              <option value="">-- Seleccionar --</option>
              <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>
          <input type="hidden" name="estado" value="pendiente">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal EDITAR -->
<div class="modal fade" id="modalEditarTarea" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEditarTarea">
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title">Editar tarea</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" id="edit_titulo" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" id="edit_descripcion" class="form-control"></textarea>
          </div>
          <?php if ($isAdmin): ?>
          <div class="mb-3">
            <label class="form-label">Asignado a</label>
            <select name="usuario_id" id="edit_usuario" class="form-select">
              <option value="">-- Seleccionar --</option>
              <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================= -->
<!-- 💼 BLOQUE ADMIN (solo Admin) -->
<!-- ========================= -->
 
<?php if ($isAdmin): ?>
<div class="modal fade" id="modalUsuarios" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">Administrar usuarios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <button class="btn btn-sm btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">+ Nuevo usuario</button>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-secondary">
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                  <select class="form-select form-select-sm" onchange="cambiarRol(<?= $u['id'] ?>, this.value)">
                    <option value="1" <?= $u['rol_id'] == 1 ? 'selected' : '' ?>>Admin</option>
                    <option value="2" <?= $u['rol_id'] == 2 ? 'selected' : '' ?>>Gerente</option>
                    <option value="3" <?= $u['rol_id'] == 3 ? 'selected' : '' ?>>Miembro</option>
                  </select>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(<?= $u['id'] ?>)">Eliminar</button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formCrearUsuario">
        <div class="modal-header">
          <h5 class="modal-title">Crear nuevo usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="contraseña" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Rol</label>
            <select name="rol_id" class="form-select">
              <option value="1">Admin</option>
              <option value="2">Gerente</option>
              <option value="3" selected>Miembro</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// --- Crear tarea ---
document.getElementById('formCrearTarea').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('index.php?action=task_create', { method: 'POST', body: formData });
    const data = await res.json();
    alert(data.message);
    if (data.success) location.reload();
});

// --- Editar tarea ---
function editarTarea(id, titulo, descripcion, usuario_id, estado) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_titulo').value = titulo;
    document.getElementById('edit_descripcion').value = descripcion;
    const userSel = document.getElementById('edit_usuario');
    if (userSel) userSel.value = usuario_id;
    new bootstrap.Modal('#modalEditarTarea').show();
}

document.getElementById('formEditarTarea').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await fetch('index.php?action=task_update', { method: 'POST', body: formData });
    const data = await res.json();
    alert(data.message);
    if (data.success) location.reload();
});

// --- Eliminar tarea ---
async function eliminarTarea(id) {
    if (!confirm('¿Seguro que deseas eliminar esta tarea?')) return;
    const formData = new FormData();
    formData.append('id', id);
    const res = await fetch('index.php?action=task_delete', { method: 'POST', body: formData });
    const data = await res.json();
    alert(data.message);
    if (data.success) location.reload();
}

// --- Completar o reabrir tarea (sin recargar) ---
async function cambiarEstado(id, nuevoEstado) {
    const formData = new FormData();
    formData.append('id', id);

    const action = nuevoEstado === 'completado' ? 'task_complete' : 'task_pending';
    const res = await fetch(`index.php?action=${action}`, { method: 'POST', body: formData });
    const data = await res.json();

    alert(data.message);
    if (!data.success) return;

    // ✅ Actualiza dinámicamente la fila
    const fila = Array.from(document.querySelectorAll('tr')).find(tr => tr.firstElementChild?.textContent == id);
    if (!fila) return;

    const badge = fila.querySelector('.estado-badge');
    const btnCambiar = Array.from(fila.querySelectorAll('button')).find(b => 
        b.textContent.trim() === 'Completar' || b.textContent.trim() === 'Reabrir'
    );

    if (nuevoEstado === 'completado') {
        badge.classList.remove('bg-warning');
        badge.classList.add('bg-success');
        badge.textContent = 'completado';

        btnCambiar.textContent = 'Reabrir';
        btnCambiar.classList.remove('btn-outline-success');
        btnCambiar.classList.add('btn-outline-warning');
        btnCambiar.setAttribute('onclick', `cambiarEstado(${id}, 'pendiente')`);
    } else {
        badge.classList.remove('bg-success');
        badge.classList.add('bg-warning');
        badge.textContent = 'Pendiente';

        btnCambiar.textContent = 'Completar';
        btnCambiar.classList.remove('btn-outline-warning');
        btnCambiar.classList.add('btn-outline-success');
        btnCambiar.setAttribute('onclick', `cambiarEstado(${id}, 'completado')`);
    }
    location.reload();
}

// --- Funciones de administración de usuarios ---
async function cambiarRol(id, nuevoRol) {
  const formData = new FormData();
  formData.append('id', id);
  formData.append('rol_id', nuevoRol);
  const res = await fetch('index.php?action=user_role_update', { method: 'POST', body: formData });
  const data = await res.json();
  alert(data.message);
}

async function eliminarUsuario(id) {
  if (!confirm('¿Seguro que deseas eliminar este usuario?')) return;
  const formData = new FormData();
  formData.append('id', id);
  const res = await fetch('index.php?action=user_delete', { method: 'POST', body: formData });
  const data = await res.json();
  alert(data.message);
  if (data.success) location.reload();
}

document.getElementById('formCrearUsuario')?.addEventListener('submit', async e => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const res = await fetch('index.php?action=user_create', { method: 'POST', body: formData });
  const data = await res.json();
  alert(data.message);
  if (data.success) location.reload();
});
</script>

</body>
</html>
