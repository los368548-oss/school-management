<?php
/**
 * Session Class
 *
 * Handles session management and flash messages
 */

class Session {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        self::start();
        session_destroy();
    }

    public static function regenerate() {
        self::start();
        session_regenerate_id(true);
    }

    // Flash messages
    public static function setFlash($type, $message) {
        self::start();
        $_SESSION['flash_messages'][$type][] = $message;
    }

    public static function getFlash($type = null) {
        self::start();
        if ($type === null) {
            $messages = $_SESSION['flash_messages'] ?? [];
            unset($_SESSION['flash_messages']);
            return $messages;
        }

        if (isset($_SESSION['flash_messages'][$type])) {
            $messages = $_SESSION['flash_messages'][$type];
            unset($_SESSION['flash_messages'][$type]);
            return $messages;
        }

        return [];
    }

    public static function hasFlash($type = null) {
        self::start();
        if ($type === null) {
            return !empty($_SESSION['flash_messages']);
        }
        return !empty($_SESSION['flash_messages'][$type]);
    }

    // User session management
    public static function setUser($user) {
        self::start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['academic_year_id'] = $user['academic_year_id'] ?? null;
    }

    public static function getUser() {
        self::start();
        if (self::has('user_id')) {
            return [
                'id' => self::get('user_id'),
                'role' => self::get('user_role'),
                'name' => self::get('user_name'),
                'email' => self::get('user_email'),
                'academic_year_id' => self::get('academic_year_id')
            ];
        }
        return null;
    }

    public static function isLoggedIn() {
        return self::has('user_id') && self::has('user_role');
    }

    public static function logout() {
        self::start();
        session_unset();
        session_destroy();
        session_start();
    }

    // Academic year management
    public static function setAcademicYear($yearId) {
        self::start();
        $_SESSION['academic_year_id'] = $yearId;
    }

    public static function getAcademicYear() {
        self::start();
        return $_SESSION['academic_year_id'] ?? null;
    }

    public static function clearAcademicYear() {
        self::start();
        unset($_SESSION['academic_year_id']);
    }
}
?>