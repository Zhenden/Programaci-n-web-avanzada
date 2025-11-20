<?php require_once 'views/layouts/header.php'; 

?>

<div class="auth-container">
    <div class="auth-header">
        <h2 class="auth-title">Iniciar Sesión</h2>
        <p class="auth-subtitle">Bienvenido de vuelta a Hotel Luxury</p>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?action=authenticate">
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">Iniciar Sesión</button>
    </form>
    
    <div class="text-center mt-3">
        <p>¿No tienes una cuenta? <a href="index.php?action=register">Regístrate aquí</a></p>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>