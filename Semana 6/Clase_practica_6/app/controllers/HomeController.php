<?php
require_once 'BaseController.php';

class HomeController extends BaseController {
    
    public function index() {
        // If user is logged in, redirect to dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('index.php?action=dashboard');
            return;
        }
        
        // Show home page
        $this->render('home');
    }
}