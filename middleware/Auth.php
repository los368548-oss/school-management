<?php
/**
 * Authentication Middleware
 *
 * Checks if user is authenticated before allowing access to protected routes
 */

class AuthMiddleware {
    public function handle() {
        if (!Security::isLoggedIn()) {
            // Store current URL for redirect after login
            $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
            if ($currentUrl !== '/login' && $currentUrl !== '/logout') {
                Session::set('redirect_after_login', $currentUrl);
            }

            header('Location: /login');
            exit;
        }

        // Check if session is expired
        $user = Security::getCurrentUser();
        if ($user) {
            $config = require BASE_PATH . 'config/security.php';
            $sessionLifetime = $config['session_lifetime'];

            if (isset($_SESSION['last_activity']) &&
                (time() - $_SESSION['last_activity']) > $sessionLifetime) {
                Security::logout();
                header('Location: /login?expired=1');
                exit;
            }

            $_SESSION['last_activity'] = time();
        }
    }
}
?>