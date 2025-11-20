<?php
require_once __DIR__ . '/app/autoload.php';

$action = $_GET['action'] ?? 'home';

// Rutas estándar
switch($action){
    // AUTH
    case 'login': (new AuthController())->showLogin(); break;
    case 'login_post': (new AuthController())->login(); break;
    case 'logout': (new AuthController())->logout(); break;
    case 'register': (new AuthController())->showRegister(); break;
    case 'register_post': (new AuthController())->register(); break;
    
    // DASH
    case 'dashboard': (new DashboardController())->index(); break;
    
    // DISHES
    case 'dishes': (new DishesController())->index(); break;
    case 'dish_create': (new DishesController())->createForm(); break;
    case 'dish_store': (new DishesController())->store(); break;
    case 'dish_edit': (new DishesController())->editForm(); break;
    case 'dish_update': (new DishesController())->update(); break;
    case 'dish_delete': (new DishesController())->delete(); break;
    case 'dish': (new DishesController())->show(); break;
    
    // ORDERS
    case 'orders': (new OrdersController())->index(); break;
    case 'order_create': (new OrdersController())->createForm(); break;
    case 'order_store': (new OrdersController())->store(); break;
    case 'order_update_status': (new OrdersController())->updateStatus(); break;
    case 'order_delete': (new OrdersController())->delete(); break;
    
    // USERS
    case 'users': (new UserController())->index(); break;
    case 'user_create': (new UserController())->createForm(); break;
    case 'user_store': (new UserController())->store(); break;
    case 'user_delete': (new UserController())->delete(); break;
    
    // COMMENTS
    case 'comments': (new CommentController())->index(); break;
    case 'comments/create': (new CommentController())->create(); break;
    case 'comments/delete': (new CommentController())->delete(); break;
    case 'comments/update': (new CommentController())->update(); break;
    case 'comments/dish': 
        $dishId = $_GET['id'] ?? 0;
        (new CommentController())->showByDish($dishId); 
        break;
    case 'comments/user': 
        $userId = $_GET['id'] ?? SessionManager::get('user_id');
        (new CommentController())->showByUser($userId); 
        break;
    
    // DEFAULT
    case 'home':
    default: (new HomeController())->index(); break;
}