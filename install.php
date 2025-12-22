<?php
/**
 * Installation Wizard
 *
 * Web-based installation script for the School Management System
 */

session_start();

// Define constants
define('BASE_PATH', __DIR__ . '/');
define('REQUIRED_PHP_VERSION', '8.1.0');

// Include required files
require_once BASE_PATH . 'core/Database.php';
require_once BASE_PATH . 'core/Security.php';
require_once BASE_PATH . 'models/User.php';
require_once BASE_PATH . 'models/AcademicYear.php';

class Installer {
    private $db;
    private $errors = [];
    private $success = [];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function run() {
        $step = $_GET['step'] ?? 1;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processStep($step);
        }

        $this->showHeader();
        $this->showStep($step);
        $this->showFooter();
    }

    private function processStep($step) {
        switch ($step) {
            case 1:
                $this->processRequirements();
                break;
            case 2:
                $this->processDatabase();
                break;
            case 3:
                $this->processAdminAccount();
                break;
            case 4:
                $this->processAcademicYear();
                break;
            case 5:
                $this->finalizeInstallation();
                break;
        }
    }

    private function processRequirements() {
        // Check PHP version
        if (version_compare(PHP_VERSION, REQUIRED_PHP_VERSION, '<')) {
            $this->errors[] = "PHP version " . REQUIRED_PHP_VERSION . " or higher is required. Current version: " . PHP_VERSION;
        }

        // Check required extensions
        $requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session'];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $this->errors[] = "PHP extension '{$ext}' is required but not loaded.";
            }
        }

        // Check file permissions
        $writablePaths = ['uploads', 'logs'];
        foreach ($writablePaths as $path) {
            $fullPath = BASE_PATH . $path;
            if (!is_writable($fullPath)) {
                $this->errors[] = "Directory '{$path}' is not writable.";
            }
        }

        if (empty($this->errors)) {
            $_SESSION['install_step'] = 2;
            header('Location: install.php?step=2');
            exit;
        }
    }

    private function processDatabase() {
        $host = $_POST['db_host'] ?? '';
        $name = $_POST['db_name'] ?? '';
        $user = $_POST['db_user'] ?? '';
        $pass = $_POST['db_pass'] ?? '';

        if (empty($name) || empty($user)) {
            $this->errors[] = "Database name and username are required.";
            return;
        }

        try {
            // Test connection
            $dsn = "mysql:host={$host};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass);

            // Create database if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Save config
            $this->saveDatabaseConfig($host, $name, $user, $pass);

            // Import schema
            $this->importSchema($host, $name, $user, $pass);

            $_SESSION['install_step'] = 3;
            header('Location: install.php?step=3');
            exit;

        } catch (PDOException $e) {
            $this->errors[] = "Database connection failed: " . $e->getMessage();
        }
    }

    private function processAdminAccount() {
        $username = $_POST['admin_username'] ?? '';
        $email = $_POST['admin_email'] ?? '';
        $password = $_POST['admin_password'] ?? '';
        $confirmPassword = $_POST['admin_confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            $this->errors[] = "All fields are required.";
            return;
        }

        if ($password !== $confirmPassword) {
            $this->errors[] = "Passwords do not match.";
            return;
        }

        if (strlen($password) < 8) {
            $this->errors[] = "Password must be at least 8 characters long.";
            return;
        }

        try {
            $userModel = new User();
            $userModel->createWithProfile([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => 'admin',
                'status' => 'active'
            ], [
                'first_name' => 'Admin',
                'last_name' => 'User'
            ]);

            $_SESSION['install_step'] = 4;
            header('Location: install.php?step=4');
            exit;

        } catch (Exception $e) {
            $this->errors[] = "Failed to create admin account: " . $e->getMessage();
        }
    }

    private function processAcademicYear() {
        $yearName = $_POST['year_name'] ?? '';
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';

        if (empty($yearName) || empty($startDate) || empty($endDate)) {
            $this->errors[] = "All fields are required.";
            return;
        }

        try {
            $yearModel = new AcademicYear();
            $yearModel->createYear([
                'year_name' => $yearName,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => 1
            ]);

            $_SESSION['install_step'] = 5;
            header('Location: install.php?step=5');
            exit;

        } catch (Exception $e) {
            $this->errors[] = "Failed to create academic year: " . $e->getMessage();
        }
    }

    private function finalizeInstallation() {
        // Create .env file or update config
        $this->createEnvFile();

        // Mark installation as complete
        file_put_contents(BASE_PATH . 'installed.lock', date('Y-m-d H:i:s'));

        $_SESSION['install_complete'] = true;
        header('Location: install.php?step=6');
        exit;
    }

    private function saveDatabaseConfig($host, $name, $user, $pass) {
        $config = "<?php\n";
        $config .= "return [\n";
        $config .= "    'host' => '{$host}',\n";
        $config .= "    'database' => '{$name}',\n";
        $config .= "    'username' => '{$user}',\n";
        $config .= "    'password' => '{$pass}',\n";
        $config .= "    'charset' => 'utf8mb4',\n";
        $config .= "    'collation' => 'utf8mb4_unicode_ci',\n";
        $config .= "    'options' => [\n";
        $config .= "        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n";
        $config .= "        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n";
        $config .= "        PDO::ATTR_EMULATE_PREPARES => false,\n";
        $config .= "    ],\n";
        $config .= "];\n";

        file_put_contents(BASE_PATH . 'config/database.php', $config);
    }

    private function importSchema($host, $name, $user, $pass) {
        $schemaFile = BASE_PATH . 'database/schema.sql';
        if (!file_exists($schemaFile)) {
            throw new Exception("Schema file not found");
        }

        $sql = file_get_contents($schemaFile);

        $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->exec($sql);
    }

    private function createEnvFile() {
        $env = "APP_ENV=production\n";
        $env .= "APP_DEBUG=false\n";
        $env .= "APP_URL=http://localhost\n";
        $env .= "DB_HOST=localhost\n";
        $env .= "DB_NAME=school_management\n";
        $env .= "DB_USER=root\n";
        $env .= "DB_PASS=\n";

        file_put_contents(BASE_PATH . '.env', $env);
    }

    private function showHeader() {
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>School Management System - Installation</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body class="bg-light">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card shadow">
                            <div class="card-header bg-primary text-white text-center">
                                <h3 class="mb-0">School Management System Installation</h3>
                            </div>
                            <div class="card-body">';
    }

    private function showStep($step) {
        $this->showProgress($step);

        if (!empty($this->errors)) {
            echo '<div class="alert alert-danger"><ul class="mb-0">';
            foreach ($this->errors as $error) {
                echo "<li>{$error}</li>";
            }
            echo '</ul></div>';
        }

        switch ($step) {
            case 1:
                $this->showRequirementsStep();
                break;
            case 2:
                $this->showDatabaseStep();
                break;
            case 3:
                $this->showAdminStep();
                break;
            case 4:
                $this->showAcademicYearStep();
                break;
            case 5:
                $this->showFinalizeStep();
                break;
            case 6:
                $this->showCompleteStep();
                break;
        }
    }

    private function showProgress($currentStep) {
        $steps = [
            1 => 'Requirements Check',
            2 => 'Database Setup',
            3 => 'Admin Account',
            4 => 'Academic Year',
            5 => 'Finalize',
            6 => 'Complete'
        ];

        echo '<div class="mb-4">
            <div class="progress" style="height: 30px;">
                <div class="progress-bar bg-success" role="progressbar"
                     style="width: ' . (($currentStep - 1) / 5 * 100) . '%"
                     aria-valuenow="' . ($currentStep - 1) . '" aria-valuemin="0" aria-valuemax="5">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">';

        foreach ($steps as $num => $name) {
            $active = $num == $currentStep ? 'text-primary fw-bold' : 'text-muted';
            echo "<small class='{$active}'>{$num}. {$name}</small>";
        }

        echo '</div></div>';
    }

    private function showRequirementsStep() {
        echo '<h4>System Requirements Check</h4>
        <p class="text-muted">Checking if your system meets the minimum requirements...</p>
        <form method="POST">
            <button type="submit" class="btn btn-primary">Check Requirements</button>
        </form>';
    }

    private function showDatabaseStep() {
        echo '<h4>Database Configuration</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Database Host</label>
                <input type="text" class="form-control" name="db_host" value="localhost" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Database Name</label>
                <input type="text" class="form-control" name="db_name" value="school_management" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Database Username</label>
                <input type="text" class="form-control" name="db_user" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Database Password</label>
                <input type="password" class="form-control" name="db_pass">
            </div>
            <button type="submit" class="btn btn-primary">Setup Database</button>
        </form>';
    }

    private function showAdminStep() {
        echo '<h4>Create Administrator Account</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="admin_username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="admin_email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="admin_password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="admin_confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Admin Account</button>
        </form>';
    }

    private function showAcademicYearStep() {
        echo '<h4>Set Up Academic Year</h4>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Academic Year Name</label>
                <input type="text" class="form-control" name="year_name" placeholder="e.g., 2024-2025" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" required>
            </div>
            <div class="mb-3">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Academic Year</button>
        </form>';
    }

    private function showFinalizeStep() {
        echo '<h4>Finalize Installation</h4>
        <p>Click the button below to complete the installation.</p>
        <form method="POST">
            <button type="submit" class="btn btn-success">Complete Installation</button>
        </form>';
    }

    private function showCompleteStep() {
        echo '<div class="text-center">
            <h4 class="text-success">Installation Complete!</h4>
            <p>Your School Management System has been successfully installed.</p>
            <div class="alert alert-info">
                <strong>Default Login Credentials:</strong><br>
                Username: admin<br>
                Password: admin123
            </div>
            <a href="/" class="btn btn-primary">Go to Login Page</a>
        </div>';
    }

    private function showFooter() {
        echo '            </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>';
    }
}

// Check if already installed
if (file_exists(BASE_PATH . 'installed.lock')) {
    header('Location: /');
    exit;
}

$installer = new Installer();
$installer->run();
?>