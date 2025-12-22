<?php
/**
 * School Management System - Main Entry Point
 *
 * This is the main entry point for the School Management System.
 * It handles routing, session management, and initializes the application.
 */

// Start session
session_start();

// Define application constants
define('BASE_PATH', __DIR__ . '/');
define('APP_PATH', BASE_PATH . 'app/');
define('PUBLIC_PATH', BASE_PATH . 'public/');
define('ASSETS_PATH', BASE_PATH . 'assets/');

// Include autoloader (if using Composer)
if (file_exists(BASE_PATH . 'vendor/autoload.php')) {
    require_once BASE_PATH . 'vendor/autoload.php';
}

// Include core files
require_once BASE_PATH . 'core/Database.php';
require_once BASE_PATH . 'core/Router.php';
require_once BASE_PATH . 'core/Security.php';
require_once BASE_PATH . 'core/Session.php';
require_once BASE_PATH . 'core/Validator.php';

// Initialize security
Security::init();

// Get the requested URL
$url = isset($_GET['url']) ? $_GET['url'] : '';
$url = rtrim($url, '/');

// Route the request
$router = new Router();
$router->route($url);
?>