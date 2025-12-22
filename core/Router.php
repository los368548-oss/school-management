<?php
/**
 * Router Class
 *
 * Handles URL routing and dispatches requests to appropriate controllers
 */

class Router {
    private $routes = [];

    public function __construct() {
        $this->loadRoutes();
    }

    private function loadRoutes() {
        // Define routes
        $this->routes = [
            // Public routes
            '' => ['controller' => 'PublicController', 'action' => 'index'],
            'home' => ['controller' => 'PublicController', 'action' => 'index'],
            'about' => ['controller' => 'PublicController', 'action' => 'about'],
            'courses' => ['controller' => 'PublicController', 'action' => 'courses'],
            'events' => ['controller' => 'PublicController', 'action' => 'events'],
            'gallery' => ['controller' => 'PublicController', 'action' => 'gallery'],
            'contact' => ['controller' => 'PublicController', 'action' => 'contact'],
            'admission' => ['controller' => 'PublicController', 'action' => 'admission'],

            // Authentication routes
            'login' => ['controller' => 'AuthController', 'action' => 'login'],
            'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
            'forgot-password' => ['controller' => 'AuthController', 'action' => 'forgotPassword'],

            // Admin routes
            'admin' => ['controller' => 'AdminController', 'action' => 'index', 'middleware' => 'Auth'],
            'admin/select-academic-year' => ['controller' => 'AdminController', 'action' => 'selectAcademicYear', 'middleware' => 'Auth'],
            'admin/dashboard' => ['controller' => 'AdminController', 'action' => 'dashboard', 'middleware' => 'Auth'],
            'admin/students' => ['controller' => 'AdminController', 'action' => 'students', 'middleware' => 'Auth'],
            'admin/classes' => ['controller' => 'AdminController', 'action' => 'classes', 'middleware' => 'Auth'],
            'admin/add-class' => ['controller' => 'AdminController', 'action' => 'addClass', 'middleware' => 'Auth'],
            'admin/subjects' => ['controller' => 'AdminController', 'action' => 'subjects', 'middleware' => 'Auth'],
            'admin/add-subject' => ['controller' => 'AdminController', 'action' => 'addSubject', 'middleware' => 'Auth'],
            'admin/assign-subject' => ['controller' => 'AdminController', 'action' => 'assignSubjectToClass', 'middleware' => 'Auth'],
            'admin/attendance' => ['controller' => 'AdminController', 'action' => 'attendance', 'middleware' => 'Auth'],
            'admin/mark-attendance' => ['controller' => 'AdminController', 'action' => 'markAttendance', 'middleware' => 'Auth'],
            'admin/save-attendance' => ['controller' => 'AdminController', 'action' => 'saveAttendance', 'middleware' => 'Auth'],
            'admin/attendance-report' => ['controller' => 'AdminController', 'action' => 'attendanceReport', 'middleware' => 'Auth'],
            'admin/exams' => ['controller' => 'AdminController', 'action' => 'exams', 'middleware' => 'Auth'],
            'admin/create-exam' => ['controller' => 'AdminController', 'action' => 'createExam', 'middleware' => 'Auth'],
            'admin/enter-results' => ['controller' => 'AdminController', 'action' => 'enterResults', 'middleware' => 'Auth'],
            'admin/save-results' => ['controller' => 'AdminController', 'action' => 'saveResults', 'middleware' => 'Auth'],
            'admin/generate-admit-card' => ['controller' => 'AdminController', 'action' => 'generateAdmitCard', 'middleware' => 'Auth'],
            'admin/generate-marksheet' => ['controller' => 'AdminController', 'action' => 'generateMarksheet', 'middleware' => 'Auth'],
            'admin/fees' => ['controller' => 'AdminController', 'action' => 'fees', 'middleware' => 'Auth'],
            'admin/events' => ['controller' => 'AdminController', 'action' => 'events', 'middleware' => 'Auth'],
            'admin/gallery' => ['controller' => 'AdminController', 'action' => 'gallery', 'middleware' => 'Auth'],
            'admin/reports' => ['controller' => 'AdminController', 'action' => 'reports', 'middleware' => 'Auth'],
            'admin/settings' => ['controller' => 'AdminController', 'action' => 'settings', 'middleware' => 'Auth'],

            // Student routes
            'student' => ['controller' => 'StudentController', 'action' => 'index', 'middleware' => 'Auth'],
            'student/dashboard' => ['controller' => 'StudentController', 'action' => 'dashboard', 'middleware' => 'Auth'],
            'student/profile' => ['controller' => 'StudentController', 'action' => 'profile', 'middleware' => 'Auth'],
            'student/attendance' => ['controller' => 'StudentController', 'action' => 'attendance', 'middleware' => 'Auth'],
            'student/results' => ['controller' => 'StudentController', 'action' => 'results', 'middleware' => 'Auth'],
            'student/fees' => ['controller' => 'StudentController', 'action' => 'fees', 'middleware' => 'Auth'],

            // Print routes
            'print/admit-card' => ['controller' => 'PrintController', 'action' => 'admitCard', 'middleware' => 'Auth'],
            'print/marksheet' => ['controller' => 'PrintController', 'action' => 'marksheet', 'middleware' => 'Auth'],
            'print/transfer-certificate' => ['controller' => 'PrintController', 'action' => 'transferCertificate', 'middleware' => 'Auth'],
            'print/fee-receipt' => ['controller' => 'PrintController', 'action' => 'feeReceipt', 'middleware' => 'Auth'],
            'print/id-card' => ['controller' => 'PrintController', 'action' => 'idCard', 'middleware' => 'Auth'],

            // API routes
            'api/v1/auth/login' => ['controller' => 'ApiController', 'action' => 'login'],
            'api/v1/students' => ['controller' => 'ApiController', 'action' => 'students'],
            'api/v1/fees' => ['controller' => 'ApiController', 'action' => 'fees'],
            'api/v1/exams' => ['controller' => 'ApiController', 'action' => 'exams'],
            'api/v1/events' => ['controller' => 'ApiController', 'action' => 'events'],
            'api/v1/gallery' => ['controller' => 'ApiController', 'action' => 'gallery'],
            'api/v1/reports' => ['controller' => 'ApiController', 'action' => 'reports'],

            // Installation
            'install' => ['controller' => 'InstallController', 'action' => 'index'],
        ];
    }

    public function route($url) {
        $route = $this->findRoute($url);

        if ($route) {
            // Check middleware
            if (isset($route['middleware'])) {
                $this->runMiddleware($route['middleware']);
            }

            // Dispatch to controller
            $this->dispatch($route['controller'], $route['action']);
        } else {
            // 404 Not Found
            $this->dispatch('ErrorController', 'notFound');
        }
    }

    private function findRoute($url) {
        // Exact match
        if (isset($this->routes[$url])) {
            return $this->routes[$url];
        }

        // Pattern matching for dynamic routes
        foreach ($this->routes as $pattern => $route) {
            if ($this->matchPattern($pattern, $url)) {
                return $route;
            }
        }

        return null;
    }

    private function matchPattern($pattern, $url) {
        // Convert pattern to regex
        $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $pattern);
        return preg_match("#^{$pattern}$#", $url);
    }

    private function runMiddleware($middleware) {
        $middlewareClass = $middleware . 'Middleware';
        if (class_exists($middlewareClass)) {
            $middlewareInstance = new $middlewareClass();
            $middlewareInstance->handle();
        }
    }

    private function dispatch($controller, $action) {
        $controllerFile = BASE_PATH . 'controllers/' . $controller . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            if (class_exists($controller)) {
                $controllerInstance = new $controller();
                if (method_exists($controllerInstance, $action)) {
                    $controllerInstance->$action();
                } else {
                    $this->dispatch('ErrorController', 'methodNotFound');
                }
            } else {
                $this->dispatch('ErrorController', 'classNotFound');
            }
        } else {
            $this->dispatch('ErrorController', 'fileNotFound');
        }
    }
}
?>