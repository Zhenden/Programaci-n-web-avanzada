<?php
class HomeController extends BaseController {
    public function index(){
        // If user is already logged in, redirect to dashboard
        if(SessionManager::get('user_id')){
            $this->redirect('index.php?action=dashboard');
            return;
        }
        
        // No auth required for home page (guest users)
        $dishModel = new Dish();
        $dishes = $dishModel->all();
        
        // Get featured dishes (first 6)
        $featuredDishes = array_slice($dishes, 0, 6);
        
        $this->view('home', [
            'featuredDishes' => $featuredDishes,
            'totalDishes' => count($dishes)
        ]);
    }
}