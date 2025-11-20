<?php
require_once 'BaseController.php';

class BookingController extends BaseController {
    
    public function index() {
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        // Check role - only staff can see all bookings
        $userRole = $this->getCurrentUserRole();
        if (!in_array($userRole, ['Administrator', 'Hotel Manager', 'Receptionist'])) {
            $this->redirect('index.php?action=my_bookings');
            return;
        }
        
        $bookingModel = new Booking();
        $bookings = $bookingModel->all();
        
        $this->render('bookings/index', ['bookings' => $bookings]);
    }
    
    public function createForm() {
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $roomModel = new Room();
        $rooms = $roomModel->getAvailableRooms();
        
        $this->render('bookings/create', ['rooms' => $rooms]);
    }
    
    public function store() {
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?action=booking_create');
            return;
        }
        
        $roomId = $_POST['room_id'] ?? '';
        $checkIn = $_POST['check_in_date'] ?? '';
        $checkOut = $_POST['check_out_date'] ?? '';
        
        if (empty($roomId) || empty($checkIn) || empty($checkOut)) {
            $_SESSION['error'] = 'Por favor complete todos los campos';
            $this->redirect('index.php?action=booking_create');
            return;
        }
        
        // Validate dates
        if ($checkIn >= $checkOut) {
            $_SESSION['error'] = 'La fecha de salida debe ser posterior a la fecha de entrada';
            $this->redirect('index.php?action=booking_create');
            return;
        }
        
        $userId = $this->getCurrentUserId();
        $bookingModel = new Booking();
        $roomModel = new Room();
        
        // Check if room is available
        if (!$bookingModel->isRoomAvailable($roomId, $checkIn, $checkOut)) {
            $_SESSION['error'] = 'La habitación no está disponible para las fechas seleccionadas';
            $this->redirect('index.php?action=booking_create');
            return;
        }
        
        // Get room price and calculate total
        $room = $roomModel->find($roomId);
        $nights = (strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24);
        $totalPrice = $room['room_price'] * $nights;
        
        if ($bookingModel->create($userId, $roomId, $checkIn, $checkOut, $totalPrice)) {
            $_SESSION['success'] = 'Reserva creada exitosamente';
            $this->redirect('index.php?action=my_bookings');
        } else {
            $_SESSION['error'] = 'Error al crear reserva';
            $this->redirect('index.php?action=booking_create');
        }
    }
    
    public function myBookings() {
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $userId = $this->getCurrentUserId();
        $bookingModel = new Booking();
        $bookings = $bookingModel->getByUserId($userId);
        
        $this->render('bookings/my_bookings', ['bookings' => $bookings]);
    }
    
    public function confirm() {
        // Check authentication and role
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $this->checkRole(['Administrator', 'Hotel Manager', 'Receptionist']);
        
        $id = $_GET['id'] ?? 0;
        $bookingModel = new Booking();
        
        if ($bookingModel->confirm($id)) {
            $_SESSION['success'] = 'Reserva confirmada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al confirmar reserva';
        }
        
        $this->redirect('index.php?action=bookings');
    }
    
    public function cancel() {
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $id = $_GET['id'] ?? 0;
        $userId = $this->getCurrentUserId();
        $userRole = $this->getCurrentUserRole();
        $bookingModel = new Booking();
        $booking = $bookingModel->find($id);
        
        // Check if user can cancel this booking
        if ($booking['user_id'] != $userId && !in_array($userRole, ['Administrator', 'Hotel Manager', 'Receptionist'])) {
            $_SESSION['error'] = 'No tiene permiso para cancelar esta reserva';
            $this->redirect('index.php?action=my_bookings');
            return;
        }
        
        if ($bookingModel->cancel($id)) {
            $_SESSION['success'] = 'Reserva cancelada exitosamente';
        } else {
            $_SESSION['error'] = 'Error al cancelar reserva';
        }
        
        if (in_array($userRole, ['Administrator', 'Hotel Manager', 'Receptionist'])) {
            $this->redirect('index.php?action=bookings');
        } else {
            $this->redirect('index.php?action=my_bookings');
        }
    }
}