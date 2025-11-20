<?php
require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../models/Member.php';
class MemberController extends Controller
{
    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        $this->render('member/dashboard', ['user' => $user]);
    }

    public function classes()
    {
        // Mostrar clases disponibles para reservar
        require_once __DIR__ . '/../models/GymClass.php';
        $classes = GymClass::all();
        $this->render('member/classes', ['classes' => $classes]);
    }

    public function reserve()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        if (!$user) { $this->redirect('?route=auth/loginForm'); }
        $miembro_id = $user['id'];
        $clase_id = $_GET['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clase_id = $_POST['clase_id'] ?? null;
            $fecha_hora = $_POST['fecha_hora'] ?? null;
            require_once __DIR__ . '/../models/Reserva.php';
            Reserva::create($miembro_id, $clase_id, $fecha_hora);
            $this->redirect('?route=member/history');
        }
        require_once __DIR__ . '/../models/GymClass.php';
        $clase = GymClass::find($clase_id);
        $this->render('member/reserve', ['clase' => $clase]);
    }

    public function history()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        if (!$user) { $this->redirect('?route=auth/loginForm'); }
        require_once __DIR__ . '/../models/Reserva.php';
        $reservas = Reserva::findByMember($user['id']);
        $this->render('member/history', ['reservas' => $reservas]);
    }

    public function cancel_reservation()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        if (!$user) { $this->redirect('?route=auth/loginForm'); }
        $res_id = $_GET['id'] ?? null;
        if ($res_id) {
            require_once __DIR__ . '/../models/Reserva.php';
            $res = Reserva::find($res_id);
            if ($res && $res['miembro_id'] == $user['id']) {
                Reserva::delete($res_id);
            }
        }
        $this->redirect('?route=member/history');
    }

    public function profile()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        if (!$user) { $this->redirect('?route=auth/loginForm'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Member::update($user['id'], [
                'nombre' => $_POST['nombre'] ?? null,
                'correo' => $_POST['correo'] ?? null,
                'password' => $_POST['password'] ?? '',
            ]);
            $this->redirect('?route=member/profile');
        }
        $member = Member::find($user['id']);
        $this->render('member/profile', ['member' => $member]);
    }
}
