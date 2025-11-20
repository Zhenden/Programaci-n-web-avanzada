 <?php
 class DashboardController extends BaseController {
 public function index(){
 $this->authRequired();
 $this->view('dashboard');
 }
 }