<div class="card fade-in">
    <h2 class="text-center mb-2">Iniciar Sesión</h2>
    
    <?php 
    $error = SessionManager::get('error');
    if($error){ 
        echo '<div class="alert alert-danger">'.htmlspecialchars($error).'</div>'; 
        SessionManager::remove('error'); 
    } 
    ?>
    
    <form method="POST" action="index.php?action=login_post">
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
    
    <p class="text-center mt-2">
        ¿No tienes una cuenta? <a href="index.php?action=register">Regístrate aquí</a>
    </p>
</div>