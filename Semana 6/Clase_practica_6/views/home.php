<?php require_once 'views/layouts/header.php'; ?>

<div class="hero">
    <h1>🏨 Bienvenido a Hotel Luxury</h1>
    <p>Experimenta el lujo y la comodidad en cada estancia</p>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div style="margin-top: 2rem;">
            <a href="index.php?action=login" class="btn btn-primary" style="margin-right: 1rem;">Iniciar Sesión</a>
            <a href="index.php?action=register" class="btn btn-success">Registrarse</a>
        </div>
    <?php else: ?>
        <div style="margin-top: 2rem;">
            <a href="index.php?action=dashboard" class="btn btn-primary">Ir al Dashboard</a>
        </div>
    <?php endif; ?>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🛏️ Habitaciones de Lujo</h3>
            </div>
            <div class="card-body">
                <p>Disfruta de nuestras habitaciones elegantemente decoradas con todas las comodidades modernas.</p>
                <ul style="list-style: none; padding: 0;">
                    <li>✅ Wi-Fi de alta velocidad</li>
                    <li>✅ Aire acondicionado</li>
                    <li>✅ TV por cable</li>
                    <li>✅ Servicio a la habitación</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🍽️ Restaurante Gourmet</h3>
            </div>
            <div class="card-body">
                <p>Experimenta la excelencia culinaria en nuestro restaurante con chefs internacionales.</p>
                <ul style="list-style: none; padding: 0;">
                    <li>✅ Cocina internacional</li>
                    <li>✅ Desayuno buffet</li>
                    <li>✅ Servicio 24/7</li>
                    <li>✅ Menú dietético</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">🏊 Servicios Premium</h3>
            </div>
            <div class="card-body">
                <p>Relájate y disfruta de nuestras instalaciones de primer nivel.</p>
                <ul style="list-style: none; padding: 0;">
                    <li>✅ Piscina climatizada</li>
                    <li>✅ Gimnasio completo</li>
                    <li>✅ Spa y bienestar</li>
                    <li>✅ Estacionamiento seguro</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>