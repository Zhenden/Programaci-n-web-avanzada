<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nueva tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">Crear nueva tarea</h3>
    <form action="index.php?action=task_create" method="POST">
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3"></textarea>
        </div>
        <input type="hidden" name="estado" value="pendiente">
        <button type="submit" class="btn btn-success">Guardar tarea</button>
        <a href="index.php?action=dashboard" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
