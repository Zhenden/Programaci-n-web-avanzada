<?php
    // views/dashboard.php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../index.php?action=login');
        exit;
    }

    // Controladores
    require_once __DIR__ . '/../controllers/LibroController.php';
    require_once __DIR__ . '/../controllers/PrestamoController.php';
    require_once __DIR__ . '/../controllers/UsuarioController.php';

    $libroCtrl = new LibroController();
    $prestamoCtrl = new PrestamoController();
    $usuarioCtrl = new UsuarioController();

    // Plantilla general
    // Mostrar mensajes de éxito/error
    $roles_map = [
    1 => 'admin',
    2 => 'bibliotecario',
    3 => 'lector'
    ];

    $rol_id = $_SESSION['rol_id'] ?? '3';
    $rol_nombre = $roles_map[$rol_id];
    $nombre = $_SESSION['nombre'] ?? 'Usuario';
    $action = $_GET['action'] ?? 'inicio';

    // Mensajes de sesión
    if (!empty($_SESSION['success'])) {
        echo '<div class="alert success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    if (!empty($_SESSION['error'])) {
        echo '<div class="alert error">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Biblioteca Online</title>
    <style>

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 5px;
            font-weight: bold;
        }
        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .dashboard-container {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: #2c7be5;
            color: #fff;
            padding: 1rem;
        }
        .sidebar h2 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            margin: 0.5rem 0;
            padding: 0.5rem;
            border-radius: 6px;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }
        .content {
            padding: 2rem;
            background: #f4f6f8;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

                /* assets/css/style.css */

        /* ======== Variables ======== */
        :root {
          --color-bg: #f4f6f8;
          --color-primary: #2c7be5;
          --color-secondary: #5e6e82;
          --color-text: #333;
          --color-white: #fff;
          --color-border: #ddd;
          --radius: 8px;
        }

        /* ======== Reset ======== */
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: "Segoe UI", Tahoma, sans-serif;
        }

        body {
          background: var(--color-bg);
          color: var(--color-text);
          line-height: 1.6;
        }

        /* ======== Header y navegación ======== */
        header {
          background: var(--color-primary);
          color: var(--color-white);
          padding: 1rem;
        }

        nav {
          display: flex;
          flex-wrap: wrap;
          gap: 1rem;
        }

        nav a {
          color: var(--color-white);
          text-decoration: none;
          font-weight: 500;
        }

        nav a:hover {
          text-decoration: underline;
        }

        /* ======== Secciones ======== */
        main {
          padding: 2rem;
        }

        section {
          background: var(--color-white);
          padding: 1.5rem;
          border-radius: var(--radius);
          box-shadow: 0 2px 6px rgba(0,0,0,0.1);
          margin-bottom: 2rem;
        }

        /* ======== Botones ======== */
        button, .btn {
          background: var(--color-primary);
          color: var(--color-white);
          border: none;
          padding: 0.6rem 1.2rem;
          border-radius: var(--radius);
          cursor: pointer;
          font-size: 1rem;
          transition: background 0.2s;
        }

        button:hover, .btn:hover {
          background: #1b5fc1;
        }

        /* ======== Tablas ======== */
        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 1rem;
        }

        table th, table td {
          border: 1px solid var(--color-border);
          padding: 0.75rem;
          text-align: left;
        }

        table th {
          background: var(--color-primary);
          color: var(--color-white);
        }

        table tr:nth-child(even) {
          background: #f9fafb;
        }

        /* ======== Formularios ======== */
        form:not(.inline) {
          display: flex;
          flex-direction: column;
          gap: 0.8rem;
          max-width: 400px;
        }


        input, select, textarea {
          padding: 0.6rem;
          border: 1px solid var(--color-border);
          border-radius: var(--radius);
          width: 100%;
        }

        .error {
          color: red;
          margin-bottom: 1rem;
        }

        /* ======== Footer ======== */
        footer {
          text-align: center;
          padding: 1rem;
          background: var(--color-secondary);
          color: var(--color-white);
          position: relative;
          bottom: 0;
          width: 100%;
        }

        /* ======== Responsive ======== */
        @media (max-width: 768px) {
          nav {
            flex-direction: column;
          }
        }

          /* Modal básico */
          .modal {
              position: fixed;
              z-index: 1000;
              left: 0; top: 0;
              width: 100%; height: 100%;
              background: rgba(0,0,0,0.5);
              display: flex;
              justify-content: center;
              align-items: center;
          }
          .modal-content {
              background: #fff;
              padding: 2rem;
              border-radius: 8px;
              width: 400px;
              position: relative;
          }
          .modal .close {
              position: absolute;
              right: 1rem;
              top: 1rem;
              cursor: pointer;
              font-size: 1.2rem;
          }

          td form {
              display: inline !important;
              max-width: unset !important;
              flex-direction: row !important;
              gap: 0 !important;
          }
          td form button {
              display: inline-block !important;
              width: auto !important;
              margin: 0 !important;
              padding: 6px 12px !important;
          }


    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>📚 Biblioteca</h2>
        <p><strong><?php echo ucfirst($rol_nombre); ?></strong></p>
        <a href="/Tarea_practica_5/index.php?action=inicio">🏠 Inicio</a>
        <a href="/Tarea_practica_5/index.php?action=catalogo">📖 Catálogo</a>
        <a href="/Tarea_practica_5/index.php?action=prestamos">📦 Préstamos</a>
        <?php if ($rol_id === 2 || $rol_id === 1): ?>
            <a href="/Tarea_practica_5/index.php?action=agregar_libro">➕ Agregar Libro</a>
        <?php endif; ?>
        <?php if ($rol_id === 1): ?>
            <a href="/Tarea_practica_5/index.php?action=usuarios">👥 Usuarios</a>
        <?php endif; ?>
        <a href="/Tarea_practica_5/index.php?action=logout">🚪 Salir</a>
    </aside>


    <!-- Contenido -->
    <main class="content">
        <div class="topbar">
            <h1>Bienvenido, <?php echo htmlspecialchars($nombre); ?> 👋</h1>
            <small><?php echo ucfirst($rol_nombre); ?></small>
        </div>

        <?php
        switch ($action) {
            case 'inicio':
                echo "<section><h2>Panel Principal</h2><p>Usa el menú de la izquierda para gestionar la biblioteca.</p></section>";
                break;

            case 'catalogo':
                $libros = $libroCtrl->listar();
                echo "<section><h2>Catálogo de Libros</h2><table><tr><th>ID</th><th>Título</th><th>Autor</th><th>Disponible</th><th>Acciones</th></tr>";
                foreach ($libros as $l) {
                    echo "<tr>
                <td>{$l['id']}</td>
                <td>{$l['titulo']}</td>
                <td>{$l['autor']}</td>
                <td>{$l['disponible']}</td>
                <td>";


                  // LECTOR puede solicitar
                  if ($rol_id === 3 && $l['disponible'] > 0) {
                      echo "<form action='/Tarea_practica_5/acciones/solicitar.php' method='POST' style='display:inline;'>
                      <input type='hidden' name='libro_id' value='{$l['id']}'>
                      <button>Solicitar</button>
                      </form>";
                  }


                  // ADMIN + BIBLIOTECARIO pueden editar
                  if ($rol_id === 1 || $rol_id === 2) {
                      echo "<button class='btn btnEditarLibro' 
                        data-id='{$l['id']}'
                        data-titulo='{$l['titulo']}'
                        data-autor='{$l['autor']}'
                        data-disponible='{$l['disponible']}'>Editar</button>";
                  }
                  echo "</td></tr>";
                }
                break;


            case 'prestamos':
                $prestamos = $prestamoCtrl->listarPorUsuario($_SESSION['usuario_id'], $rol_id);
                echo "<section><h2>Historial de Préstamos</h2><table><tr><th>ID</th><th>Libro</th><th>Fecha</th><th>Estado</th><th>Acción</th></tr>";
                foreach ($prestamos as $p) {
                    echo "<tr>
                        <td>{$p['id']}</td>
                        <td>{$p['titulo']}</td>
                        <td>{$p['fecha_prestamo']}</td>
                        <td>{$p['estado']}</td>
                        <td>";
                    if ($p['estado'] === 'prestado') {
                        echo "<form action='/Tarea_practica_5/acciones/devolver.php' method='POST'>
                                <input type='hidden' name='prestamo_id' value='{$p['id']}'>
                                <input type='hidden' name='libro_id' value='{$p['libro_id']}'>
                                <button>Devolver</button>
                              </form>";
                    }
                    echo "</td></tr>";
                }
                echo "</table></section>";
                break;

            case 'agregar_libro':
                if ($rol_id !== 1 && $rol_id !== 2) {
                    echo "<p>No autorizado.</p>";
                    break;
                }
                echo "<section>
                        <h2>Agregar Nuevo Libro</h2>
                        <form action='/Tarea_practica_5/acciones/agregar_libro.php' method='POST'>
                            <label>Título:</label>
                            <input type='text' name='titulo' required>
                            <label>Autor:</label>
                            <input type='text' name='autor' required>
                            <label>Cantidad disponible:</label>
                            <input type='number' name='disponible' min='0' value='1' required>
                            <button type='submit'>Agregar Libro</button>
                        </form>
                      </section>";
            break;

            case 'usuarios':
              if ($rol_id !== 1) {
                  echo "<p>No autorizado.</p>";
                  break;
              }
              $usuarios = $usuarioCtrl->listar();

              // Botón para abrir modal
              echo "<section>
                      <h2>Gestión de Usuarios</h2>
                      <button id='btnNuevoUsuario' class='btn'>➕ Nuevo Usuario</button>
                      <table>
                      <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th></tr>";
              foreach ($usuarios as $u) {
                  $u_rol_nombre = $roles_map[$u['rol_id']] ?? 'lector';
                  echo "<tr>
                          <td>{$u['id']}</td>
                          <td>{$u['nombre']}</td>
                          <td>{$u['email']}</td>
                          <td>{$u_rol_nombre}</td>
                        </tr>";
              }
              echo "</table></section>";
              break;
            }
            
            
            // Modal HTML
            ?>
            <div id="modalUsuario" class="modal" style="display:none;">
              <div class="modal-content">
                <span class="close">&times;</span>
                <h3>Crear Nuevo Usuario</h3>
                <form action="/Tarea_practica_5/acciones/agregar_usuario.php" method="POST">
                  <label>Nombre:</label>
                  <input type="text" name="nombre" required>
                  <label>Email:</label>
                  <input type="email" name="email" required>
                  <label>Contraseña:</label>
                  <input type="password" name="password" required>
                  <label>Rol:</label>
                  <select name="rol_id" required>
                    <option value="1">Admin</option>
                    <option value="2">Bibliotecario</option>
                    <option value="3" selected>Lector</option>
                          </select>
                          <button type="submit">Crear Usuario</button>
                      </form>
                  </div>
              </div>

              <!-- Modal Editar Libro -->
              <div id="modalEditarLibro" class="modal" style="display:none;
                  position:fixed; top:0; left:0; width:100%; height:100%;
                  background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
                  
                  <div style="background:white; padding:20px; border-radius:8px; width:400px;">
                      <h3>Editar Libro</h3>

                      <form id="formEditarLibro" method="POST" action="/Tarea_practica_5/acciones/editar_libro.php">

                          <input type="hidden" name="id" id="edit_id">

                          <label>Título:</label>
                          <input type="text" name="titulo" id="edit_titulo" required>

                          <label>Autor:</label>
                          <input type="text" name="autor" id="edit_autor" required>

                          <label>Disponible:</label>
                          <input type="number" name="disponible" id="edit_disponible" min="0" required>

                          <br><br>

                          <button type="submit" id="btnGuardarEdicion">Guardar Cambios</button>
                          <button type="button" onclick="cerrarModal()">Cancelar</button>

                      </form>
                  </div>
              </div>

<script>
/* ---------- Manejo seguro de modales: Crear Usuario + Editar Libro ---------- */

(function(){
  // ---------- helper safe-get ----------
  function $id(id){ return document.getElementById(id); }

  // ---------- Modal Crear Usuario ----------
  const modalUsuario = $id('modalUsuario');
  const btnNuevoUsuario = $id('btnNuevoUsuario');
  const closeUsuario = modalUsuario ? modalUsuario.querySelector('.close') : null;

  if (btnNuevoUsuario && modalUsuario) {
    btnNuevoUsuario.addEventListener('click', () => {
      modalUsuario.style.display = 'flex';
    });
  }
  if (closeUsuario) {
    closeUsuario.addEventListener('click', () => modalUsuario.style.display = 'none');
  }

  // ---------- Modal Editar Libro ----------
  const modalEditar = $id('modalEditarLibro');
  const closeEditar = modalEditar ? modalEditar.querySelector('.close') : null;

  // Activar botones editar (pueden no existir)
  document.querySelectorAll('.btnEditarLibro').forEach(btn => {
    btn.addEventListener('click', () => {
      if (!modalEditar) return;
      // rellenar campos
      $id('edit_id').value = btn.dataset.id ?? '';
      $id('edit_titulo').value = btn.dataset.titulo ?? '';
      $id('edit_autor').value = btn.dataset.autor ?? '';
      $id('edit_disponible').value = btn.dataset.disponible ?? 0;
      modalEditar.style.display = 'flex';
    });
  });

  if (closeEditar) {
    closeEditar.addEventListener('click', () => modalEditar.style.display = 'none');
  }

  // ---------- Cerrar al hacer click fuera (delegado) ----------
  window.addEventListener('click', (e) => {
    if (modalUsuario && e.target === modalUsuario) modalUsuario.style.display = 'none';
    if (modalEditar && e.target === modalEditar) modalEditar.style.display = 'none';
  });

  // ---------- Protección extra: evitar errores si elementos no existen ----------
  // (no hace nada, solo evita que se rompa si algo falta)
})();
</script>



    </main>
</div>

</body>
</html>
