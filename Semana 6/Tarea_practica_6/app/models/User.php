<?php
class User extends Model {
    /**
     * Busca usuario por email
     */
    public function findByEmail($email) {
        try {
            $stmt = $this->executePrepared(
                "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id=r.id WHERE u.email=?",
                's',
                [$email]
            );
            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error en findByEmail: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Busca usuario por ID
     */
    public function findById($id) {
        try {
            $stmt = $this->executePrepared(
                "SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id=r.id WHERE u.id=?",
                'i',
                [$id]
            );
            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error en findById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Crea un nuevo usuario
     */
    public function create($username, $email, $password, $role_id = 4) {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->executePrepared(
                "INSERT INTO users (username, email, password, role_id) VALUES (?,?,?,?)",
                'sssi',
                [$username, $email, $hash, $role_id]
            );
            return $this->getLastInsertId();
        } catch (Exception $e) {
            error_log("Error en create: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Elimina un usuario por ID
     */
    public function delete($id) {
        $stmt = $this->executePrepared(
            "DELETE FROM users WHERE id=?",
            'i',
            [$id]
        );
        return $stmt->affected_rows > 0;
    }
}