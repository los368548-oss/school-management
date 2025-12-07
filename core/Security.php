<?php
/**
 * Security Class
 * Handles security-related operations
 */

class Security {
    private static $instance = null;

    private function __construct() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Hash password using bcrypt
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password against hash
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Generate CSRF token
     */
    public function generateCsrfToken() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Validate CSRF token
     */
    public function validateCsrfToken($token, $sessionToken) {
        return hash_equals($sessionToken, $token);
    }

    /**
     * Sanitize input data
     */
    public function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Clean input for database
     */
    public function cleanForDatabase($data) {
        if (is_array($data)) {
            return array_map([$this, 'cleanForDatabase'], $data);
        }
        return trim($data);
    }

    /**
     * Generate random string
     */
    public function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Check if request is AJAX
     */
    public function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get client IP address
     */
    public function getClientIP() {
        $ipHeaders = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipHeaders as $header) {
            if (isset($_SERVER[$header])) {
                $ip = trim($_SERVER[$header]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Rate limiting check
     */
    public function checkRateLimit($key, $maxRequests = 60, $timeWindow = 60) {
        $cacheKey = 'rate_limit_' . md5($key);
        $currentTime = time();

        // Simple file-based rate limiting (in production, use Redis or similar)
        $cacheFile = sys_get_temp_dir() . '/' . $cacheKey . '.cache';

        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && $currentTime - $data['start_time'] < $timeWindow) {
                if ($data['requests'] >= $maxRequests) {
                    return false;
                }
                $data['requests']++;
            } else {
                $data = [
                    'start_time' => $currentTime,
                    'requests' => 1
                ];
            }
        } else {
            $data = [
                'start_time' => $currentTime,
                'requests' => 1
            ];
        }

        file_put_contents($cacheFile, json_encode($data));
        return true;
    }

    /**
     * Log security event
     */
    public function logSecurityEvent($event, $details = []) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'details' => $details
        ];

        $logFile = __DIR__ . '/../logs/security.log';
        $logMessage = json_encode($logData) . PHP_EOL;

        error_log($logMessage, 3, $logFile);
    }

    /**
     * Validate email format
     */
    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate URL
     */
    public function isValidUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Escape for JavaScript
     */
    public function escapeJs($string) {
        return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    private function __wakeup() {}
}
?>