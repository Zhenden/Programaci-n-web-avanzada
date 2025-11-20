 <?php
 require_once __DIR__ . '/../models/Order.php';
 require_once __DIR__ . '/../models/Dish.php';
 class OrdersController extends BaseController {
 public function index(){
 $this->authRequired();
 $order = new Order();
 if($_SESSION['role_name'] === 'Administrator' ||
 $_SESSION['role_name'] === 'Chef' || $_SESSION['role_name'] === 'Waiter'){
 $orders = $order->all();
 } else {
 $orders = $order->byUser($_SESSION['user_id']);
 }
 $this->view('orders/index', ['orders'=>$orders]);
 }
 public function createForm(){
 $this->authRequired();
 $dish = new Dish();
$dishes = $dish->all();
 $this->view('orders/create', ['dishes'=>$dishes]);
 }
 public function store(){
 $this->authRequired();
 $order = new Order();
 $order->create($_SESSION['user_id'], $_POST['dish_id'],
 $_POST['quantity']);
 $this->redirect('index.php?action=orders');
 }
 public function updateStatus(){
 // Only Chef, Waiter, Admin can update status
 $this->checkRole(['Administrator','Chef','Waiter']);
 $order = new Order();
 $order->updateStatus($_POST['id'], $_POST['status']);
 $this->redirect('index.php?action=orders');
 }
 public function delete(){
 // Only Admin
 $this->checkRole(['Administrator']);
 $order = new Order();
 $order->delete($_GET['id']);
 $this->redirect('index.php?action=orders');
 }
 }