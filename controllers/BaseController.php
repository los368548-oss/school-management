<?php
/**
 * Base Controller Class
 * All controllers should extend this class
 */

class BaseController {
    protected $session;
    protected $security;
    protected $validator;

    public function __construct() {
        $this->session = Session::getInstance();
        $this->security = Security::getInstance();
        $this->validator = new Validator();
    }

    /**
     * Load a view file
     */
    protected function view($view, $data = []) {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($viewFile)) {
            // Extract data to make variables available in view
            extract($data);

            // Determine layout
            $layout = $data['layout'] ?? 'main';
            $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';

            if (!file_exists($layoutFile)) {
                $layoutFile = __DIR__ . '/../views/layouts/main.php';
            }

            // Start output buffering
            ob_start();
            require_once $viewFile;
            $content = ob_get_clean();

            // Add content to data
            $data['content'] = $content;

            // Extract data again for layout
            extract($data);

            // Include the layout
            require_once $layoutFile;
        } else {
            throw new Exception("View file not found: {$view}");
        }
    }

    /**
     * Load a partial view (for AJAX responses)
     */
    protected function partial($view, $data = []) {
        $viewFile = __DIR__ . '/../views/partials/' . $view . '.php';

        if (file_exists($viewFile)) {
            extract($data);
            require_once $viewFile;
        } else {
            throw new Exception("Partial view file not found: {$view}");
        }
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect to another URL
     */
    protected function redirect($url, $message = null, $type = 'success') {
        if ($message) {
            $this->session->setFlash('message', $message);
            $this->session->setFlash('message_type', $type);
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * Check if user is logged in
     */
    protected function requireAuth() {
        if (!$this->session->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    /**
     * Check if user is admin
     */
    protected function requireAdmin() {
        $this->requireAuth();
        if (!$this->session->isAdmin()) {
            $this->redirect('/unauthorized');
        }
    }

    /**
     * Check if user is student
     */
    protected function requireStudent() {
        $this->requireAuth();
        if (!$this->session->isStudent()) {
            $this->redirect('/unauthorized');
        }
    }

    /**
     * Get POST data
     */
    protected function getPostData() {
        return $_POST;
    }

    /**
     * Get GET data
     */
    protected function getQueryData() {
        return $_GET;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrf() {
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $this->session->generateCsrfToken();

        if (!$this->security->validateCsrfToken($token, $sessionToken)) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
    }

    /**
     * Handle AJAX requests
     */
    protected function handleAjax($callback) {
        if ($this->security->isAjaxRequest()) {
            try {
                $result = call_user_func($callback);
                $this->json(['success' => true, 'data' => $result]);
            } catch (Exception $e) {
                $this->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        } else {
            $this->json(['error' => 'Invalid request'], 400);
        }
    }

    /**
     * Get current user
     */
    protected function getCurrentUser() {
        return $this->session->getUser();
    }

    /**
     * Get current user ID
     */
    protected function getCurrentUserId() {
        return $this->session->getUserId();
    }

    /**
     * Log user action
     */
    protected function logAction($action, $details = []) {
        $db = Database::getInstance();

        $db->query("INSERT INTO audit_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)")
           ->bind(1, $this->getCurrentUserId())
           ->bind(2, $action)
           ->bind(3, json_encode($details))
           ->bind(4, $this->security->getClientIP())
           ->bind(5, $_SERVER['HTTP_USER_AGENT'] ?? '')
           ->execute();
    }
}
?>