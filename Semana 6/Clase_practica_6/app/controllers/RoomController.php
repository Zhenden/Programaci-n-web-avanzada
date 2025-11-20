<?php
require_once 'BaseController.php';

class RoomController extends BaseController {
    
    public function index() {
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $roomModel = new Room();
        $rooms = $roomModel->all();
        
        $this->render('rooms/index', ['rooms' => $rooms]);
    }
    
    public function createForm() {
        // Check authentication and role
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $this->checkRole(['Administrator', 'Hotel Manager']);
        
        $this->render('rooms/create');
    }
    
    public function store() {
        // Check authentication and role
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $this->checkRole(['Administrator', 'Hotel Manager']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=room_create');
            return;
        }
        
        $roomNumber = $_POST['room_number'] ?? '';
        $roomType = $_POST['room_type'] ?? '';
        $price = $_POST['price'] ?? '';
        
        if (empty($roomNumber) || empty($roomType) || empty($price)) {
            $_SESSION['error'] = 'Por favor complete todos los campos requeridos';
            $this->redirect('index.php?action=room_create');
            return;
        }
        
        $roomModel = new Room();
        
        // Check if room number already exists
        if ($roomModel->findByRoomNumber($roomNumber)) {
            $_SESSION['error'] = 'El número de habitación ya existe';
            $this->redirect('index.php?action=room_create');
            return;
        }
        
        if ($roomModel->create($roomNumber, $roomType, $price)) {
            $_SESSION['success'] = 'Habitación creada exitosamente';
            $this->redirect('index.php?action=rooms');
        } else {
            $_SESSION['error'] = 'Error al crear habitación';
            $this->redirect('index.php?action=room_create');
        }
    }
    
    public function editForm() {
        // Check authentication and role
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $this->checkRole(['Administrator', 'Hotel Manager']);
        
        $id = $_GET['id'] ?? 0;
        $roomModel = new Room();
        $room = $roomModel->find($id);
        
        if (!$room) {
            $_SESSION['error'] = 'Habitación no encontrada';
            $this->redirect('index.php?action=rooms');
            return;
        }
        
        $this->render('rooms/edit', ['room' => $room]);
    }
    
    public function update() {
        // Check authentication and role
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $this->checkRole(['Administrator', 'Hotel Manager']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=rooms');
            return;
        }
        
        $id = $_POST['id'] ?? 0;
        $roomNumber = $_POST['room_number'] ?? '';
        $roomType = $_POST['room_type'] ?? '';
        $price = $_POST['price'] ?? '';
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;
        
        if (empty($roomNumber) || empty($roomType) || empty($price)) {
            $_SESSION['error'] = 'Por favor complete todos los campos requeridos';
            $this->redirect('index.php?action=room_edit&id=' . $id);
            return;
        }
        
        $roomModel = new Room();
        
        if ($roomModel->update($id, $roomNumber, $roomType, $price, $isAvailable)) {
            $_SESSION['success'] = 'Habitación actualizada exitosamente';
            $this->redirect('index.php?action=rooms');
        } else {
            $_SESSION['error'] = 'Error al actualizar habitación';
            $this->redirect('index.php?action=room_edit&id=' . $id);
        }
    }
    
    public function delete() {
        // Check authentication and role
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $this->checkRole(['Administrator', 'Hotel Manager']);
        
        $id = $_GET['id'] ?? 0;
        $roomModel = new Room();
        
        try {
            if ($roomModel->delete($id)) {
                $_SESSION['success'] = 'Habitación eliminada exitosamente';
            } else {
                $_SESSION['error'] = 'Error al eliminar habitación';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        $this->redirect('index.php?action=rooms');
    }
}