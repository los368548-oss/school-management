<?php
/**
 * Base Controller Class
 *
 * All controllers should extend this class
 */

class BaseController {
    protected $db;
    protected $session;
    protected $validator;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->session = new Session();
        $this->validator = new Validator();
    }

    /**
     * Load a view file
     */
    protected function view($view, $data = []) {
        $viewFile = BASE_PATH . 'views/' . $view . '.php';

        if (file_exists($viewFile)) {
            // Extract data to make variables available in view
            extract($data);

            // Include the view
            require_once $viewFile;
        } else {
            throw new Exception("View file not found: {$viewFile}");
        }
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    /**
     * Check if user is authenticated
     */
    protected function requireAuth() {
        if (!Security::isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    /**
     * Check if user has specific role
     */
    protected function requireRole($role) {
        $this->requireAuth();
        $user = Security::getCurrentUser();
        if ($user['role'] !== $role) {
            $this->redirect('/unauthorized');
        }
    }

    /**
     * Get current academic year
     */
    protected function getCurrentAcademicYear() {
        $academicYearId = Session::getAcademicYear();
        if (!$academicYearId) {
            // If no academic year is set, get the active one
            $activeYear = $this->db->selectOne("SELECT id FROM academic_years WHERE is_active = 1");
            if ($activeYear) {
                $academicYearId = $activeYear['id'];
                Session::setAcademicYear($academicYearId);
            }
        }
        return $academicYearId;
    }

    /**
     * Set JSON response
     */
    protected function json($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Handle AJAX requests
     */
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get POST data
     */
    protected function getPostData() {
        return Validator::sanitize($_POST);
    }

    /**
     * Get GET data
     */
    protected function getQueryData() {
        return Validator::sanitize($_GET);
    }

    /**
     * Set flash message
     */
    protected function setFlash($type, $message) {
        Session::setFlash($type, $message);
    }

    /**
     * Get flash messages
     */
    protected function getFlash($type = null) {
        return Session::getFlash($type);
    }

    /**
     * Log user activity
     */
    protected function logActivity($action, $details = '') {
        Security::logActivity($action, $details);
    }
}
?>