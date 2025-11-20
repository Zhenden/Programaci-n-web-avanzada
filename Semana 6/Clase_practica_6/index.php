<?php


session_start();

spl_autoload_register(function ($class) {
    $paths = [
        'app/models/',
        'app/controllers/',
        'app/core/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once 'config/database.php';
require_once 'app/models/Model.php';

// Routing
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'home':
        require_once 'app/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
        
    case 'login':
        require_once 'app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->showLogin();
        break;
        
    case 'register':
        require_once 'app/controllers/AuthController.php';
        $controller = new AuthController();
        // If POST, handle registration submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;
        
    case 'authenticate':
        require_once 'app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
        
    case 'logout':
        require_once 'app/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'dashboard':
        require_once 'app/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
        
    // Room management routes
    case 'rooms':
        require_once 'app/controllers/RoomController.php';
        $controller = new RoomController();
        $controller->index();
        break;
        
    case 'room_create':
        require_once 'app/controllers/RoomController.php';
        $controller = new RoomController();
        $controller->createForm();
        break;
        
    case 'room_store':
        require_once 'app/controllers/RoomController.php';
        $controller = new RoomController();
        $controller->store();
        break;
        
    case 'room_edit':
        require_once 'app/controllers/RoomController.php';
        $controller = new RoomController();
        $controller->editForm();
        break;
        
    case 'room_update':
        require_once 'app/controllers/RoomController.php';
        $controller = new RoomController();
        $controller->update();
        break;
        
    case 'room_delete':
        require_once 'app/controllers/RoomController.php';
        $controller = new RoomController();
        $controller->delete();
        break;
        
    // Booking routes
    case 'bookings':
        require_once 'app/controllers/BookingController.php';
        $controller = new BookingController();
        $controller->index();
        break;
        
    case 'booking_create':
        require_once 'app/controllers/BookingController.php';
        $controller = new BookingController();
        $controller->createForm();
        break;
        
    case 'booking_store':
        require_once 'app/controllers/BookingController.php';
        $controller = new BookingController();
        $controller->store();
        break;
        
    case 'booking_confirm':
        require_once 'app/controllers/BookingController.php';
        $controller = new BookingController();
        $controller->confirm();
        break;
        
    case 'booking_cancel':
        require_once 'app/controllers/BookingController.php';
        $controller = new BookingController();
        $controller->cancel();
        break;
        
    case 'my_bookings':
        require_once 'app/controllers/BookingController.php';
        $controller = new BookingController();
        $controller->myBookings();
        break;
        
    // Supply management routes
    case 'supplies':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->index();
        break;
        
    case 'supplies_create':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->createForm();
        break;
        
    case 'supplies_store':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->store();
        break;
        
    case 'supplies_offer':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->offer();
        break;
        
    // Singular supply routes (views use these)
    case 'supply_create':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->createForm();
        break;
    case 'supply_store':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->store();
        break;
    case 'supply_view':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->view();
        break;
    case 'supply_edit':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->editForm();
        break;
    case 'supply_update':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->update();
        break;
    case 'supply_delete':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->delete();
        break;
    case 'supply_offer':
        require_once 'app/controllers/SupplyController.php';
        $controller = new SupplyController();
        $controller->offer();
        break;
        
    default:
        require_once 'app/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}