<?php
/**
 * PHPUnit Bootstrap File
 *
 * Sets up the testing environment
 */

// Define base path
define('BASE_PATH', dirname(__DIR__) . '/');
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

// Include models
require_once BASE_PATH . 'models/BaseModel.php';
require_once BASE_PATH . 'models/User.php';
require_once BASE_PATH . 'models/Student.php';
require_once BASE_PATH . 'models/AcademicYear.php';

// Include controllers
require_once BASE_PATH . 'controllers/BaseController.php';
require_once BASE_PATH . 'controllers/AuthController.php';

// Include helpers
require_once BASE_PATH . 'helpers/functions.php';

// Set up test database connection
// Note: Create a separate test database for testing
putenv('APP_ENV=testing');
putenv('DB_HOST=localhost');
putenv('DB_NAME=school_management_test');
putenv('DB_USER=root');
putenv('DB_PASS=');

// Initialize session for testing
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Create test database tables if they don't exist
function setupTestDatabase() {
    try {
        $db = Database::getInstance();

        // Create test tables (simplified schema for testing)
        $sql = "
        CREATE TABLE IF NOT EXISTS test_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin','student') DEFAULT 'student',
            status ENUM('active','inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS test_students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            scholar_number VARCHAR(20) NOT NULL UNIQUE,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            academic_year_id INT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS test_academic_years (
            id INT AUTO_INCREMENT PRIMARY KEY,
            year_name VARCHAR(20) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            is_active BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";

        $db->query($sql);

        // Insert test data
        $db->query("INSERT IGNORE INTO test_academic_years (year_name, start_date, end_date, is_active) VALUES ('2024-2025', '2024-04-01', '2025-03-31', 1)");

    } catch (Exception $e) {
        echo "Test database setup failed: " . $e->getMessage() . "\n";
    }
}

// Clean up test data
function cleanupTestDatabase() {
    try {
        $db = Database::getInstance();

        // Clear test tables
        $db->query("DELETE FROM test_students");
        $db->query("DELETE FROM test_users");
        $db->query("DELETE FROM test_academic_years");

    } catch (Exception $e) {
        echo "Test database cleanup failed: " . $e->getMessage() . "\n";
    }
}

// Set up test environment
setupTestDatabase();

// Register cleanup function to run after tests
register_shutdown_function('cleanupTestDatabase');
?>