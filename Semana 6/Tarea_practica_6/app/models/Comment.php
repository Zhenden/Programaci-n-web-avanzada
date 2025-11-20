<?php
/**
 * Modelo de Comentarios
 */
class Comment extends Model {
    
    /**
     * Obtiene todos los comentarios de un plato específico
     */
    public function getByDishId($dishId) {
        try {
            $stmt = $this->executePrepared(
                "SELECT c.*, u.username, u.email 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 WHERE c.dish_id = ? 
                 ORDER BY c.created_at DESC",
                'i',
                [$dishId]
            );
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error en getByDishId: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene todos los comentarios de un usuario específico
     */
    public function getByUserId($userId) {
        try {
            $stmt = $this->executePrepared(
                "SELECT c.*, d.name as dish_name 
                 FROM comments c 
                 JOIN dishes d ON c.dish_id = d.id 
                 WHERE c.user_id = ? 
                 ORDER BY c.created_at DESC",
                'i',
                [$userId]
            );
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error en getByUserId: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Crea un nuevo comentario
     */
    public function create($userId, $dishId, $comment) {
        try {
            $stmt = $this->executePrepared(
                "INSERT INTO comments (user_id, dish_id, comment) VALUES (?, ?, ?)",
                'iis',
                [$userId, $dishId, $comment]
            );
            return $this->getLastInsertId();
        } catch (Exception $e) {
            error_log("Error en create: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene un comentario por ID
     */
    public function findById($id) {
        try {
            $stmt = $this->executePrepared(
                "SELECT c.*, u.username, u.email, d.name as dish_name 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 JOIN dishes d ON c.dish_id = d.id 
                 WHERE c.id = ?",
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
     * Elimina un comentario
     */
    public function delete($id) {
        try {
            $stmt = $this->executePrepared(
                "DELETE FROM comments WHERE id = ?",
                'i',
                [$id]
            );
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Error en delete: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Actualiza un comentario
     */
    public function update($id, $comment) {
        try {
            $stmt = $this->executePrepared(
                "UPDATE comments SET comment = ? WHERE id = ?",
                'si',
                [$comment, $id]
            );
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Error en update: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cuenta los comentarios de un plato
     */
    public function countByDishId($dishId) {
        try {
            $stmt = $this->executePrepared(
                "SELECT COUNT(*) as total FROM comments WHERE dish_id = ?",
                'i',
                [$dishId]
            );
            $result = $stmt->get_result()->fetch_assoc();
            return $result['total'];
        } catch (Exception $e) {
            error_log("Error en countByDishId: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtiene comentarios con paginación
     */
    public function getWithPagination($dishId, $limit = 10, $offset = 0) {
        try {
            $stmt = $this->executePrepared(
                "SELECT c.*, u.username, u.email 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 WHERE c.dish_id = ? 
                 ORDER BY c.created_at DESC 
                 LIMIT ? OFFSET ?",
                'iii',
                [$dishId, $limit, $offset]
            );
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error en getWithPagination: " . $e->getMessage());
            return [];
        }
    }
}