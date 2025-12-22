<?php
/**
 * Helper Functions
 *
 * Common utility functions used throughout the application
 */

/**
 * Get base URL
 */
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $basePath = dirname($_SERVER['SCRIPT_NAME']);

    // Remove trailing slash from base path
    $basePath = rtrim($basePath, '/');

    return $protocol . '://' . $host . $basePath . '/' . ltrim($path, '/');
}

/**
 * Get current URL
 */
function current_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Redirect to URL
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Check if request is AJAX
 */
function is_ajax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Check if request is POST
 */
function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 */
function is_get() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Get POST data
 */
function post($key = null, $default = null) {
    if ($key === null) {
        return $_POST;
    }
    return $_POST[$key] ?? $default;
}

/**
 * Get GET data
 */
function get($key = null, $default = null) {
    if ($key === null) {
        return $_GET;
    }
    return $_GET[$key] ?? $default;
}

/**
 * Get request data (POST or GET)
 */
function request($key = null, $default = null) {
    if (is_post()) {
        return post($key, $default);
    }
    return get($key, $default);
}

/**
 * Set session data
 */
function session($key, $value = null) {
    if ($value === null) {
        return $_SESSION[$key] ?? null;
    }
    $_SESSION[$key] = $value;
}

/**
 * Get old input value
 */
function old($key, $default = '') {
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Set old input
 */
function set_old_input($data) {
    $_SESSION['old_input'] = $data;
}

/**
 * Clear old input
 */
function clear_old_input() {
    unset($_SESSION['old_input']);
}

/**
 * Format currency
 */
function format_currency($amount, $symbol = '₹') {
    return $symbol . number_format($amount, 2);
}

/**
 * Format date
 */
function format_date($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function format_datetime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }
    return date($format, strtotime($datetime));
}

/**
 * Calculate age from date of birth
 */
function calculate_age($dob) {
    if (empty($dob) || $dob === '0000-00-00') {
        return '-';
    }

    $birthDate = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birthDate);

    return $age->y;
}

/**
 * Generate random string
 */
function random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $string;
}

/**
 * Slugify string
 */
function slugify($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

/**
 * Truncate string
 */
function truncate($string, $length = 100, $suffix = '...') {
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length - strlen($suffix)) . $suffix;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

/**
 * Get current user
 */
function current_user() {
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'role' => $_SESSION['user_role'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? ''
    ];
}

/**
 * Check if user has role
 */
function has_role($role) {
    $user = current_user();
    return $user && $user['role'] === $role;
}

/**
 * Get current academic year
 */
function current_academic_year() {
    return $_SESSION['academic_year_id'] ?? null;
}

/**
 * Get academic year name
 */
function academic_year_name($yearId = null) {
    if (!$yearId) {
        $yearId = current_academic_year();
    }

    if (!$yearId) {
        return 'Not Set';
    }

    $db = Database::getInstance();
    $year = $db->selectOne("SELECT year_name FROM academic_years WHERE id = ?", [$yearId]);

    return $year ? $year['year_name'] : 'Not Set';
}

/**
 * Generate receipt number
 */
function generate_receipt_number() {
    $date = date('Ymd');
    $db = Database::getInstance();

    $lastReceipt = $db->selectOne(
        "SELECT receipt_number FROM fee_payments
         WHERE receipt_number LIKE ?
         ORDER BY id DESC LIMIT 1",
        [$date . '%']
    );

    if ($lastReceipt) {
        $lastNumber = (int) substr($lastReceipt['receipt_number'], -4);
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    return $date . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}

/**
 * Generate scholar number
 */
function generate_scholar_number() {
    $academicYearId = current_academic_year();
    if (!$academicYearId) {
        return null;
    }

    $db = Database::getInstance();
    $year = $db->selectOne("SELECT year_name FROM academic_years WHERE id = ?", [$academicYearId]);

    if (!$year) {
        return null;
    }

    // Get the last scholar number for this year
    $lastStudent = $db->selectOne(
        "SELECT scholar_number FROM students
         WHERE academic_year_id = ? AND scholar_number LIKE ?
         ORDER BY CAST(SUBSTRING(scholar_number, -4) AS UNSIGNED) DESC LIMIT 1",
        [$academicYearId, substr($year['year_name'], 0, 4) . '%']
    );

    if ($lastStudent) {
        $lastNumber = (int) substr($lastStudent['scholar_number'], -4);
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    return substr($year['year_name'], 0, 4) . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}

/**
 * Get file extension
 */
function get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file is image
 */
function is_image($filename) {
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    return in_array(get_file_extension($filename), $imageExtensions);
}

/**
 * Format file size
 */
function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Get grade from percentage
 */
function get_grade($percentage) {
    if ($percentage >= 91) return 'A+';
    if ($percentage >= 81) return 'A';
    if ($percentage >= 71) return 'B+';
    if ($percentage >= 61) return 'B';
    if ($percentage >= 51) return 'C+';
    if ($percentage >= 41) return 'C';
    if ($percentage >= 33) return 'D';
    return 'F';
}

/**
 * Calculate percentage
 */
function calculate_percentage($obtained, $total) {
    if ($total == 0) return 0;
    return round(($obtained / $total) * 100, 2);
}

/**
 * Get status badge class
 */
function get_status_badge_class($status) {
    $classes = [
        'active' => 'bg-success',
        'inactive' => 'bg-secondary',
        'pending' => 'bg-warning',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger',
        'upcoming' => 'bg-info',
        'ongoing' => 'bg-primary',
        'suspended' => 'bg-danger',
        'transferred' => 'bg-info',
        'passed_out' => 'bg-success'
    ];

    return $classes[$status] ?? 'bg-secondary';
}

/**
 * Log activity
 */
function log_activity($action, $details = '') {
    if (class_exists('Security')) {
        Security::logActivity($action, $details);
    }
}

/**
 * Send email (placeholder)
 */
function send_email($to, $subject, $message, $headers = []) {
    // In production, use PHPMailer or similar
    $headersStr = '';
    foreach ($headers as $key => $value) {
        $headersStr .= $key . ': ' . $value . "\r\n";
    }

    // Log email for debugging
    $logData = [
        'to' => $to,
        'subject' => $subject,
        'message' => substr($message, 0, 200) . '...',
        'headers' => $headers,
        'sent_at' => date('Y-m-d H:i:s')
    ];

    $logFile = BASE_PATH . 'logs/email.log';
    $logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($logData) . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);

    // In development, just return true
    return true;
}

/**
 * Debug function
 */
function debug($data, $die = false) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';

    if ($die) {
        die();
    }
}

/**
 * Check if development environment
 */
function is_development() {
    return getenv('APP_ENV') === 'development' || !file_exists(BASE_PATH . 'installed.lock');
}

/**
 * Get application config
 */
function config($key = null, $default = null) {
    static $config = null;

    if ($config === null) {
        $config = require BASE_PATH . 'config/app.php';
    }

    if ($key === null) {
        return $config;
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default;
        }
    }

    return $value;
}
?>