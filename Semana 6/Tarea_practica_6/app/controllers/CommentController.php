<?php
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Dish.php';

class CommentController extends BaseController {
    
    /**
     * Muestra todos los comentarios (para administradores)
     */
    public function index() {
        $this->checkRole(['Administrator']);
        
        try {
            $commentModel = new Comment();
            $dishModel = new Dish();
            
            // Obtener todos los platos para el filtro
            $dishes = $dishModel->all();
            
            // Filtrar por plato si se especifica
            $dishId = $_GET['dish_id'] ?? null;
            $comments = [];
            
            if ($dishId) {
                $comments = $commentModel->getByDishId($dishId);
            }
            
            $this->view('comments/index', [
                'comments' => $comments,
                'dishes' => $dishes,
                'selected_dish' => $dishId
            ]);
            
        } catch (Exception $e) {
            error_log("Error en CommentController@index: " . $e->getMessage());
            SessionManager::set('error', 'Error al cargar los comentarios');
            $this->redirect('index.php?action=dashboard');
        }
    }
    
    /**
     * Muestra los comentarios de un plato específico
     */
    public function showByDish($dishId) {
        try {
            $commentModel = new Comment();
            $dishModel = new Dish();
            
            // Verificar que el plato existe
            $dish = $dishModel->find($dishId);
            if (!$dish) {
                SessionManager::set('error', 'Plato no encontrado');
                $this->redirect('index.php?action=dishes');
                return;
            }
            
            // Obtener comentarios del plato
            $comments = $commentModel->getByDishId($dishId);
            
            $this->view('comments/dish_comments', [
                'dish' => $dish,
                'comments' => $comments
            ]);
            
        } catch (Exception $e) {
            error_log("Error en CommentController@showByDish: " . $e->getMessage());
            SessionManager::set('error', 'Error al cargar los comentarios');
            $this->redirect('index.php?action=dishes');
        }
    }
    
    /**
     * Muestra los comentarios de un usuario específico
     */
    public function showByUser($userId) {
        // Allow users to see their own comments, and administrators to see any comments
        $currentUserId = SessionManager::get('user_id');
        $userRole = SessionManager::get('role_name');
        
        if (!$currentUserId) {
            $this->redirect('index.php?action=login');
            return;
        }
        
        // If not admin and trying to see another user's comments, redirect to their own
        if ($userRole !== 'Administrator' && $currentUserId != $userId) {
            $this->redirect('index.php?action=comments/user&id=' . $currentUserId);
            return;
        }
        
        try {
            $commentModel = new Comment();
            
            // Obtener comentarios del usuario
            $comments = $commentModel->getByUserId($userId);
            
            $this->view('comments/user_comments', [
                'comments' => $comments,
                'user_id' => $userId
            ]);
            
        } catch (Exception $e) {
            error_log("Error en CommentController@showByUser: " . $e->getMessage());
            SessionManager::set('error', 'Error al cargar los comentarios');
            $this->redirect('index.php?action=dashboard');
        }
    }
    
    /**
     * Crea un nuevo comentario
     */
    public function create() {
        // Verificar que el usuario esté autenticado
        if (!SessionManager::get('user_id')) {
            SessionManager::set('error', 'Debes iniciar sesión para comentar');
            $this->redirect('index.php?action=login');
            return;
        }
        
        try {
            // Validar datos
            $dishId = intval($_POST['dish_id'] ?? 0);
            $comment = trim($_POST['comment'] ?? '');
            $userId = SessionManager::get('user_id');
            
            // Preservar parámetros de URL para redirección
            $redirectParams = [];
            if (isset($_POST['page']) && intval($_POST['page']) > 1) {
                $redirectParams['page'] = intval($_POST['page']);
            }
            if (isset($_POST['sort']) && in_array($_POST['sort'], ['newest', 'oldest'])) {
                $redirectParams['sort'] = $_POST['sort'];
            }
            
            if (empty($comment)) {
                SessionManager::set('error', 'El comentario no puede estar vacío');
                $this->redirectWithParams("index.php?action=dish&id=$dishId", $redirectParams, 'comments-section');
                return;
            }
            
            if (strlen($comment) > 1000) {
                SessionManager::set('error', 'El comentario es demasiado largo (máximo 1000 caracteres)');
                $this->redirectWithParams("index.php?action=dish&id=$dishId", $redirectParams, 'comments-section');
                return;
            }
            
            // Verificar que el plato existe
            $dishModel = new Dish();
            $dish = $dishModel->find($dishId);
            if (!$dish) {
                SessionManager::set('error', 'Plato no encontrado');
                $this->redirect('index.php?action=dishes');
                return;
            }
            
            // Crear comentario
            $commentModel = new Comment();
            $commentId = $commentModel->create($userId, $dishId, $comment);
            
            if ($commentId) {
                SessionManager::set('success', 'Comentario agregado exitosamente');
                // Invalidar caché de comentarios
                CacheManager::getInstance()->deleteByPrefix('comments:');
                // Redirigir con ancla al nuevo comentario
                $redirectParams['new_comment'] = $commentId;
                $this->redirectWithParams("index.php?action=dish&id=$dishId", $redirectParams, 'comment-' . $commentId);
            } else {
                SessionManager::set('error', 'Error al agregar el comentario');
                $this->redirectWithParams("index.php?action=dish&id=$dishId", $redirectParams, 'comments-section');
            }
            
        } catch (Exception $e) {
            error_log("Error en CommentController@create: " . $e->getMessage());
            SessionManager::set('error', 'Error al crear el comentario');
            $dishId = intval($_POST['dish_id'] ?? 0);
            $this->redirectWithParams("index.php?action=dish&id=$dishId", $redirectParams, 'comments-section');
        }
    }
    
    /**
     * Elimina un comentario
     */
    public function delete() {
        // Verificar que el usuario esté autenticado
        if (!SessionManager::get('user_id')) {
            SessionManager::set('error', 'Debes iniciar sesión');
            $this->redirect('index.php?action=login');
            return;
        }
        
        try {
            $commentId = intval($_POST['comment_id'] ?? 0);
            $userId = SessionManager::get('user_id');
            $userRole = SessionManager::get('role_name');
            
            // Preservar parámetros de URL para redirección
            $redirectParams = [];
            if (isset($_POST['page']) && intval($_POST['page']) > 1) {
                $redirectParams['page'] = intval($_POST['page']);
            }
            if (isset($_POST['sort']) && in_array($_POST['sort'], ['newest', 'oldest'])) {
                $redirectParams['sort'] = $_POST['sort'];
            }
            
            // Obtener el comentario
            $commentModel = new Comment();
            $comment = $commentModel->findById($commentId);
            
            if (!$comment) {
                SessionManager::set('error', 'Comentario no encontrado');
                $this->redirect('index.php?action=dishes');
                return;
            }
            
            // Verificar permisos: solo el autor o administrador pueden eliminar
            if ($comment['user_id'] != $userId && $userRole != 'Administrator') {
                SessionManager::set('error', 'No tienes permisos para eliminar este comentario');
                $this->redirectWithParams("index.php?action=dish&id=" . $comment['dish_id'], $redirectParams, 'comments-section');
                return;
            }
            
            // Validar CSRF token
            if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Token CSRF inválido');
            }
            
            // Eliminar comentario
            if ($commentModel->delete($commentId)) {
                SessionManager::set('success', 'Comentario eliminado exitosamente');
                // Invalidar caché de comentarios
                CacheManager::getInstance()->deleteByPrefix('comments:');
                // Indicar que se eliminó un comentario
                $redirectParams['comment_deleted'] = $commentId;
                
                // Debug: log the redirection parameters
                error_log("DEBUG: Deleting comment ID: $commentId, dish ID: " . $comment['dish_id'] . ", redirect params: " . json_encode($redirectParams));
            } else {
                SessionManager::set('error', 'Error al eliminar el comentario');
            }
            
            $this->redirectWithParams("index.php?action=dish&id=" . $comment['dish_id'], $redirectParams, 'comments-section');
            
        } catch (Exception $e) {
            error_log("Error en CommentController@delete: " . $e->getMessage());
            SessionManager::set('error', 'Error al eliminar el comentario');
            $this->redirect('index.php?action=dishes');
        }
    }
    
    /**
     * Actualiza un comentario
     */
    public function update() {
        // Verificar que el usuario esté autenticado
        if (!SessionManager::get('user_id')) {
            SessionManager::set('error', 'Debes iniciar sesión');
            $this->redirect('index.php?action=login');
            return;
        }
        
        try {
            $commentId = intval($_POST['comment_id'] ?? 0);
            $newComment = trim($_POST['comment'] ?? '');
            $userId = SessionManager::get('user_id');
            
            // Preservar parámetros de URL para redirección
            $redirectParams = [];
            if (isset($_POST['page']) && intval($_POST['page']) > 1) {
                $redirectParams['page'] = intval($_POST['page']);
            }
            if (isset($_POST['sort']) && in_array($_POST['sort'], ['newest', 'oldest'])) {
                $redirectParams['sort'] = $_POST['sort'];
            }
            
            if (empty($newComment)) {
                SessionManager::set('error', 'El comentario no puede estar vacío');
                $this->redirect('index.php?action=dishes');
                return;
            }
            
            // Obtener el comentario original
            $commentModel = new Comment();
            $originalComment = $commentModel->findById($commentId);
            
            if (!$originalComment) {
                SessionManager::set('error', 'Comentario no encontrado');
                $this->redirect('index.php?action=dishes');
                return;
            }
            
            // Verificar que el usuario sea el autor
            if ($originalComment['user_id'] != $userId) {
                SessionManager::set('error', 'No tienes permisos para editar este comentario');
                $this->redirectWithParams("index.php?action=dish&id=" . $originalComment['dish_id'], $redirectParams, 'comments-section');
                return;
            }
            
            // Validar CSRF token
            if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Token CSRF inválido');
            }
            
            // Actualizar comentario
            if ($commentModel->update($commentId, $newComment)) {
                SessionManager::set('success', 'Comentario actualizado exitosamente');
                // Invalidar caché de comentarios
                CacheManager::getInstance()->deleteByPrefix('comments:');
                // Indicar que se actualizó un comentario
                $redirectParams['comment_updated'] = $commentId;
                $this->redirectWithParams("index.php?action=dish&id=" . $originalComment['dish_id'], $redirectParams, 'comment-' . $commentId);
            } else {
                SessionManager::set('error', 'Error al actualizar el comentario');
                $this->redirectWithParams("index.php?action=dish&id=" . $originalComment['dish_id'], $redirectParams, 'comments-section');
            }
            
        } catch (Exception $e) {
            error_log("Error en CommentController@update: " . $e->getMessage());
            SessionManager::set('error', 'Error al actualizar el comentario');
            $this->redirect('index.php?action=dishes');
        }
    }
}