<?php
/**
 * Security Class
 *
 * Handles security-related functions like CSRF protection, input sanitization, etc.
 */

class Security {
    public static function init() {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration'])) {
            self::regenerateSession();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
            self::regenerateSession();
        }

        // Set security headers
        self::setSecurityHeaders();
    }

    public static function regenerateSession() {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }

    public static function setSecurityHeaders() {
        if (!headers_sent()) {
            header('X-Frame-Options: DENY');
            header('X-Content-Type-Options: nosniff');
            header('X-XSS-Protection: 1; mode=block');
            header('Referrer-Policy: strict-origin-when-cross-origin');
            header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data: https:; font-src \'self\';');
        }
    }

    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    public static function encrypt($data, $key = null) {
        if ($key === null) {
            $key = self::getEncryptionKey();
        }
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($data, $key = null) {
        if ($key === null) {
            $key = self::getEncryptionKey();
        }
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }

    private static function getEncryptionKey() {
        $config = require BASE_PATH . 'config/security.php';
        return $config['encryption_key'];
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }

    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'role' => $_SESSION['user_role'],
                'name' => $_SESSION['user_name'] ?? '',
                'email' => $_SESSION['user_email'] ?? ''
            ];
        }
        return null;
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'login');
            exit;
        }
    }

    public static function requireRole($role) {
        self::requireLogin();
        if ($_SESSION['user_role'] !== $role) {
            header('Location: ' . BASE_URL . 'unauthorized');
            exit;
        }
    }

    public static function logout() {
        session_destroy();
        session_start();
    }

    public static function logActivity($action, $details = '') {
        $userId = $_SESSION['user_id'] ?? 0;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        $db = Database::getInstance();
        $db->insert('audit_logs', [
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
?>