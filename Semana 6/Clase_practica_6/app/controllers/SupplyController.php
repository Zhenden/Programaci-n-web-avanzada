<?php
require_once 'BaseController.php';

class SupplyController extends BaseController {
    
    public function index() {
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $supplyModel = new Supply();
        $supplies = $supplyModel->all();
        
        $this->render('supplies/index', ['supplies' => $supplies]);
    }
    
    public function createForm() {
        // Check authentication and role
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $this->checkRole(['Administrator', 'Hotel Manager']);
        
        $this->render('supplies/create', []);
    }
    
    public function store() {
        // Check authentication and role
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $this->checkRole(['Administrator', 'Hotel Manager']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=supply_create');
            return;
        }
        
        $name = $_POST['name'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $description = $_POST['description'] ?? null;
        
        if (empty($name) || empty($quantity)) {
            $_SESSION['error'] = 'Por favor complete todos los campos';
            $this->redirect('index.php?action=supply_create');
            return;
        }
        
        $supplyModel = new Supply();
        
        if ($supplyModel->create($name, $quantity)) {
            $_SESSION['success'] = 'Suministro creado exitosamente';
            $this->redirect('index.php?action=supplies');
        } else {
            $_SESSION['error'] = 'Error al crear suministro';
            $this->redirect('index.php?action=supply_create');
        }
    }
    
    public function view() {
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        $id = $_GET['id'] ?? 0;
        $model = new Supply();
        $supply = $model->find($id);
        if (!$supply) {
            $_SESSION['error'] = 'Suministro no encontrado';
            $this->redirect('index.php?action=supplies');
            return;
        }
        $this->render('supplies/view', ['supply' => $supply]);
    }

    public function editForm() {
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        $this->checkRole(['Administrator', 'Hotel Manager']);
        $id = $_GET['id'] ?? 0;
        $model = new Supply();
        $supply = $model->find($id);
        if (!$supply) {
            $_SESSION['error'] = 'Suministro no encontrado';
            $this->redirect('index.php?action=supplies');
            return;
        }
        $this->render('supplies/edit', ['supply' => $supply]);
    }

    public function update() {
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        $this->checkRole(['Administrator', 'Hotel Manager']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=supplies');
            return;
        }
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;
        $status = $_POST['status'] ?? null;
        if (empty($name)) {
            $_SESSION['error'] = 'El nombre es requerido';
            $this->redirect('index.php?action=supply_edit&id=' . $id);
            return;
        }
        $model = new Supply();
        if ($model->update($id, $name, $quantity, $status)) {
            $_SESSION['success'] = 'Suministro actualizado';
        } else {
            $_SESSION['error'] = 'Error al actualizar suministro';
        }
        $this->redirect('index.php?action=supplies');
    }

    public function delete() {
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        $this->checkRole(['Administrator', 'Hotel Manager']);
        $id = $_GET['id'] ?? 0;
        $model = new Supply();
        try {
            if ($model->delete($id)) {
                $_SESSION['success'] = 'Suministro eliminado';
            } else {
                $_SESSION['error'] = 'Error al eliminar suministro';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        $this->redirect('index.php?action=supplies');
    }

    public function offer() {
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        $this->checkRole(['Administrator', 'Hotel Manager']);
        $id = $_GET['id'] ?? 0;
        $model = new Supply();
        if (!$model->find($id)) {
            $_SESSION['error'] = 'Suministro no encontrado';
            $this->redirect('index.php?action=supplies');
            return;
        }
        if ($model->update($id, $model->find($id)['name'], $model->find($id)['quantity'] ?? null, 'offered')) {
            $_SESSION['success'] = 'Suministro marcado como ofertado';
        } else {
            $_SESSION['error'] = 'Error al actualizar suministro';
        }
        $this->redirect('index.php?action=supplies');
    }
    

}