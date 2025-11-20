<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h1>Añadir Nuevo Suministro</h1>
        <a href="index.php?action=supplies" class="btn btn-secondary">Volver a Suministros</a>
    </div>

    <div class="form-container">
        <form action="index.php?action=supplies_store" method="POST" class="supply-form">
            <div class="form-group">
                <label for="name">Nombre del Suministro:</label>
                <input type="text" id="name" name="name" required 
                       placeholder="Ej: Toallas, Productos de Limpieza, Café"
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="quantity">Cantidad Requerida:</label>
                <input type="number" id="quantity" name="quantity" required min="1"
                       placeholder="Ingrese la cantidad necesaria"
                       value="<?php echo htmlspecialchars($_POST['quantity'] ?? ''); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Crear Suministro</button>
                <a href="index.php?action=supplies" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--primary-color);
}

.page-header h1 {
    color: var(--primary-color);
    margin: 0;
}

.form-container {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.supply-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 0.75rem;
    border: 2px solid #e1e5e9;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.form-actions .btn {
    flex: 1;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .form-container {
        padding: 1.5rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>