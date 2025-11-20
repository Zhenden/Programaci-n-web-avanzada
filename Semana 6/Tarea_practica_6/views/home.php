<div class="home-page fade-in">
    <!-- Animated background particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-text slide-in-up">
                <h1 class="hero-title">Bienvenido a <span class="text-primary">Restaurante Deluxe</span></h1>
                <p class="hero-subtitle">
                    Descubre una experiencia culinaria única con nuestros platos exquisitos preparados por los mejores chefs
                </p>
                <div class="hero-buttons">
                    <?php if(SessionManager::get('user_id')): ?>
                        <a href="index.php?action=dishes" class="btn btn-primary btn-lg">
                            <i class="fas fa-utensils"></i> Ver Menú
                        </a>
                        <a href="index.php?action=orders" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-list"></i> Mis Pedidos
                        </a>
                    <?php else: ?>
                        <a href="index.php?action=login" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </a>
                        <a href="index.php?action=register" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-image float-animation">
                <div class="hero-placeholder">
                    <i class="fas fa-utensils fa-5x text-muted"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="stat-item slide-in-up" style="animation-delay: 0.1s;">
                        <div class="stat-icon">
                            <i class="fas fa-utensils fa-2x text-primary"></i>
                        </div>
                        <div class="stat-number"><?= htmlspecialchars($totalDishes) ?></div>
                        <div class="stat-label">Platos Disponibles</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item slide-in-up" style="animation-delay: 0.2s;">
                        <div class="stat-icon">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Clientes Satisfechos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-star fa-2x text-warning"></i>
                        </div>
                        <div class="stat-number">4.8</div>
                        <div class="stat-label">Valoración Promedio</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-clock fa-2x text-info"></i>
                        </div>
                        <div class="stat-number">30min</div>
                        <div class="stat-label">Tiempo de Entrega</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Dishes Section -->
    <?php if(!empty($featuredDishes)): ?>
    <section class="featured-section">
        <div class="container">
            <h2 class="section-title text-center mb-5">
                <i class="fas fa-crown text-warning"></i>
                Platos Destacados
            </h2>
            <div class="row">
                <?php foreach($featuredDishes as $dish): ?>
                    <div class="col-md-4 mb-4">
                        <div class="dish-card">
                            <div class="dish-image">
                                <div class="dish-placeholder">
                                    <i class="fas fa-utensils fa-3x"></i>
                                </div>
                            </div>
                            <div class="dish-content">
                                <h4 class="dish-title"><?= htmlspecialchars($dish['name']) ?></h4>
                                <p class="dish-description"><?= htmlspecialchars(substr($dish['description'], 0, 100)) ?>...</p>
                                <div class="dish-footer">
                                    <span class="dish-price">$<?= number_format($dish['price'], 2) ?></span>
                                    <?php if(SessionManager::get('user_id')): ?>
                                        <a href="index.php?action=dish&id=<?= htmlspecialchars($dish['id']) ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?action=login" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="index.php?action=dishes" class="btn btn-primary">
                    <i class="fas fa-utensils"></i> Ver Todos los Platos
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title text-center mb-5">¿Por Qué Elegirnos?</h2>
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-award fa-3x text-primary"></i>
                        </div>
                        <h4>Calidad Premium</h4>
                        <p>Ingredientes frescos y de la más alta calidad en cada plato</p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-motorcycle fa-3x text-success"></i>
                        </div>
                        <h4>Entrega Rápida</h4>
                        <p>Servicio de entrega eficiente a tu puerta en minutos</p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-heart fa-3x text-danger"></i>
                        </div>
                        <h4>Hecho con Amor</h4>
                        <p>Cada plato es preparado con pasión y dedicación</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="cta-title">¿Listo para Ordenar?</h2>
            <p class="cta-subtitle">No esperes más, disfruta de la mejor comida ahora</p>
            <?php if(SessionManager::get('user_id')): ?>
                <a href="index.php?action=order_create" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-cart"></i> Hacer Pedido Ahora
                </a>
            <?php else: ?>
                <a href="index.php?action=register" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus"></i> Regístrate y Ordena
                </a>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
.home-page {
    margin: 0;
    padding: 0;
    animation: fadeIn 0.8s ease-in;
    position: relative;
    overflow: hidden;
}

/* Animated Particles */
.particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: particleFloat 6s ease-in-out infinite;
}

.particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 8s; }
.particle:nth-child(2) { left: 20%; animation-delay: 1s; animation-duration: 6s; }
.particle:nth-child(3) { left: 30%; animation-delay: 2s; animation-duration: 7s; }
.particle:nth-child(4) { left: 40%; animation-delay: 3s; animation-duration: 9s; }
.particle:nth-child(5) { left: 50%; animation-delay: 4s; animation-duration: 5s; }
.particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 8s; }
.particle:nth-child(7) { left: 70%; animation-delay: 6s; animation-duration: 6s; }
.particle:nth-child(8) { left: 80%; animation-delay: 7s; animation-duration: 7s; }

@keyframes particleFloat {
    0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-10vh) rotate(360deg); opacity: 0; }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}

.slide-in-up {
    animation: slideInUp 0.6s ease-out;
}

.float-animation {
    animation: float 3s ease-in-out infinite;
}

.pulse-animation {
    animation: pulse 2s ease-in-out infinite;
}

.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    margin-bottom: 50px;
}

.hero-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.hero-text {
    flex: 1;
    padding-right: 40px;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.2;
}

.hero-subtitle {
    font-size: 1.3rem;
    margin-bottom: 30px;
    opacity: 0.9;
    line-height: 1.6;
}

.hero-buttons .btn {
    margin-right: 15px;
    margin-bottom: 10px;
}

.hero-image {
    flex: 0 0 400px;
}

.hero-placeholder {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 60px;
    text-align: center;
    backdrop-filter: blur(10px);
}

.stats-section {
    padding: 60px 0;
    background: #f8f9fa;
    margin-bottom: 50px;
}

.stat-item {
    padding: 20px;
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
}

.stat-icon {
    margin-bottom: 15px;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 1.1rem;
    color: #666;
    font-weight: 500;
}

.featured-section {
    padding: 80px 0;
    margin-bottom: 50px;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
}

.dish-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.dish-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

.dish-image {
    height: 200px;
    background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.dish-placeholder {
    color: white;
    opacity: 0.8;
}

.dish-content {
    padding: 25px;
}

.dish-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

.dish-description {
    color: #666;
    margin-bottom: 20px;
    line-height: 1.5;
}

.dish-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dish-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #28a745;
}

.features-section {
    padding: 80px 0;
    background: #f8f9fa;
    margin-bottom: 50px;
}

.feature-item {
    padding: 30px 20px;
}

.feature-icon {
    margin-bottom: 20px;
}

.feature-item h4 {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #333;
}

.feature-item p {
    color: #666;
    line-height: 1.6;
}

.cta-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.cta-subtitle {
    font-size: 1.2rem;
    margin-bottom: 30px;
    opacity: 0.9;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.text-center {
    text-align: center;
}

.text-primary {
    color: #007bff !important;
}

@media (max-width: 768px) {
    .hero-content {
        flex-direction: column;
        text-align: center;
    }
    
    .hero-text {
        padding-right: 0;
        margin-bottom: 40px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-image {
        flex: 0 0 auto;
    }
    
    .hero-buttons .btn {
        margin-right: 0;
        margin-bottom: 10px;
        display: block;
        width: 100%;
        max-width: 250px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
}
</style>