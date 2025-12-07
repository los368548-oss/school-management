<?php
/**
 * Session Class
 * Handles session management and security
 */

class Session {
    private static $instance = null;
    private $sessionLifetime = 3600; // 1 hour

    private function __construct() {
        $this->startSession();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.use_only_cookies', 1);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.gc_maxlifetime', $this->sessionLifetime);

            session_set_cookie_params([
                'lifetime' => $this->sessionLifetime,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            session_start();

            // Regenerate session ID periodically for security
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } elseif (time() - $_SESSION['created'] > 300) { // 5 minutes
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function destroy() {
        session_unset();
        session_destroy();
        session_regenerate_id(true);
    }

    public function setFlash($key, $value) {
        $_SESSION['flash'][$key] = $value;
    }

    public function getFlash($key, $default = null) {
        $value = $_SESSION['flash'][$key] ?? $default;
        if (isset($_SESSION['flash'][$key])) {
            unset($_SESSION['flash'][$key]);
        }
        return $value;
    }

    public function hasFlash($key) {
        return isset($_SESSION['flash'][$key]);
    }

    public function setUser($user) {
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_activity'] = time();
    }

    public function getUser() {
        return $_SESSION['user'] ?? null;
    }

    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public function getRole() {
        return $_SESSION['role'] ?? null;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['last_activity']);
    }

    public function isAdmin() {
        return $this->getRole() === 'Admin';
    }

    public function isStudent() {
        return $this->getRole() === 'Student';
    }

    public function updateActivity() {
        $_SESSION['last_activity'] = time();
    }

    public function checkTimeout() {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $this->sessionLifetime) {
                $this->destroy();
                return false;
            }
        }
        return true;
    }

    public function regenerateId() {
        session_regenerate_id(true);
    }

    // CSRF token methods
    public function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    private function __wakeup() {}
}
?>