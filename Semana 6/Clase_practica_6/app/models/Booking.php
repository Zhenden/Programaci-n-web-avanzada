<?php
require_once 'Model.php';

class Booking extends Model {

    public function all() {
        $res = $this->conn->query(
            "SELECT b.*, r.room_number, r.room_type, u.name as user_name, u.email " .
            "FROM bookings b JOIN rooms r ON b.room_id = r.id JOIN users u ON b.user_id = u.id " .
            "ORDER BY b.booking_date DESC"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function find($id) {
        $stmt = $this->conn->prepare(
            "SELECT b.*, r.room_number, r.room_type, r.room_price, u.name as user_name, u.email " .
            "FROM bookings b JOIN rooms r ON b.room_id = r.id JOIN users u ON b.user_id = u.id WHERE b.id=?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }

    public function getByUserId($userId) {
        $order = $this->columnExists('bookings', 'created_at') ? 'b.created_at DESC' : 'b.id DESC';
        $sql = "SELECT b.*, r.room_number, r.room_type, r.room_price FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.user_id=? ORDER BY $order";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    public function create($userId, $roomId, $checkIn, $checkOut, $totalPrice = 0.0) {
        if ($this->columnExists('bookings', 'total_price')) {
            $stmt = $this->conn->prepare("INSERT INTO bookings (user_id, room_id, booking_date, check_in_date, check_out_date, total_price) VALUES (?, ?, CURDATE(), ?, ?, ?)");
            $stmt->bind_param('iissd', $userId, $roomId, $checkIn, $checkOut, $totalPrice);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO bookings (user_id, room_id, booking_date, check_in_date, check_out_date) VALUES (?, ?, CURDATE(), ?, ?)");
            $stmt->bind_param('iiss', $userId, $roomId, $checkIn, $checkOut);
        }
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateStatus($id, $status) {
        if (!$this->columnExists('bookings', 'status')) {
            return false;
        }
        $stmt = $this->conn->prepare("UPDATE bookings SET status=? WHERE id=?");
        $stmt->bind_param('si', $status, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function cancel($id) {
        return $this->updateStatus($id, 'cancelled');
    }

    public function confirm($id) {
        return $this->updateStatus($id, 'confirmed');
    }

    /**
     * Check if a room is available for the given date range.
     * The overlap logic is: existing.check_in_date <= desired.check_out
     * AND existing.check_out_date >= desired.check_in
     */
    public function isRoomAvailable($roomId, $checkIn, $checkOut, $excludeBookingId = null) {
        $hasStatus = $this->columnExists('bookings', 'status');

        $sql = "SELECT COUNT(*) as count FROM bookings WHERE room_id=?";
        if ($hasStatus) {
            $sql .= " AND status IN ('pending', 'confirmed')";
        }
        $sql .= " AND check_in_date <= ? AND check_out_date >= ?";
        if ($excludeBookingId) {
            $sql .= " AND id != ?";
        }

        $stmt = $this->conn->prepare($sql);
        if ($excludeBookingId) {
            $stmt->bind_param('issi', $roomId, $checkOut, $checkIn, $excludeBookingId);
        } else {
            $stmt->bind_param('iss', $roomId, $checkOut, $checkIn);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return isset($row['count']) ? ($row['count'] == 0) : true;
    }

    public function getUpcomingBookings() {
        $sql = "SELECT b.*, r.room_number, r.room_type, u.name as user_name FROM bookings b JOIN rooms r ON b.room_id = r.id JOIN users u ON b.user_id = u.id WHERE b.check_in_date >= CURDATE()";
        if ($this->columnExists('bookings', 'status')) {
            $sql .= " AND b.status IN ('pending', 'confirmed')";
        }
        $sql .= " ORDER BY b.check_in_date ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }
}