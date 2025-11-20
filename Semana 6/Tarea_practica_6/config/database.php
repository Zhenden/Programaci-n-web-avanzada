<?php
 function conectar() {
 $conn = new mysqli("localhost", "root", "", "restaurante");
 if ($conn->connect_error) {
 die("DB Error: " . $conn->connect_error);
 }
 $conn->set_charset('utf8mb4');
 return $conn;
 }