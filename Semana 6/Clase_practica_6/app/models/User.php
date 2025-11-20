<?php
require_once 'Model.php';

class User extends Model {
    
    /**
     * Get all users with their roles
     */
    public function all() {
        $order = $this->columnExists('users', 'created_at') ? 'u.created_at DESC' : 'u.id DESC';
        $res = $this->conn->query("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY $order");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Find user by ID
     */
    public function find($id) {
        $stmt = $this->conn->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE u.id=?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("
            SELECT users.*, roles.name AS role_name
            FROM users
            JOIN roles ON roles.id = users.role_id
            WHERE users.email = ?
            LIMIT 1 
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }

    
    /**
     * Create new user
     */
    public function create($name, $email, $password, $roleId) {
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sssi', $name, $email, $password, $roleId);
        return $stmt->execute();
    }
    
    /**
     * Update user
     */
    public function update($id, $name, $email, $roleId) {
        $stmt = $this->conn->prepare("UPDATE users SET name=?, email=?, role_id=? WHERE id=?");
        $stmt->bind_param('ssii', $name, $email, $roleId, $id);
        return $stmt->execute();
    }
    
    /**
     * Update user password
     */
    public function updatePassword($id, $password) {
        $stmt = $this->conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param('si', $password, $id);
        return $stmt->execute();
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
            if (!$stmt) throw new Exception('Error preparando la eliminación de usuario');
            $stmt->bind_param('i', $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1451) {
                throw new Exception('No se puede eliminar el usuario porque tiene registros dependientes (reservas u otros)', 0, $e);
            }
            throw new Exception('Error al eliminar usuario: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Get all roles
     */
    public function getRoles() {
        $res = $this->conn->query("SELECT * FROM roles ORDER BY name");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Find role by ID
     */
    public function findRole($id) {
        $stmt = $this->conn->prepare("SELECT * FROM roles WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row;
    }
    
    /**
     * Get users by role name
     */
    public function getByRole($roleName) {
        $stmt = $this->conn->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u 
            JOIN roles r ON u.role_id = r.id 
            WHERE r.name = ?
            ORDER BY u.name
        ");
        $stmt->bind_param('s', $roleName);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }
}