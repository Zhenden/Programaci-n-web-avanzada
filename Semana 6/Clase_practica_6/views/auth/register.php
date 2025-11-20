<?php require_once 'views/layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-header">
        <h2 class="auth-title">Registrarse</h2>
        <p class="auth-subtitle">Únete a Hotel Luxury</p>
    </div>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?action=register">
        <div class="form-group">
            <label for="name" class="form-label">Nombre Completo</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="role_id" class="form-label">Rol</label>
            <select id="role_id" name="role_id" class="form-control" required>
                <option value="">Seleccione un rol</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-success" style="width: 100%;">Registrarse</button>
    </form>
    
    <div class="text-center mt-3">
        <p>¿Ya tienes una cuenta? <a href="index.php?action=login">Inicia sesión aquí</a></p>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>