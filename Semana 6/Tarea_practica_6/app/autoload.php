<?php
/**
 * Autoloader - Carga automática de clases siguiendo el patrón PSR-4
 */
spl_autoload_register(function ($className) {
    // Convertir namespace a path
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    
    // Mapeo de clases
    $classMap = [
        'SessionManager' => __DIR__ . '/core/SessionManager.php',
        'Model' => __DIR__ . '/models/Model.php',
        'User' => __DIR__ . '/models/User.php',
        'Dish' => __DIR__ . '/models/Dish.php',
        'Order' => __DIR__ . '/models/Order.php',
        'Comment' => __DIR__ . '/models/Comment.php',
        'Role' => __DIR__ . '/models/Role.php',
        'BaseController' => __DIR__ . '/controllers/BaseController.php',
        'AuthController' => __DIR__ . '/controllers/AuthController.php',
        'DishesController' => __DIR__ . '/controllers/DishesController.php',
        'OrdersController' => __DIR__ . '/controllers/OrdersController.php',
        'CommentsController' => __DIR__ . '/controllers/CommentsController.php',
        'DashboardController' => __DIR__ . '/controllers/DashboardController.php',
        'UserController' => __DIR__ . '/controllers/UserController.php'
    ];
    
    // Buscar en el mapeo
    if (isset($classMap[$className])) {
        if (file_exists($classMap[$className])) {
            require_once $classMap[$className];
            return;
        }
    }
    
    // Buscar en directorios estándar
    $paths = [
        __DIR__ . '/models/' . $className . '.php',
        __DIR__ . '/controllers/' . $className . '.php',
        __DIR__ . '/core/' . $className . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});