<?php
require_once __DIR__ . '/../Controller.php';
class InstructorController extends Controller
{
    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        // obtener las clases del instructor y pasarlas a la vista para mostrar la tabla en el dashboard
        require_once __DIR__ . '/../models/GymClass.php';
        $instructor_id = $user['id'] ?? null;
        $classes = $instructor_id ? GymClass::findByInstructor($instructor_id) : [];
        $this->render('instructor/dashboard', ['user' => $user, 'classes' => $classes]);
    }

    public function classes()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        $instructor_id = $user['id'] ?? null;
        require_once __DIR__ . '/../models/GymClass.php';
        $classes = GymClass::findByInstructor($instructor_id);
        $this->render('instructor/classes', ['classes' => $classes]);
    }

    public function create_class()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        $instructor_id = $user['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            GymClass::create([
                'nombre' => $_POST['nombre'] ?? '',
                'tipo' => $_POST['tipo'] ?? '',
                'instructor_id' => $instructor_id,
                'fecha_hora' => $_POST['fecha_hora'] ?? null,
            ]);
            $this->redirect('?route=instructor/classes');
        }
        $this->render('instructor/class_form');
    }

    public function edit_class()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        $instructor_id = $user['id'] ?? null;
        $id = $_GET['id'] ?? null;
        if (!$id) { $this->redirect('?route=instructor/classes'); }
        $class = GymClass::find($id);
        if ($class['instructor_id'] != $instructor_id) { $this->redirect('?route=instructor/classes'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            GymClass::update($id, [
                'nombre' => $_POST['nombre'] ?? null,
                'tipo' => $_POST['tipo'] ?? null,
                'fecha_hora' => $_POST['fecha_hora'] ?? null,
            ]);
            $this->redirect('?route=instructor/classes');
        }
        $this->render('instructor/class_form', ['class' => $class]);
    }

    public function delete_class()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        $instructor_id = $user['id'] ?? null;
        $id = $_GET['id'] ?? null;
        if ($id) {
            $class = GymClass::find($id);
            if ($class && $class['instructor_id'] == $instructor_id) {
                GymClass::delete($id);
            }
        }
        $this->redirect('?route=instructor/classes');
    }

    public function view_class()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        $instructor_id = $user['id'] ?? null;
        $id = $_GET['id'] ?? null;
        if (!$id) { $this->redirect('?route=instructor/classes'); }
        $class = GymClass::find($id);
        if (!$class || $class['instructor_id'] != $instructor_id) { $this->redirect('?route=instructor/classes'); }
        require_once __DIR__ . '/../models/Reserva.php';
        $attendees = Reserva::findByClass($id);
        $this->render('instructor/class_view', ['class' => $class, 'attendees' => $attendees]);
    }

    public function delete_reservation()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $user = $_SESSION['user'] ?? null;
        $instructor_id = $user['id'] ?? null;
        $res_id = $_GET['id'] ?? null;
        if ($res_id) {
            require_once __DIR__ . '/../models/Reserva.php';
            $res = Reserva::find($res_id);
            if ($res) {
                $class = GymClass::find($res['clase_id']);
                if ($class && $class['instructor_id'] == $instructor_id) {
                    Reserva::delete($res_id);
                    $this->redirect('?route=instructor/view_class&id=' . $class['id']);
                }
            }
        }
        $this->redirect('?route=instructor/classes');
    }
}
