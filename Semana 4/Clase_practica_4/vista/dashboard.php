    <?php
    require_once '../controlador/crud_usuarios.php';
    require_once '../controlador/crud_notas.php';
    require_once '../controlador/funciones.php';
    require_once '../controlador/Auth.php';

    $auth = new Auth();
    $auth->requireAuth();
    $usuario_info = $auth->getUserInfo();

    $sistemaPerfiles = new SistemaPerfiles();
    $crudUsuarios = new CRUDUsuarios();
    $crudNotas = new CRUDNotas();
    $usuario_actual_id = $usuario_info['id'];

    $perfil = $sistemaPerfiles->obtenerPerfil($usuario_actual_id);
    $modulos_accesibles = $sistemaPerfiles->obtenerModulosAccesibles($usuario_actual_id);
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Sistema de Calificaciones</title>
        <style>
            .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
            .card { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
            .btn { padding: 8px 16px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; }
            .btn-primary { background: #007bff; color: white; }
            .btn-success { background: #28a745; color: white; }
            .btn-danger { background: #dc3545; color: white; }
            .table { width: 100%; border-collapse: collapse; }
            .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            .table th { background-color: #f2f2f2; }
            .permiso-denegado { background: #ffebee; color: #c62828; padding: 10px; border-radius: 4px; }
            .navbar { background: #f8f9fa; padding: 10px; border-radius: 5px; }
            .navbar ul { list-style: none; margin: 0; padding: 0; }
            .navbar li { display: inline; margin-right: 10px; }
            .navbar a { text-decoration: none; color: #333; }
            .navbar a:hover { color: #667eea; }
            .nav-links { padding: 10px 20px; text-decoration: none; color: #555; border-radius: 4px; transition: all 0.3s ease; display: flex; align-items: center; justify-content: right; gap: 8px; font-weight: 500; }
            
            /* === MODAL STYLES === */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.6);
                align-items: center;
                justify-content: center;
            }
            .modal-content {
                background: #fff;
                padding: 20px;
                border-radius: 8px;
                width: 420px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            }
            .modal-content h2 {
                margin-bottom: 10px;
                text-align: center;
            }
            .modal-content label {
                display: block;
                margin-top: 10px;
                font-weight: bold;
            }
            .modal-content input, .modal-content select, .modal-content textarea {
                width: 100%;
                padding: 8px;
                border: 1px solid #ccc;
                border-radius: 5px;
                margin-top: 5px;
                box-sizing: border-box;
            }
            .close {
                float: right;
                font-size: 22px;
                cursor: pointer;
            }
            .hidden { display: none; }
        </style>
    </head>
    <body>
        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="logout.php">Cerrar sesión</a></li>
            </ul>
        </nav>
        <div class="container">
            <h1>🎓 Dashboard del Sistema</h1>
            <div class="card">
                <h2>👤 Perfil: <?php echo $perfil['nombre']; ?></h2>
                <p><strong>Descripción:</strong> <?php echo $perfil['descripcion']; ?></p>
                <p><strong>Módulos accesibles:</strong> <?php echo implode(', ', $modulos_accesibles); ?></p>
            </div>

            <!-- Módulo de Usuarios -->
            <div class="card">
                <h2>👥 Gestión de Usuarios</h2>
                <?php if (in_array('usuarios', $modulos_accesibles)): ?>
                    <?php $usuarios = $crudUsuarios->obtenerUsuarios($usuario_actual_id); ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Perfil</th>
                                <?php if (
                                    $sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'actualizar') ||
                                    $sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'eliminar')
                                ): ?>
                                    <th>Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo $usuario['id']; ?></td>
                                <td><?php echo $usuario['nombre']; ?></td>
                                <td><?php echo $usuario['email']; ?></td>
                                <td><?php echo $usuario['perfil']; ?></td>
                                <?php if (
                                    $sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'actualizar') ||
                                    $sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'eliminar')
                                ): ?>
                                <td>
                                    <?php if ($sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'actualizar')): ?>
                                        <button class="btn btn-primary btn-editar-usuario" 
                                                data-id="<?php echo $usuario['id']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($usuario['nombre']); ?>"
                                                data-email="<?php echo htmlspecialchars($usuario['email']); ?>"
                                                data-perfil="<?php echo $usuario['perfil']; ?>">
                                            Editar
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'eliminar')): ?>
                                        <button class="btn btn-danger btn-eliminar-usuario" 
                                                data-id="<?php echo $usuario['id']; ?>"
                                                data-nombre="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                                            Eliminar
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($sistemaPerfiles->tienePermiso($usuario_actual_id, 'usuarios', 'crear')): ?>
                        <button class="btn btn-success" id="crear_usuario">➕ Crear Usuario</button>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="permiso-denegado">❌ No tiene permisos para acceder a este módulo</div>
                <?php endif; ?>
            </div>

            <!-- Módulo de Notas -->
            <div class="card">
                <h2>📝 Gestión de Calificaciones</h2>
                <?php if (in_array('notas', $modulos_accesibles)): ?>
                    <?php $notas = $crudNotas->obtenerNotas($usuario_actual_id); ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Asignatura</th>
                                <th>Parcial</th>
                                <th>Teoría</th>
                                <th>Práctica</th>
                                <th>Promedio</th>
                                <?php if (
                                    $sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'actualizar') ||
                                    $sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'eliminar')
                                ): ?>
                                    <th>Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notas as $nota): ?>
                            <tr data-id="<?php echo $nota['id']; ?>">
                                <td><?php echo $nota['estudiante'] ?? 'Mis notas'; ?></td>
                                <td><?php echo $nota['asignatura']; ?></td>
                                <td><?php echo $nota['parcial'] == 1 ? '1er' : ($nota['parcial'] == 2 ? '2do' : 'Mejoramiento'); ?></td>
                                <td><?php echo $nota['teoria']; ?></td>
                                <td><?php echo $nota['practica']; ?></td>
                                <td><strong><?php echo number_format($nota['promedio'], 2); ?></strong></td>
                                <?php if (
                                    $sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'actualizar') ||
                                    $sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'eliminar')
                                ): ?>
                                <td>
                                    <?php if ($sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'actualizar')): ?>
                                        <button class="btn btn-primary btn-editar-nota" 
                                                data-id="<?php echo $nota['id']; ?>"
                                                data-teoria="<?php echo $nota['teoria']; ?>"
                                                data-practica="<?php echo $nota['practica']; ?>"
                                                data-obs="<?php echo htmlspecialchars($nota['obs'] ?? ''); ?>">
                                            Editar
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'eliminar')): ?>
                                        <button class="btn btn-danger btn-eliminar-nota" 
                                                data-id="<?php echo $nota['id']; ?>"
                                                data-estudiante="<?php echo htmlspecialchars($nota['estudiante'] ?? 'Mis notas'); ?>"
                                                data-asignatura="<?php echo htmlspecialchars($nota['asignatura']); ?>">
                                            Eliminar
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($sistemaPerfiles->tienePermiso($usuario_actual_id, 'notas', 'crear')): ?>
                        <button class="btn btn-success" id="agregar_calificacion">➕ Agregar Calificación</button>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="permiso-denegado">❌ No tiene permisos para acceder a este módulo</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ✅ MODAL REUTILIZABLE -->
        <div id="modal" class="modal hidden">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 id="modal-title">Editar registro</h2>
                <form id="modal-form">
                    <!-- Campos dinámicos -->
                    <div id="modal-fields"></div>
                    <div style="margin-top: 20px; text-align: right;">
                        <button type="button" class="btn btn-secondary" id="cancelar-modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("modal");
            const closeBtn = document.querySelector(".close");
            const cancelarBtn = document.getElementById("cancelar-modal");
            const modalTitle = document.getElementById("modal-title");
            const modalFields = document.getElementById("modal-fields");
            const modalForm = document.getElementById("modal-form");

            let currentAction = "";
            let currentId = null;

            // 🔹 Función para abrir modal con campos personalizados
            function openModal(title, fields, action, id = null, values = {}) {
                modalTitle.textContent = title;
                modalFields.innerHTML = "";
                currentAction = action;
                currentId = id;

                fields.forEach(f => {
                    const label = document.createElement("label");
                    label.textContent = f.label;
                    label.htmlFor = f.name;

                    let input;
                    if (f.type === "textarea") {
                        input = document.createElement("textarea");
                        input.rows = 3;
                    } else if (f.type === "select") {
                        input = document.createElement("select");
                        f.options.forEach(opt => {
                            const option = document.createElement("option");
                            option.value = opt.value;
                            option.textContent = opt.text;
                            if (values[f.name] == opt.value) {
                                option.selected = true;
                            }
                            input.appendChild(option);
                        });
                    } else {
                        input = document.createElement("input");
                        input.type = f.type;
                        if (f.step) input.step = f.step;
                        if (f.min !== undefined) input.min = f.min;
                        if (f.max !== undefined) input.max = f.max;
                    }

                    input.id = f.name;
                    input.name = f.name;
                    input.value = values[f.name] || "";
                    input.required = f.required || false;
                    
                    const fieldContainer = document.createElement("div");
                    fieldContainer.style.marginBottom = "15px";
                    fieldContainer.appendChild(label);
                    fieldContainer.appendChild(input);
                    
                    modalFields.appendChild(fieldContainer);
                });

                modal.style.display = "flex";
            }

            // 🔹 Cerrar modal
            function closeModal() {
                modal.style.display = "none";
                currentAction = "";
                currentId = null;
            }

            closeBtn.onclick = closeModal;
            cancelarBtn.onclick = closeModal;
            window.onclick = (e) => { 
                if (e.target == modal) closeModal(); 
            };

            // 🔹 Enviar formulario
            modalForm.addEventListener("submit", async (e) => {
                e.preventDefault();
                const submitBtn = modalForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                
                submitBtn.textContent = "Guardando...";
                submitBtn.disabled = true;

                try {
                    const formData = new FormData(modalForm);
                    formData.append("accion", currentAction);
                    if (currentId) formData.append("id", currentId);

                    const res = await fetch("../controlador/acciones.php", { 
                        method: "POST", 
                        body: formData 
                    });
                    const json = await res.json();

                    if (json.success) {
                        alert(json.success);
                        closeModal();
                        location.reload();
                    } else {
                        alert(json.error || "Error desconocido");
                    }
                } catch (error) {
                    alert("Error de conexión: " + error.message);
                } finally {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });

            // === USUARIOS ===
            // === CREAR USUARIO ===
            document.getElementById("crear_usuario")?.addEventListener("click", () => {
                openModal("➕ Crear Usuario", [
                    { label: "Nombre", name: "nombre", type: "text", required: true },
                    { label: "Email", name: "email", type: "email", required: true },
                    { label: "Contraseña", name: "contrasena", type: "password", required: true },
                    { 
                        label: "Perfil", 
                        name: "perfil_id", 
                        type: "select", 
                        required: true,
                        options: [
                            { value: "1", text: "Administrador" },
                            { value: "2", text: "Estudiante" },
                            { value: "3", text: "Docente" }
                        ]
                    },
                    { label: "Observaciones", name: "obs", type: "textarea" }
                ], "crear_usuario");
            });



            document.querySelectorAll(".btn-editar-usuario").forEach(btn => {
                btn.addEventListener("click", () => {
                    const id = btn.getAttribute("data-id");
                    const nombre = btn.getAttribute("data-nombre");
                    const email = btn.getAttribute("data-email");
                    const perfil = btn.getAttribute("data-perfil");
                    
                    openModal("✏️ Editar Usuario", [
                        { label: "Nombre", name: "nombre", type: "text", required: true },
                        { label: "Email", name: "email", type: "email", required: true },
                        { 
                            label: "Perfil", 
                            name: "perfil_id", 
                            type: "select", 
                            required: true,
                            options: [
                                { value: "1", text: "Administrador" },
                                { value: "2", text: "Estudiante" },
                                { value: "3", text: "Docente" }
                            ]
                        }
                    ], "actualizar_usuario", id, {
                        nombre: nombre,
                        email: email,
                        perfil_id: perfil === "Administrador" ? "1" : (perfil === "Estudiante" ? "2" : "3")
                    });
                });
            });

            document.querySelectorAll(".btn-eliminar-usuario").forEach(btn => {
                btn.addEventListener("click", async () => {
                    const id = btn.getAttribute("data-id");
                    const nombre = btn.getAttribute("data-nombre");
                    
                    if (!confirm(`¿Está seguro de que desea eliminar al usuario "${nombre}"?`)) return;

                    try {
                        const datos = new FormData();
                        datos.append("accion", "eliminar_usuario");
                        datos.append("id", id);

                        const res = await fetch("../controlador/acciones.php", { 
                            method: "POST", 
                            body: datos 
                        });
                        const json = await res.json();
                        
                        if (json.success) {
                            alert(json.success);
                            location.reload();
                        } else {
                            alert(json.error || "Error al eliminar usuario");
                        }
                    } catch (error) {
                        alert("Error de conexión: " + error.message);
                    }
                });
            });

            // === NOTAS ===
            document.querySelector("#agregar_calificacion")?.addEventListener("click", () => {
                openModal("➕ Agregar Calificación", [
                    { label: "ID Asignatura", name: "asignatura_id", type: "number", required: true },
                    { label: "ID Estudiante", name: "usuario_id", type: "number", required: true },
                    { label: "Parcial", name: "parcial", type: "number", required: true, min: 1, max: 3 },
                    { label: "Nota Teoría", name: "teoria", type: "number", required: true, step: "1", min: 0, max: 100 },
                    { label: "Nota Práctica", name: "practica", type: "number", required: true, step: "1", min: 0, max: 100 },
                    { label: "Observaciones", name: "obs", type: "textarea" }
                ], "crear_nota");
            });

            document.querySelectorAll(".btn-editar-nota").forEach(btn => {
                btn.addEventListener("click", () => {
                    const id = btn.getAttribute("data-id");
                    const teoria = btn.getAttribute("data-teoria");
                    const practica = btn.getAttribute("data-practica");
                    const obs = btn.getAttribute("data-obs");
                    
                    openModal("✏️ Editar Nota", [
                        { label: "Nota Teoría", name: "teoria", type: "number", required: true, step: "1", min: 0, max: 100 },
                        { label: "Nota Práctica", name: "practica", type: "number", required: true, step: "1", min: 0, max: 100 },
                        { label: "Observación", name: "obs", type: "textarea" }
                    ], "actualizar_nota", id, {
                        teoria: teoria,
                        practica: practica,
                        obs: obs || ""
                    });
                });
            });

            document.querySelectorAll(".btn-eliminar-nota").forEach(btn => {
                btn.addEventListener("click", async () => {
                    const id = btn.getAttribute("data-id");
                    const estudiante = btn.getAttribute("data-estudiante");
                    const asignatura = btn.getAttribute("data-asignatura");
                    
                    if (!confirm(`¿Está seguro de que desea eliminar la calificación de "${estudiante}" en "${asignatura}"?`)) return;

                    try {
                        const datos = new FormData();
                        datos.append("accion", "eliminar_nota");
                        datos.append("id", id);

                        const res = await fetch("../controlador/acciones.php", { 
                            method: "POST", 
                            body: datos 
                        });
                        const json = await res.json();
                        
                        if (json.success) {
                            alert(json.success);
                            location.reload();
                        } else {
                            alert(json.error || "Error al eliminar calificación");
                        }
                    } catch (error) {
                        alert("Error de conexión: " + error.message);
                    }
                });
            });

            // Debug
            console.log('Botones editar usuario:', document.querySelectorAll('.btn-editar-usuario').length);
            console.log('Botones eliminar usuario:', document.querySelectorAll('.btn-eliminar-usuario').length);
            console.log('Botones editar nota:', document.querySelectorAll('.btn-editar-nota').length);
            console.log('Botones eliminar nota:', document.querySelectorAll('.btn-eliminar-nota').length);
        });
        </script>
    </body>
    </html>