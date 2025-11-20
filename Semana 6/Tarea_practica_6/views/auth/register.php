<link rel="stylesheet" href="../assets/css/styles.css">

<div class="card fade-in">
    <h2 class="text-center mb-2">Registro de Usuario</h2>
    
    <form method="POST" action="index.php?action=register_post">
        <div class="form-group">
            <label for="name">Nombre de Usuario</label>
            <input type="text" name="username" id="username" placeholder="Ingresa tu nombre" required>
        </div>
        
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" placeholder="Ingresa tu email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" placeholder="Crea una contraseña" required>
        </div>
        
        <button type="submit" class="btn btn-success btn-block">Crear Cuenta</button>
    </form>
    
    <p class="text-center mt-2">
        ¿Ya tienes cuenta? <a href="index.php?action=login">Inicia sesión aquí</a>
    </p>
</div>