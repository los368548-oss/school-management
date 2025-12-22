<?php
/**
 * Role Check Middleware
 *
 * Provides granular role-based access control for different user types
 */

class RoleCheckMiddleware {
    private $rolePermissions = [
        'admin' => [
            'allowed_routes' => [
                '/admin',
                '/api',
                '/install'
            ],
            'restricted_actions' => [], // Admins can do everything
        ],
        'student' => [
            'allowed_routes' => [
                '/student',
                '/api/v1/exams',  // Read-only access to exam info
                '/api/v1/reports' // Limited reports
            ],
            'restricted_actions' => [
                'create', 'update', 'delete', 'admin', 'manage'
            ],
            'allowed_student_data_only' => true, // Can only access their own data
        ],
        'teacher' => [
            'allowed_routes' => [
                '/teacher',
                '/api/v1/students', // Limited access
                '/api/v1/attendance',
                '/api/v1/exams'
            ],
            'restricted_actions' => [
                'delete', 'admin', 'system'
            ],
            'class_teacher_only' => true, // Can only access their class data
        ]
    ];

    public function handle() {
        $user = Security::getCurrentUser();

        if (!$user) {
            return; // Let Auth middleware handle unauthenticated users
        }

        $userRole = $user['role'];
        $currentRoute = $this->getCurrentRoute();

        // Check if role exists in permissions
        if (!isset($this->rolePermissions[$userRole])) {
            $this->denyAccess('Unknown user role');
        }

        $permissions = $this->rolePermissions[$userRole];

        // Check route access
        if (!$this->hasRouteAccess($currentRoute, $permissions)) {
            $this->denyAccess('Access denied to this section');
        }

        // Check action restrictions
        if ($this->hasRestrictedAction($permissions)) {
            $this->denyAccess('Action not permitted for your role');
        }

        // Apply data-level restrictions
        $this->applyDataRestrictions($user, $permissions);
    }

    private function getCurrentRoute() {
        $requestURI = $_SERVER['REQUEST_URI'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

        // Remove script name and query string
        $route = str_replace($scriptName, '', $requestURI);
        $route = parse_url($route, PHP_URL_PATH);
        $route = rtrim($route, '/');

        return $route ?: '/';
    }

    private function hasRouteAccess($route, $permissions) {
        $allowedRoutes = $permissions['allowed_routes'];

        foreach ($allowedRoutes as $allowedRoute) {
            if (strpos($route, $allowedRoute) === 0) {
                return true;
            }
        }

        return false;
    }

    private function hasRestrictedAction($permissions) {
        if (empty($permissions['restricted_actions'])) {
            return false;
        }

        $currentAction = $this->getCurrentAction();

        foreach ($permissions['restricted_actions'] as $restrictedAction) {
            if (stripos($currentAction, $restrictedAction) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getCurrentAction() {
        // Extract action from URL or POST data
        $action = $_GET['action'] ?? $_POST['action'] ?? '';

        // Check for action in URL path
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));

        // Common action keywords
        $actionKeywords = [
            'create', 'edit', 'update', 'delete', 'manage', 'admin',
            'system', 'config', 'settings', 'install'
        ];

        foreach ($pathParts as $part) {
            if (in_array(strtolower($part), $actionKeywords)) {
                return $part;
            }
        }

        return $action;
    }

    private function applyDataRestrictions($user, $permissions) {
        // Student data restriction
        if (isset($permissions['allowed_student_data_only']) && $permissions['allowed_student_data_only']) {
            $this->restrictToStudentData($user['id']);
        }

        // Class teacher restriction
        if (isset($permissions['class_teacher_only']) && $permissions['class_teacher_only']) {
            $this->restrictToClassTeacherData($user['id']);
        }
    }

    private function restrictToStudentData($userId) {
        // Get student ID for this user
        $student = $this->getStudentByUserId($userId);

        if ($student) {
            // Store student ID in session for data filtering
            Session::set('restricted_student_id', $student['id']);
            Session::set('restricted_class_id', $student['class_id']);
        } else {
            $this->denyAccess('Student profile not found');
        }
    }

    private function restrictToClassTeacherData($userId) {
        // Get classes where this user is the class teacher
        $classes = $this->getClassesByTeacher($userId);

        if (!empty($classes)) {
            $classIds = array_column($classes, 'id');
            Session::set('restricted_class_ids', $classIds);
        } else {
            $this->denyAccess('No classes assigned as class teacher');
        }
    }

    private function getStudentByUserId($userId) {
        $db = Database::getInstance();
        return $db->selectOne(
            "SELECT id, class_id FROM students WHERE user_id = ? AND status = 'active'",
            [$userId]
        );
    }

    private function getClassesByTeacher($userId) {
        $db = Database::getInstance();
        return $db->select(
            "SELECT id, class_name, section FROM classes WHERE class_teacher_id = ?",
            [$userId]
        );
    }

    private function denyAccess($reason = 'Access denied') {
        // Log the access denial
        $user = Security::getCurrentUser();
        $route = $this->getCurrentRoute();

        Security::logActivity('access_denied',
            "Role: {$user['role']}, Route: {$route}, Reason: {$reason}");

        // Send appropriate response
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['error' => 'Access denied', 'reason' => $reason]);
            exit;
        } else {
            http_response_code(403);
            die("Access Denied: {$reason}");
        }
    }

    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    // Helper methods for checking permissions
    public static function canAccessRoute($route, $userRole = null) {
        if (!$userRole) {
            $user = Security::getCurrentUser();
            $userRole = $user ? $user['role'] : null;
        }

        if (!$userRole) {
            return false;
        }

        $middleware = new self();
        $permissions = $middleware->rolePermissions[$userRole] ?? [];

        return $middleware->hasRouteAccess($route, $permissions);
    }

    public static function canPerformAction($action, $userRole = null) {
        if (!$userRole) {
            $user = Security::getCurrentUser();
            $userRole = $user ? $user['role'] : null;
        }

        if (!$userRole) {
            return false;
        }

        $middleware = new self();
        $permissions = $middleware->rolePermissions[$userRole] ?? [];

        return !$middleware->hasRestrictedAction($permissions);
    }

    public static function getUserRestrictions() {
        $user = Security::getCurrentUser();
        if (!$user) {
            return [];
        }

        return [
            'student_id' => Session::get('restricted_student_id'),
            'class_ids' => Session::get('restricted_class_ids'),
            'role' => $user['role']
        ];
    }
}
?>