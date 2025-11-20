<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante Deluxe - Estilo Rojo/Naranja/Negro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Variables de colores */
        :root {
            --rojo-primario: #e74c3c;
            --rojo-oscuro: #c0392b;
            --naranja-principal: #e67e22;
            --naranja-claro: #f39c12;
            --negro: #1a1a1a;
            --negro-claro: #2c3e50;
            --gris-oscuro: #34495e;
            --blanco: #ecf0f1;
        }

        /* Estilos generales */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--negro);
            color: var(--blanco);
            margin: 0;
            padding: 5;
            line-height: 1.6;
        }

        .home-page {
            border-radius: 0;
            margin: 0;
            padding: 0;
            animation: fadeIn 0.8s ease-in;
            position: relative;
            overflow: hidden;
        }

        /* Animated Particles */
        .particles {
            position: absolute;
            top: -30px;
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
            background: rgba(230, 126, 34, 0.5);
            border-radius: 50%;
            animation: particleFloat 6s ease-in-out infinite;
        }

        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 8s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 1s; animation-duration: 6s; background: rgba(231, 76, 60, 0.5); }
        .particle:nth-child(3) { left: 30%; animation-delay: 2s; animation-duration: 7s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 3s; animation-duration: 9s; background: rgba(231, 76, 60, 0.5); }
        .particle:nth-child(5) { left: 50%; animation-delay: 4s; animation-duration: 5s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 8s; background: rgba(231, 76, 60, 0.5); }
        .particle:nth-child(7) { left: 70%; animation-delay: 6s; animation-duration: 6s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 7s; animation-duration: 7s; background: rgba(231, 76, 60, 0.5); }

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

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--negro) 0%, var(--rojo-oscuro) 50%, var(--naranja-principal) 100%);
            color: var(--blanco);
            padding: 100px 0;
            margin-bottom: 50px;
            border-radius: 0;
            position: relative;
            overflow: hidden;
            border-radius: 20px;
        }

        .hero-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 2;
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
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
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
            border: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 12px 30px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--rojo-primario), var(--naranja-principal));
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }

        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.6);
        }

        .btn-outline-danger {
            background: transparent;
            border: 2px solid var(--rojo-primario);
            color: var(--rojo-primario);
        }

        .btn-outline-danger:hover {
            background: var(--rojo-primario);
            color: white;
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
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: var(--negro-claro);
            margin-bottom: 50px;
        }

        .stat-item {
            padding: 30px 20px;
            transition: transform 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            margin: 10px;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
        }

        .stat-icon {
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--naranja-principal);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: var(--blanco);
            font-weight: 500;
        }

        /* Featured Section */
        .featured-section {
            padding: 80px 0;
            margin-bottom: 50px;
            background: var(--negro);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--blanco);
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(to right, var(--rojo-primario), var(--naranja-principal));
        }

        .dish-card {
            background: var(--negro-claro);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 20px;
            
        }

        .dish-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(231, 76, 60, 0.3);
        }

        .dish-image {
            height: 200px;
            background: linear-gradient(45deg, var(--rojo-primario) 0%, var(--naranja-principal) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .dish-placeholder {
            color: white;
            opacity: 0.8;
        }

        .dish-content {

            padding-bottom: 25px;
        }

        .dish-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--blanco);
        }

        .dish-description {
        font-size: 20px;
        line-height: 1.6; /* balanced */
        margin-bottom: 20px;
        color: #bdc3c7;   /* or var(--gris-texto) if defined */
        }


        .dish-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dish-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--naranja-claro);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--rojo-primario), var(--naranja-principal));
            color: white;
            border: none;
            border-radius: 50px;
            padding: 8px 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.4);
            color: white;
        }

        /* Features Section */
        .features-section {
            padding: 80px 0;
            background: var(--negro-claro);
            margin-bottom: 50px;
        }

        .feature-item {
            padding: 40px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
        }

        .feature-icon {
            margin-bottom: 20px;
        }

        .feature-item h4 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--blanco);
        }

        .feature-item p {
            color: #bdc3c7;
            line-height: 1.6;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--rojo-oscuro) 0%, var(--naranja-principal) 100%);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .cta-subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .btn-success {
            background: var(--negro);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-success:hover {
            background: var(--negro-claro);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            color: white;
        }

        /* Utilidades */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .text-center {
            text-align: center;
        }

        .text-primary {
            color: var(--rojo-primario) !important;
        }

        .text-success {
            color: var(--naranja-claro) !important;
        }

        .text-warning {
            color: var(--naranja-principal) !important;
        }

        .text-info {
            color: #3498db !important;
        }

        .text-danger {
            color: var(--rojo-primario) !important;
        }

        .row {
            display: flex;
            flex-wrap: nowrap;
            margin: 0 10px;
        }

        .col-md-3, .col-md-4 {
            padding: 0 15px;
            box-sizing: border-box;
        }

        .col-md-3 {
            flex: 0 0 25%;
            max-width: 25%;
        }

        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }

        .mb-4 {
            margin-bottom: 1.5rem;
        }

        .mb-5 {
            margin-bottom: 3rem;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hero-content {
                flex-direction: column;
                text-align: center;
            }
            
            .hero-text {
                padding-right: 0;
                margin-bottom: 40px;
            }
            
            .col-md-3, .col-md-4 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        @media (max-width: 768px) {
            .col-md-3, .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
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
                        <!-- Simulando que el usuario está logueado -->
                        <a href="index.php?action=dishes" class="btn btn-danger btn-lg">
                            <i class="fas fa-utensils"></i> Ver Menú
                        </a>
                        <a href="index.php?action=orders" class="btn btn-outline-danger btn-lg">
                            <i class="fas fa-list"></i> Mis Pedidos
                        </a>
                        <!--
                        <?php if(SessionManager::get('user_id')): ?>
                            <a href="index.php?action=dishes" class="btn btn-danger btn-lg">
                                <i class="fas fa-utensils"></i> Ver Menú
                            </a>
                            <a href="index.php?action=orders" class="btn btn-outline-danger btn-lg">
                                <i class="fas fa-list"></i> Mis Pedidos
                            </a>
                        <?php else: ?>
                            <a href="index.php?action=login" class="btn btn-danger btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </a>
                            <a href="index.php?action=register" class="btn btn-danger btn-lg">
                                Registrarse
                            </a>
                        <?php endif; ?>
                        -->
                    </div>
                </div>
                <div class="hero-image">
                    <div class="hero-placeholder float-animation">
                        <i class="fas fa-utensils fa-5x text-primary"></i>
                        <p class="mt-4">Experiencia Gourmet</p>
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
                            <div class="stat-number"><?= htmlspecialchars($totalDishes ?? '120') ?></div>
                            
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
                        <div class="stat-item slide-in-up" style="animation-delay: 0.3s;">
                            <div class="stat-icon">
                                <i class="fas fa-star fa-2x text-warning"></i>
                            </div>
                            <div class="stat-number">4.8</div>
                            <div class="stat-label">Valoración Promedio</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item slide-in-up" style="animation-delay: 0.4s;">
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
                                        <a href="index.php?action=login" class="btn btn-primary btn-sm">
                                            <i class="fas fa-sign-in-alt"></i> Pedir Ahora
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
                <!-- Simulando que el usuario está logueado -->
                <a href="index.php?action=order_create" class="btn btn-success btn-lg">
                    <i class="fas fa-shopping-cart"></i> Hacer Pedido Ahora
                </a>
                <!--
                <?php if(SessionManager::get('user_id')): ?>
                    <a href="index.php?action=order_create" class="btn btn-success btn-lg">
                        <i class="fas fa-shopping-cart"></i> Hacer Pedido Ahora
                    </a>
                <?php else: ?>
                    <a href="index.php?action=register" class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus"></i> Regístrate y Ordena
                    </a>
                <?php endif; ?>
                -->
            </div>
        </section>
    </div>
</body>
</html>