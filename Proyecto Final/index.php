<?php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/Controller.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';
require_once __DIR__ . '/app/controllers/MemberController.php';
require_once __DIR__ . '/app/controllers/InstructorController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$route = $_GET['route'] ?? 'home';
$parts = explode('/', trim($route, '/'));
$controller = $parts[0] ?? 'home';
$action = $parts[1] ?? null;

// Nota: para facilitar desarrollo se permite llamar a métodos públicos existentes en controladores.
// En producción deberías restringir las rutas explícitamente.

switch ($controller) {
	case 'home':
		$c = new Controller();
		$c->render('home');
		break;

	case 'auth':
		$auth = new AuthController();
		$act = $action ?? 'loginForm';
		if ($act === 'login') {
			$auth->login();
		} elseif ($act === 'logout') {
			$auth->logout();
		} else {
			$auth->loginForm();
		}
		break;

	case 'admin':
		if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') { header('Location: ?route=auth/loginForm'); exit; }
			$admin = new AdminController();
			$act = $action ?? 'dashboard';
			if (!method_exists($admin, $act)) { http_response_code(404); echo 'Not found'; break; }
			$admin->$act();
		break;

	case 'member':
			if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'member') { header('Location: ?route=auth/loginForm'); exit; }
			$mc = new MemberController();
			$act = $action ?? 'dashboard';
			if (!method_exists($mc, $act)) { http_response_code(404); echo 'Not found'; break; }
			$mc->$act();
		break;

	case 'instructor':
			if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') { header('Location: ?route=auth/loginForm'); exit; }
			$ic = new InstructorController();
			$act = $action ?? 'dashboard';
			if (!method_exists($ic, $act)) { http_response_code(404); echo 'Not found'; break; }
			$ic->$act();
		break;

	default:
		http_response_code(404);
		echo 'Página no encontrada';
}
