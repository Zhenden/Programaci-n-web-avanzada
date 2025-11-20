<?php
 require_once 'Model.php';
 class Dish extends Model {
 public function all(){
        $res = $this->conn->query("SELECT * FROM dishes ORDER BY created_at 
DESC");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
 public function find($id){
 $stmt = $this->conn->prepare("SELECT * FROM dishes WHERE id=?");
 $stmt->bind_param('i',$id);
 $stmt->execute();
 return $stmt->get_result()->fetch_assoc();
 }
 public function create($name,$desc,$price){
 $stmt = $this->conn->prepare("INSERT INTO dishes 
(name,description,price) VALUES (?,?,?)");
 $stmt->bind_param('ssd',$name,$desc,$price);
 return $stmt->execute();
 }
 public function update($id,$name,$desc,$price){
 $stmt = $this->conn->prepare("UPDATE dishes SET name=?, 
description=?, price=? WHERE id=?");
 $stmt->bind_param('ssdi',$name,$desc,$price,$id);
 return $stmt->execute();
 }
 public function delete($id){
 $stmt = $this->conn->prepare("DELETE FROM dishes WHERE id=?");
 $stmt->bind_param('i',$id);
 return $stmt->execute();
 }
 }