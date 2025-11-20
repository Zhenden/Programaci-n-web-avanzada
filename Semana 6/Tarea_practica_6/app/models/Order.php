<?php
 require_once 'Model.php';
 class Order extends Model {
 public function all(){
 $sql = "SELECT o.*, u.username, d.name as dish_name FROM orders o 
JOIN users u ON o.user_id=u.id JOIN dishes d ON o.dish_id=d.id ORDER BY 
o.created_at DESC";
 return $this->conn->query($sql);
 }
 public function byUser($user_id){
 $stmt = $this->conn->prepare("SELECT o.*, d.name as dish_name FROM 
orders o JOIN dishes d ON o.dish_id=d.id WHERE o.user_id=? ORDER BY 
o.created_at DESC");
 $stmt->bind_param('i',$user_id);
 $stmt->execute();
 return $stmt->get_result();
 }
 public function find($id){
 $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id=?");
 $stmt->bind_param('i',$id);
 $stmt->execute();
 return $stmt->get_result()->fetch_assoc();
 }
 public function create($user_id,$dish_id,$quantity){
 $stmt = $this->conn->prepare("INSERT INTO orders 
(user_id,dish_id,quantity) VALUES (?,?,?)");
 $stmt->bind_param('iii',$user_id,$dish_id,$quantity);
 return $stmt->execute();
 }
 public function updateStatus($id,$status){
 $stmt = $this->conn->prepare("UPDATE orders SET status=? WHERE 
id=?");
 $stmt->bind_param('si',$status,$id);
 return $stmt->execute();
 }
 public function delete($id){
 $stmt = $this->conn->prepare("DELETE FROM orders WHERE id=?");
 $stmt->bind_param('i',$id);
 return $stmt->execute();
 }
 }