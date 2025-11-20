<?php
require_once 'BaseController.php';

class DashboardController extends BaseController {
    
    public function index() {
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        $userRole = $this->getCurrentUserRole();
        
        // Get dashboard data based on role
        $data = [];
        
        if (in_array($userRole, ['Administrator', 'Hotel Manager', 'Receptionist'])) {
            // Get statistics
            $roomModel = new Room();
            $bookingModel = new Booking();
            $userModel = new User();
            
            $data = [
                'totalRooms' => count($roomModel->all()),
                'availableRooms' => count($roomModel->getAvailableRooms()),
                'totalBookings' => count($bookingModel->all()),
                'upcomingBookings' => count($bookingModel->getUpcomingBookings()),
                'totalUsers' => count($userModel->all()),
                'recentBookings' => $bookingModel->getUpcomingBookings()
            ];
        }
        
        $this->render('dashboard', $data);
    }
}