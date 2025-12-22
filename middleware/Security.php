<?php
/**
 * Security Middleware
 *
 * Provides additional security checks for requests
 */

class SecurityMiddleware {
    public function handle() {
        // Rate limiting
        $this->checkRateLimit();

        // CSRF protection for POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRFToken();
        }

        // Input sanitization
        $this->sanitizeInput();

        // Security headers
        $this->setSecurityHeaders();

        // Log suspicious activities
        $this->logSuspiciousActivity();
    }

    private function checkRateLimit() {
        $config = require BASE_PATH . 'config/security.php';
        $maxRequests = $config['rate_limiting']['max_requests'];
        $windowMinutes = $config['rate_limiting']['window_minutes'];

        if (!$config['rate_limiting']['enabled']) {
            return;
        }

        $clientIP = $this->getClientIP();
        $currentTime = time();
        $windowStart = $currentTime - ($windowMinutes * 60);

        // Simple rate limiting using session (in production, use Redis or database)
        $rateLimitKey = 'rate_limit_' . $clientIP;

        if (!isset($_SESSION[$rateLimitKey])) {
            $_SESSION[$rateLimitKey] = [];
        }

        // Remove old requests outside the window
        $_SESSION[$rateLimitKey] = array_filter($_SESSION[$rateLimitKey], function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });

        // Check if rate limit exceeded
        if (count($_SESSION[$rateLimitKey]) >= $maxRequests) {
            $this->logSecurityEvent('rate_limit_exceeded', "IP: {$clientIP}");
            http_response_code(429);
            die('Too many requests. Please try again later.');
        }

        // Add current request
        $_SESSION[$rateLimitKey][] = $currentTime;
    }

    private function validateCSRFToken() {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (empty($token) || !Security::validateCSRFToken($token)) {
            $this->logSecurityEvent('csrf_token_invalid', 'Invalid CSRF token');
            http_response_code(403);
            die('Invalid security token. Please refresh the page and try again.');
        }
    }

    private function sanitizeInput() {
        // Sanitize GET parameters
        $_GET = Validator::sanitize($_GET);

        // Sanitize POST parameters
        $_POST = Validator::sanitize($_POST);

        // Sanitize COOKIE parameters
        $_COOKIE = Validator::sanitize($_COOKIE);
    }

    private function setSecurityHeaders() {
        if (!headers_sent()) {
            // Prevent clickjacking
            header('X-Frame-Options: DENY');

            // Prevent MIME type sniffing
            header('X-Content-Type-Options: nosniff');

            // Enable XSS protection
            header('X-XSS-Protection: 1; mode=block');

            // Referrer policy
            header('Referrer-Policy: strict-origin-when-cross-origin');

            // Content Security Policy
            header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self';");

            // HSTS (HTTP Strict Transport Security) - only if HTTPS
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
            }
        }
    }

    private function logSuspiciousActivity() {
        $clientIP = $this->getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $requestURI = $_SERVER['REQUEST_URI'] ?? '';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';

        // Check for suspicious patterns
        $suspiciousPatterns = [
            '/\.\./',  // Directory traversal
            '/<script/i',  // XSS attempts
            '/union.*select/i',  // SQL injection attempts
            '/eval\(/i',  // Code injection
            '/base64_decode/i',  // Encoded attacks
        ];

        $suspiciousData = $_GET + $_POST;
        $suspiciousFound = false;

        foreach ($suspiciousData as $key => $value) {
            if (is_string($value)) {
                foreach ($suspiciousPatterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        $suspiciousFound = true;
                        break 2;
                    }
                }
            }
        }

        if ($suspiciousFound) {
            $this->logSecurityEvent('suspicious_activity_detected',
                "Suspicious activity from IP: {$clientIP}, URI: {$requestURI}, Method: {$requestMethod}");
        }

        // Log all POST requests to sensitive areas
        $sensitivePaths = ['/admin', '/api', '/install'];
        $isSensitive = false;

        foreach ($sensitivePaths as $path) {
            if (strpos($requestURI, $path) === 0) {
                $isSensitive = true;
                break;
            }
        }

        if ($isSensitive && $requestMethod === 'POST') {
            $this->logSecurityEvent('sensitive_post_request',
                "POST to sensitive path: {$requestURI} from IP: {$clientIP}");
        }
    }

    private function getClientIP() {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Handle comma-separated IPs (like X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    private function logSecurityEvent($event, $details) {
        $logEntry = sprintf(
            "[%s] SECURITY: %s - %s - IP: %s - User-Agent: %s\n",
            date('Y-m-d H:i:s'),
            $event,
            $details,
            $this->getClientIP(),
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        );

        $logFile = BASE_PATH . 'logs/security.log';
        $this->ensureLogDirectory();

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    private function ensureLogDirectory() {
        $logDir = BASE_PATH . 'logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
}
?>