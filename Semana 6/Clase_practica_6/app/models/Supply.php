<?php
require_once 'Model.php';

class Supply extends Model {
    
    /**
     * Get all supplies
     */
    public function all() {
        $order = $this->columnExists('supplies', 'created_at') ? 's.created_at DESC' : 's.id DESC';
        $res = $this->conn->query("SELECT s.* FROM supplies s ORDER BY $order");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Find supply by ID
     */
    public function find($id) {
        $stmt = $this->conn->prepare("
            SELECT s.* 
            FROM supplies s 
            WHERE s.id=?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }
    
    /**
     * Create new supply request
     */
    public function create($name, $quantity = null) {
        // If quantity column exists, include it, otherwise insert only name
        if ($this->columnExists('supplies', 'quantity')) {
            $stmt = $this->conn->prepare("INSERT INTO supplies (name, quantity) VALUES (?, ?)");
            $stmt->bind_param('si', $name, $quantity);
        } else {
            $stmt = $this->conn->prepare("INSERT INTO supplies (name) VALUES (?)");
            $stmt->bind_param('s', $name);
        }
        return $stmt->execute();
    }
    
    /**
     * Update supply
     */
    public function update($id, $name, $quantity = null, $status = null) {
        $fields = [];
        $types = '';
        $values = [];

        $fields[] = 'name = ?'; $types .= 's'; $values[] = $name;
        if ($this->columnExists('supplies', 'quantity') && $quantity !== null) {
            $fields[] = 'quantity = ?'; $types .= 'i'; $values[] = $quantity;
        }
        if ($this->columnExists('supplies', 'status') && $status !== null) {
            $fields[] = 'status = ?'; $types .= 's'; $values[] = $status;
        }

        $sql = "UPDATE supplies SET " . implode(', ', $fields) . " WHERE id = ?";
        $types .= 'i'; $values[] = $id;

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }
    
    /**
     * Delete supply
     */
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM supplies WHERE id=?");
            if (!$stmt) throw new Exception('Error preparando la eliminación del suministro');
            $stmt->bind_param('i', $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1451) {
                throw new Exception('No se puede eliminar el suministro porque está referenciado en otros registros', 0, $e);
            }
            throw new Exception('Error al eliminar suministro: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Update supply status
     */
    public function updateStatus($id, $status) {
        if (!$this->columnExists('supplies', 'status')) {
            return false;
        }
        $stmt = $this->conn->prepare("UPDATE supplies SET status=? WHERE id=?");
        $stmt->bind_param('si', $status, $id);
        return $stmt->execute();
    }
    
    /**
     * Get requested supplies
     */
    public function getRequestedSupplies() {
        if (!$this->columnExists('supplies', 'status')) {
            return $this->all();
        }
        $order = $this->columnExists('supplies', 'created_at') ? 's.created_at DESC' : 's.id DESC';
        $res = $this->conn->query("SELECT s.* FROM supplies s WHERE s.status = 'requested' ORDER BY $order");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get offered supplies
     */
    public function getOfferedSupplies() {
        if (!$this->columnExists('supplies', 'status')) {
            return $this->all();
        }
        $order = $this->columnExists('supplies', 'created_at') ? 's.created_at DESC' : 's.id DESC';
        $res = $this->conn->query("SELECT s.* FROM supplies s WHERE s.status = 'offered' ORDER BY $order");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}