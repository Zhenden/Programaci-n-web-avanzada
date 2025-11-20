<?php
// Migration script to add missing columns for supplies and bookings.
// Run this from browser: http://localhost/Deberes/Clase_practica_6/tools/migrate_schema.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'reservas_hotel';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('DB connect error: ' . $conn->connect_error);
}

function colExists($conn, $table, $column) {
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    if (!$stmt) return false;
    $stmt->bind_param('ss', $table, $column);
    if (!$stmt->execute()) { $stmt->close(); return false; }
    $stmt->bind_result($cnt);
    $stmt->store_result();
    $found = false;
    if ($stmt->fetch()) {
        $found = ($cnt > 0);
    }
    $stmt->close();
    return $found;
}

$queries = [];

// Supplies: add supplier_id, status, created_at, and foreign key to users
if (!colExists($conn, 'supplies', 'supplier_id')) {
    $queries[] = "ALTER TABLE supplies ADD COLUMN supplier_id INT NULL AFTER quantity";
}
if (!colExists($conn, 'supplies', 'status')) {
    $queries[] = "ALTER TABLE supplies ADD COLUMN status ENUM('requested','offered','delivered') NOT NULL DEFAULT 'requested' AFTER supplier_id";
}
if (!colExists($conn, 'supplies', 'created_at')) {
    $queries[] = "ALTER TABLE supplies ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER status";
}

// Add foreign key to users if not exists (check by trying to add index/constraint safely)
$fkCheck = $conn->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='supplies' AND REFERENCED_TABLE_NAME='users' AND REFERENCED_COLUMN_NAME='id'");
if ($fkCheck && $fkCheck->num_rows == 0) {
    if (colExists($conn, 'supplies', 'supplier_id')) {
        $queries[] = "ALTER TABLE supplies ADD CONSTRAINT supplies_supplier_fk FOREIGN KEY (supplier_id) REFERENCES users(id)";
    }
}

// Bookings: add status, total_price, created_at if missing
if (!colExists($conn, 'bookings', 'status')) {
    $queries[] = "ALTER TABLE bookings ADD COLUMN status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending' AFTER check_out_date";
}
if (!colExists($conn, 'bookings', 'total_price')) {
    $queries[] = "ALTER TABLE bookings ADD COLUMN total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER status";
}
if (!colExists($conn, 'bookings', 'created_at')) {
    $queries[] = "ALTER TABLE bookings ADD COLUMN created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER total_price";
}

echo "<h2>Schema Migration</h2>";
if (empty($queries)) {
    echo "<p>No changes required. Schema looks up-to-date for supplies and bookings.</p>";
    exit;
}

foreach ($queries as $q) {
    echo "<p>Running: <code>" . htmlspecialchars($q) . "</code></p>";
    if ($conn->query($q) === TRUE) {
        echo "<p style='color:green'>OK</p>";
    } else {
        echo "<p style='color:red'>Error: " . htmlspecialchars($conn->error) . "</p>";
    }
}

echo "<p>Done. Verify tables in phpMyAdmin or via CLI.</p>";

?>
