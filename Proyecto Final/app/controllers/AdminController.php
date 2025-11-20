<?php
require_once __DIR__ . '/../Controller.php';
require_once __DIR__ . '/../models/Member.php';
require_once __DIR__ . '/../models/Instructor.php';
require_once __DIR__ . '/../models/GymClass.php';
require_once __DIR__ . '/../models/Facility.php';

class AdminController extends Controller
{
    public function dashboard()
    {
        $this->render('admin/dashboard');
    }

    public function members()
    {
        $members = Member::all();
        $this->render('admin/members', ['members' => $members]);
    }

    public function create_member()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Member::create([
                'nombre' => $_POST['nombre'] ?? '',
                'correo' => $_POST['correo'] ?? '',
                'password' => $_POST['password'] ?? '',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'género' => $_POST['género'] ?? ''
            ]);
            $this->redirect('?route=admin/members');
        }
        $this->render('admin/member_form');
    }

    public function edit_member()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) { $this->redirect('?route=admin/members'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Member::update($id, [
                'nombre' => $_POST['nombre'] ?? null,
                'correo' => $_POST['correo'] ?? null,
                'password' => $_POST['password'] ?? '',
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                'género' => $_POST['género'] ?? null
            ]);
            $this->redirect('?route=admin/members');
        }
        $member = Member::find($id);
        $this->render('admin/member_form', ['member' => $member]);
    }

    public function delete_member()
    {
        $id = $_GET['id'] ?? null;
        if ($id) Member::delete($id);
        $this->redirect('?route=admin/members');
    }

    public function classes()
    {
        $classes = GymClass::all();
        $this->render('admin/classes', ['classes' => $classes]);
    }

    public function create_class()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            GymClass::create([
                'nombre' => $_POST['nombre'] ?? '',
                'tipo' => $_POST['tipo'] ?? '',
                'instructor_id' => $_POST['instructor_id'] ?? null,
                'fecha_hora' => $_POST['fecha_hora'] ?? null,
            ]);
            $this->redirect('?route=admin/classes');
        }
        $instructors = Instructor::all();
        $this->render('admin/class_form', ['instructors' => $instructors]);
    }

    public function edit_class()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) { $this->redirect('?route=admin/classes'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            GymClass::update($id, [
                'nombre' => $_POST['nombre'] ?? null,
                'tipo' => $_POST['tipo'] ?? null,
                'instructor_id' => $_POST['instructor_id'] ?? null,
                'fecha_hora' => $_POST['fecha_hora'] ?? null,
            ]);
            $this->redirect('?route=admin/classes');
        }
        $class = GymClass::find($id);
        $instructors = Instructor::all();
        $this->render('admin/class_form', ['class' => $class, 'instructors' => $instructors]);
    }

    public function delete_class()
    {
        $id = $_GET['id'] ?? null;
        if ($id) GymClass::delete($id);
        $this->redirect('?route=admin/classes');
    }

    public function instructors()
    {
        $instructors = Instructor::all();
        $this->render('admin/instructors', ['instructors' => $instructors]);
    }

    public function create_instructor()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Instructor::create([
                'nombre' => $_POST['nombre'] ?? '',
                'correo' => $_POST['correo'] ?? '',
                'password' => $_POST['password'] ?? '',
                'especialidad' => $_POST['especialidad'] ?? ''
            ]);
            $this->redirect('?route=admin/instructors');
        }
        $this->render('admin/instructor_form');
    }

    public function edit_instructor()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) { $this->redirect('?route=admin/instructors'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Instructor::update($id, [
                'nombre' => $_POST['nombre'] ?? null,
                'correo' => $_POST['correo'] ?? null,
                'password' => $_POST['password'] ?? '',
                'especialidad' => $_POST['especialidad'] ?? null
            ]);
            $this->redirect('?route=admin/instructors');
        }
        $instructor = Instructor::find($id);
        $this->render('admin/instructor_form', ['instructor' => $instructor]);
    }

    public function delete_instructor()
    {
        $id = $_GET['id'] ?? null;
        if ($id) Instructor::delete($id);
        $this->redirect('?route=admin/instructors');
    }

    public function facilities()
    {
        $facilities = Facility::all();
        $this->render('admin/facilities', ['facilities' => $facilities]);
    }

    public function create_facility()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Facility::create([
                'nombre' => $_POST['nombre'] ?? '',
                'tipo' => $_POST['tipo'] ?? '',
                'capacidad' => $_POST['capacidad'] ?? 0,
            ]);
            $this->redirect('?route=admin/facilities');
        }
        $this->render('admin/facility_form');
    }

    public function edit_facility()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) { $this->redirect('?route=admin/facilities'); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Facility::update($id, [
                'nombre' => $_POST['nombre'] ?? null,
                'tipo' => $_POST['tipo'] ?? null,
                'capacidad' => $_POST['capacidad'] ?? null,
            ]);
            $this->redirect('?route=admin/facilities');
        }
        $facility = Facility::find($id);
        $this->render('admin/facility_form', ['facility' => $facility]);
    }

    public function delete_facility()
    {
        $id = $_GET['id'] ?? null;
        if ($id) Facility::delete($id);
        $this->redirect('?route=admin/facilities');
    }
}
