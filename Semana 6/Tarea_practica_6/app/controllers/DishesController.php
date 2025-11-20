 <?php
 require_once __DIR__ . '/../models/Dish.php';
 class DishesController extends BaseController {
 public function index(){
 $this->authRequired();
 $dish = new Dish();
 $dishes = $dish->all();
 $this->view('dishes/index', ['dishes'=>$dishes]);
 }
 public function createForm(){
 $this->checkRole(['Administrator']);
 $this->view('dishes/create');
 }
 public function store(){
 $this->checkRole(['Administrator']);
 $dish = new Dish();
 $dish->create($_POST['name'], $_POST['description'],
 $_POST['price']);
 $this->redirect('index.php?action=dishes');
 }
 public function editForm(){
 $this->checkRole(['Administrator']);
$dishModel = new Dish();
 $dish = $dishModel->find($_GET['id']);
 $this->view('dishes/edit', ['dish'=>$dish]);
 }
 public function update(){
 $this->checkRole(['Administrator']);
 $dishModel = new Dish();
 $dishModel->update($_POST['id'], $_POST['name'],
 $_POST['description'], $_POST['price']);
 $this->redirect('index.php?action=dishes');
 }
 public function delete(){
 $this->checkRole(['Administrator']);
 $dishModel = new Dish();
 $dishModel->delete($_GET['id']);
 $this->redirect('index.php?action=dishes');
 }
 public function show(){
 $this->authRequired();
 $dishModel = new Dish();
 $dish = $dishModel->find($_GET['id']);
 $this->view('dishes/show', ['dish'=>$dish]);
 }
 }