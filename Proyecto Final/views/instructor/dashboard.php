<h2>Panel de Instructor</h2>
<?php if (!empty($user)): ?>
    <p>Bienvenido, <?php echo htmlspecialchars($user['username']); ?>. Gestiona tus clases y horarios aquí.</p>
    <p><a class="btn" href="?route=instructor/classes">Ver mis clases</a> <a class="btn" href="?route=instructor/create_class">Crear nueva clase</a></p>
    <h3>Tus próximas clases</h3>
    <?php
    // si el controlador pasó $classes, reusar la vista de listado de clases
    if (!empty($classes)) {
        // reusar el fragmento de tabla de clases
        require __DIR__ . '/classes.php';
    } else {
        echo '<p>No tienes clases asignadas.</p>';
    }
    ?>
<?php else: ?>
    <p>Accede para administrar tus clases.</p>
<?php endif; ?>