<?php
require_once 'Model.php';

class Room extends Model {
    
    /**
     * Get all rooms
     */
    public function all() {
        $res = $this->conn->query("SELECT * FROM rooms ORDER BY room_number ASC");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Find room by ID
     */
    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM rooms WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }
    
    /**
     * Get available rooms
     */
    public function getAvailableRooms() {
        $res = $this->conn->query("SELECT * FROM rooms WHERE is_available=1 ORDER BY room_number ASC");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get available rooms by date range
     */
    public function getAvailableRoomsByDate($checkIn, $checkOut) {
        $hasStatus = $this->columnExists('bookings', 'status');

        $sub = "SELECT room_id FROM bookings WHERE ";
        if ($hasStatus) {
            $sub .= "status IN ('pending', 'confirmed') AND ";
        }
        $sub .= "check_in_date <= ? AND check_out_date >= ?";

        $sql = "SELECT r.* FROM rooms r WHERE r.is_available=1 AND r.id NOT IN (" . $sub . ") ORDER BY r.room_number ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $checkOut, $checkIn);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }
    
    /**
     * Create new room
     */
    public function create($roomNumber, $roomType, $price, $isAvailable = 1) {
        $stmt = $this->conn->prepare("INSERT INTO rooms (room_number, room_type, room_price, is_available) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isdi', $roomNumber, $roomType, $price, $isAvailable);
        return $stmt->execute();
    }
    
    /**
     * Update room
     */
    public function update($id, $roomNumber, $roomType, $price, $isAvailable) {
        $stmt = $this->conn->prepare("UPDATE rooms SET room_number=?, room_type=?, room_price=?, is_available=? WHERE id=?");
        $stmt->bind_param('isdii', $roomNumber, $roomType, $price, $isAvailable, $id);
        return $stmt->execute();
    }

    /**
     * Find room by room number
     */
    public function findByRoomNumber($roomNumber) {
        $stmt = $this->conn->prepare("SELECT * FROM rooms WHERE room_number = ? LIMIT 1");
        $stmt->bind_param('i', $roomNumber);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }
    
    /**
     * Delete room
     */
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM rooms WHERE id=?");
            if (!$stmt) throw new Exception('Error preparando la eliminación de la habitación');
            $stmt->bind_param('i', $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            // MySQL error code 1451 = cannot delete or update a parent row: a foreign key constraint fails
            if ($e->getCode() == 1451) {
                throw new Exception('No se puede eliminar la habitación porque existen reservas asociadas', 0, $e);
            }
            throw new Exception('Error al eliminar habitación: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Update room availability
     */
    public function updateAvailability($id, $isAvailable) {
        $stmt = $this->conn->prepare("UPDATE rooms SET is_available=? WHERE id=?");
        $stmt->bind_param('ii', $isAvailable, $id);
        return $stmt->execute();
    }
}