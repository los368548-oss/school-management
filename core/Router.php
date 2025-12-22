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
             'admin' => ['controller' => 'DashboardController', 'action' => 'index', 'middleware' => 'Auth'],
             'admin/select-academic-year' => ['controller' => 'DashboardController', 'action' => 'selectAcademicYear', 'middleware' => 'Auth'],
             'admin/dashboard' => ['controller' => 'DashboardController', 'action' => 'dashboard', 'middleware' => 'Auth'],
             'admin/students' => ['controller' => 'StudentController', 'action' => 'students', 'middleware' => 'Auth'],
             'admin/students/add' => ['controller' => 'StudentController', 'action' => 'add', 'middleware' => 'Auth'],
             'admin/students/edit/{id}' => ['controller' => 'StudentController', 'action' => 'edit', 'middleware' => 'Auth'],
             'admin/students/view/{id}' => ['controller' => 'StudentController', 'action' => 'view', 'middleware' => 'Auth'],
             'admin/students/delete/{id}' => ['controller' => 'StudentController', 'action' => 'delete', 'middleware' => 'Auth'],
             'admin/classes' => ['controller' => 'ClassController', 'action' => 'classes', 'middleware' => 'Auth'],
             'admin/add-class' => ['controller' => 'ClassController', 'action' => 'addClass', 'middleware' => 'Auth'],
             'admin/classes/edit/{id}' => ['controller' => 'ClassController', 'action' => 'edit', 'middleware' => 'Auth'],
             'admin/classes/delete/{id}' => ['controller' => 'ClassController', 'action' => 'delete', 'middleware' => 'Auth'],
             'admin/subjects' => ['controller' => 'SubjectController', 'action' => 'subjects', 'middleware' => 'Auth'],
             'admin/add-subject' => ['controller' => 'SubjectController', 'action' => 'addSubject', 'middleware' => 'Auth'],
             'admin/assign-subject' => ['controller' => 'SubjectController', 'action' => 'assignSubjectToClass', 'middleware' => 'Auth'],
             'admin/subjects/edit/{id}' => ['controller' => 'SubjectController', 'action' => 'edit', 'middleware' => 'Auth'],
             'admin/subjects/delete/{id}' => ['controller' => 'SubjectController', 'action' => 'delete', 'middleware' => 'Auth'],
             'admin/attendance' => ['controller' => 'AttendanceController', 'action' => 'attendance', 'middleware' => 'Auth'],
             'admin/mark-attendance' => ['controller' => 'AttendanceController', 'action' => 'markAttendance', 'middleware' => 'Auth'],
             'admin/save-attendance' => ['controller' => 'AttendanceController', 'action' => 'saveAttendance', 'middleware' => 'Auth'],
             'admin/attendance-report' => ['controller' => 'AttendanceController', 'action' => 'attendanceReport', 'middleware' => 'Auth'],
             'admin/exams' => ['controller' => 'ExamController', 'action' => 'exams', 'middleware' => 'Auth'],
             'admin/create-exam' => ['controller' => 'ExamController', 'action' => 'createExam', 'middleware' => 'Auth'],
             'admin/enter-results' => ['controller' => 'ExamController', 'action' => 'enterResults', 'middleware' => 'Auth'],
             'admin/save-results' => ['controller' => 'ExamController', 'action' => 'saveResults', 'middleware' => 'Auth'],
             'admin/generate-admit-card' => ['controller' => 'ExamController', 'action' => 'generateAdmitCard', 'middleware' => 'Auth'],
             'admin/generate-marksheet' => ['controller' => 'ExamController', 'action' => 'generateMarksheet', 'middleware' => 'Auth'],
             'admin/exams/edit/{id}' => ['controller' => 'ExamController', 'action' => 'edit', 'middleware' => 'Auth'],
             'admin/exams/delete/{id}' => ['controller' => 'ExamController', 'action' => 'delete', 'middleware' => 'Auth'],
             'admin/fees' => ['controller' => 'FeeController', 'action' => 'fees', 'middleware' => 'Auth'],
             'admin/fees/add' => ['controller' => 'FeeController', 'action' => 'add', 'middleware' => 'Auth'],
             'admin/fees/edit/{id}' => ['controller' => 'FeeController', 'action' => 'edit', 'middleware' => 'Auth'],
             'admin/fees/delete/{id}' => ['controller' => 'FeeController', 'action' => 'delete', 'middleware' => 'Auth'],
             'admin/events' => ['controller' => 'EventController', 'action' => 'events', 'middleware' => 'Auth'],
             'admin/events/add' => ['controller' => 'EventController', 'action' => 'add', 'middleware' => 'Auth'],
             'admin/events/edit/{id}' => ['controller' => 'EventController', 'action' => 'edit', 'middleware' => 'Auth'],
             'admin/events/delete/{id}' => ['controller' => 'EventController', 'action' => 'delete', 'middleware' => 'Auth'],
             'admin/gallery' => ['controller' => 'GalleryController', 'action' => 'gallery', 'middleware' => 'Auth'],
             'admin/gallery/add' => ['controller' => 'GalleryController', 'action' => 'add', 'middleware' => 'Auth'],
             'admin/gallery/edit/{id}' => ['controller' => 'GalleryController', 'action' => 'edit', 'middleware' => 'Auth'],
             'admin/gallery/delete/{id}' => ['controller' => 'GalleryController', 'action' => 'delete', 'middleware' => 'Auth'],
             'admin/reports' => ['controller' => 'ReportController', 'action' => 'reports', 'middleware' => 'Auth'],
             'admin/settings' => ['controller' => 'SettingController', 'action' => 'settings', 'middleware' => 'Auth'],

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

        // Check if file exists in root controllers directory
        if (!file_exists($controllerFile)) {
            // Try subfolder structure
            $base = str_replace('Controller', '', $controller);
            $controllerFile = BASE_PATH . 'controllers/' . $base . '/' . $controller . '/' . $controller . '.php';
        }

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            if (class_exists($controller)) {
                $controllerInstance = new $controller();
                // Check if action has parameters (for routes like /edit/123)
                $urlParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                $adminIndex = array_search('admin', $urlParts);

                if ($adminIndex !== false && isset($urlParts[$adminIndex + 2])) {
                    // Extract parameter from URL
                    $param = $urlParts[$adminIndex + 2];
                    if (method_exists($controllerInstance, $action)) {
                        $controllerInstance->$action($param);
                    } else {
                        $this->dispatch('ErrorController', 'methodNotFound');
                    }
                } else {
                    if (method_exists($controllerInstance, $action)) {
                        $controllerInstance->$action();
                    } else {
                        $this->dispatch('ErrorController', 'methodNotFound');
                    }
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