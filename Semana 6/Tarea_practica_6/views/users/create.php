<div class="card fade-in">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Crear Nuevo Usuario</h2>
        <a href="index.php?action=users" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <!-- Mensajes de error -->
    <?php if ($error = SessionManager::get('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php SessionManager::remove('error'); ?>
    <?php endif; ?>
    
    <form method="POST" action="index.php?action=user_store" id="userForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        
        <div class="form-group">
            <label for="username">Nombre de Usuario *</label>
            <input type="text" class="form-control" id="username" name="username" required 
                   minlength="3" maxlength="50" pattern="[a-zA-Z0-9_-]{3,50}"
                   placeholder="Ingrese el nombre de usuario">
            <div class="invalid-feedback">
                El nombre de usuario debe tener entre 3 y 50 caracteres, solo letras, números, guiones y guiones bajos.
            </div>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" class="form-control" id="email" name="email" required
                   placeholder="usuario@ejemplo.com">
            <div class="invalid-feedback">
                Por favor ingrese un email válido.
            </div>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña *</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" required
                       minlength="6" placeholder="Mínimo 6 caracteres">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" 
                            aria-label="Mostrar/ocultar contraseña" aria-pressed="false">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <small class="form-text text-muted">
                La contraseña debe tener al menos 6 caracteres.
            </small>
            <div class="invalid-feedback">
                La contraseña debe tener al menos 6 caracteres.
            </div>
        </div>
        
        <div class="form-group">
            <label for="password_confirm">Confirmar Contraseña *</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required
                       placeholder="Repita la contraseña">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm" 
                            aria-label="Mostrar/ocultar confirmación de contraseña" aria-pressed="false">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="invalid-feedback">
                Las contraseñas no coinciden.
            </div>
        </div>
        
        <div class="form-group">
            <label for="role_id">Rol *</label>
            <select class="form-control" id="role_id" name="role_id" required>
                <option value="">Seleccione un rol</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= htmlspecialchars($role['id']) ?>">
                        <?= htmlspecialchars($role['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">
                Por favor seleccione un rol.
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Crear Usuario
            </button>
            <a href="index.php?action=users" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userForm');
    const submitBtn = document.getElementById('submitBtn');
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    
    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye" aria-hidden="true"></i>' : '<i class="fas fa-eye-slash" aria-hidden="true"></i>';
        // Update aria-pressed state
        this.setAttribute('aria-pressed', type === 'text' ? 'true' : 'false');
    });
    
    togglePasswordConfirm.addEventListener('click', function() {
        const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirm.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye" aria-hidden="true"></i>' : '<i class="fas fa-eye-slash" aria-hidden="true"></i>';
        // Update aria-pressed state
        this.setAttribute('aria-pressed', type === 'text' ? 'true' : 'false');
    });
    
    // Validación en tiempo real
    form.addEventListener('input', function() {
        validateForm();
    });
    
    // Validación al enviar
    form.addEventListener('submit', function(event) {
        if (!validateForm()) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
    
    function validateForm() {
        let isValid = true;
        
        // Validar username
        const username = document.getElementById('username');
        if (username.value.length < 3 || !/^[a-zA-Z0-9_-]{3,50}$/.test(username.value)) {
            username.classList.add('is-invalid');
            isValid = false;
        } else {
            username.classList.remove('is-invalid');
        }
        
        // Validar email
        const email = document.getElementById('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            email.classList.add('is-invalid');
            isValid = false;
        } else {
            email.classList.remove('is-invalid');
        }
        
        // Validar contraseña
        if (password.value.length < 6) {
            password.classList.add('is-invalid');
            isValid = false;
        } else {
            password.classList.remove('is-invalid');
        }
        
        // Validar confirmación de contraseña
        if (passwordConfirm.value !== password.value || passwordConfirm.value.length < 6) {
            passwordConfirm.classList.add('is-invalid');
            isValid = false;
        } else {
            passwordConfirm.classList.remove('is-invalid');
        }
        
        // Validar rol
        const roleId = document.getElementById('role_id');
        if (!roleId.value) {
            roleId.classList.add('is-invalid');
            isValid = false;
        } else {
            roleId.classList.remove('is-invalid');
        }
        
        // Habilitar/deshabilitar botón de envío
        submitBtn.disabled = !isValid;
        
        return isValid;
    }
});
</script>