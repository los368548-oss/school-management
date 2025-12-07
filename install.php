<?php
/**
 * School Management System Installation Wizard
 */

session_start();

// Prevent access if already installed
if (file_exists(__DIR__ . '/config/installed.lock')) {
    die('System is already installed. Remove config/installed.lock to reinstall.');
}

// Installation steps
$step = $_GET['step'] ?? 1;
$errors = [];
$success = false;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleInstallationStep($step);
}

function handleInstallationStep($step) {
    global $errors, $success;

    switch ($step) {
        case 1:
            // Requirements check
            $requirements = checkRequirements();
            if ($requirements['all_passed']) {
                header('Location: install.php?step=2');
                exit;
            }
            break;

        case 2:
            // Database configuration
            $dbConfig = [
                'host' => $_POST['db_host'] ?? '',
                'database' => $_POST['db_name'] ?? '',
                'username' => $_POST['db_user'] ?? '',
                'password' => $_POST['db_pass'] ?? '',
                'charset' => 'utf8mb4'
            ];

            // Test database connection
            try {
                $pdo = new PDO(
                    "mysql:host={$dbConfig['host']};charset={$dbConfig['charset']}",
                    $dbConfig['database'],
                    $dbConfig['username'],
                    $dbConfig['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );

                // Create database if it doesn't exist
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                // Save database config
                saveDatabaseConfig($dbConfig);

                // Import schema
                importDatabaseSchema($dbConfig);

                header('Location: install.php?step=3');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Database connection failed: ' . $e->getMessage();
            }
            break;

        case 3:
            // Administrator account setup
            $adminData = [
                'username' => $_POST['admin_username'] ?? '',
                'email' => $_POST['admin_email'] ?? '',
                'password' => $_POST['admin_password'] ?? '',
                'confirm_password' => $_POST['admin_confirm_password'] ?? ''
            ];

            // Validate admin data
            if (empty($adminData['username']) || empty($adminData['email']) || empty($adminData['password'])) {
                $errors[] = 'All fields are required.';
            } elseif (!filter_var($adminData['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email address.';
            } elseif (strlen($adminData['password']) < 8) {
                $errors[] = 'Password must be at least 8 characters long.';
            } elseif ($adminData['password'] !== $adminData['confirm_password']) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                // Create admin user
                createAdminUser($adminData);

                // Mark as installed
                file_put_contents(__DIR__ . '/config/installed.lock', 'installed');

                $success = true;
            }
            break;
    }
}

function checkRequirements() {
    $requirements = [
        'php_version' => [
            'name' => 'PHP Version',
            'required' => '8.1.0',
            'current' => PHP_VERSION,
            'status' => version_compare(PHP_VERSION, '8.1.0', '>=')
        ],
        'pdo_mysql' => [
            'name' => 'PDO MySQL Extension',
            'required' => 'Enabled',
            'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled',
            'status' => extension_loaded('pdo_mysql')
        ],
        'mbstring' => [
            'name' => 'Multibyte String Extension',
            'required' => 'Enabled',
            'current' => extension_loaded('mbstring') ? 'Enabled' : 'Disabled',
            'status' => extension_loaded('mbstring')
        ],
        'config_writable' => [
            'name' => 'Config Directory Writable',
            'required' => 'Writable',
            'current' => is_writable(__DIR__ . '/config') ? 'Writable' : 'Not Writable',
            'status' => is_writable(__DIR__ . '/config')
        ],
        'uploads_writable' => [
            'name' => 'Uploads Directory Writable',
            'required' => 'Writable',
            'current' => is_writable(__DIR__ . '/uploads') ? 'Writable' : 'Not Writable',
            'status' => is_writable(__DIR__ . '/uploads')
        ]
    ];

    $all_passed = true;
    foreach ($requirements as $req) {
        if (!$req['status']) {
            $all_passed = false;
            break;
        }
    }

    $requirements['all_passed'] = $all_passed;
    return $requirements;
}

function saveDatabaseConfig($config) {
    $configFile = __DIR__ . '/config/database.php';
    $content = "<?php\nreturn " . var_export($config, true) . ";\n";
    file_put_contents($configFile, $content);
}

function importDatabaseSchema($dbConfig) {
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}",
        $dbConfig['username'],
        $dbConfig['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    $pdo->exec($schema);
}

function createAdminUser($data) {
    require_once __DIR__ . '/core/Database.php';
    require_once __DIR__ . '/core/Security.php';

    $db = Database::getInstance();
    $security = Security::getInstance();

    $hashedPassword = $security->hashPassword($data['password']);

    $db->query("
        INSERT INTO users (username, email, password, role_id, is_active, created_at, updated_at)
        VALUES (?, ?, ?, 1, 1, NOW(), NOW())
    ")->bind(1, $data['username'])->bind(2, $data['email'])->bind(3, $hashedPassword)->execute();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System - Installation</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .install-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            font-weight: bold;
        }
        .step.active {
            background-color: #007bff;
            color: white;
        }
        .step.completed {
            background-color: #28a745;
            color: white;
        }
        .step.pending {
            background-color: #e9ecef;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="install-header">
        <div class="container text-center">
            <h1><i class="fas fa-school me-2"></i>A.s.higher secondary school</h1>
            <h2>Management System Installation</h2>
            <p class="lead">Let's get your school management system up and running!</p>
        </div>
    </div>

    <div class="container my-5">
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? 'active' : 'pending'; ?>">
                <?php echo $step > 1 ? '<i class="fas fa-check"></i>' : '1'; ?>
            </div>
            <div class="step <?php echo $step >= 2 ? 'active' : 'pending'; ?>">
                <?php echo $step > 2 ? '<i class="fas fa-check"></i>' : '2'; ?>
            </div>
            <div class="step <?php echo $step >= 3 ? 'active' : 'pending'; ?>">
                <?php echo $step > 3 ? '<i class="fas fa-check"></i>' : '3'; ?>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h6>Please fix the following errors:</h6>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <h4><i class="fas fa-check-circle me-2"></i>Installation Complete!</h4>
                <p>Your School Management System has been successfully installed.</p>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Admin Login</h5>
                                <p><strong>Username:</strong> <?php echo htmlspecialchars($_POST['admin_username']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($_POST['admin_email']); ?></p>
                                <a href="/" class="btn btn-primary">Go to Login</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Next Steps</h5>
                                <ul class="mb-0">
                                    <li>Upload school logo to <code>assets/logos/schoollogs/</code></li>
                                    <li>Configure email settings</li>
                                    <li>Add classes and subjects</li>
                                    <li>Import student data</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php if ($step === 1): ?>
                <!-- Step 1: Requirements Check -->
                <div class="card">
                    <div class="card-header">
                        <h4>Step 1: System Requirements Check</h4>
                    </div>
                    <div class="card-body">
                        <p>Checking if your server meets the minimum requirements for the School Management System.</p>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Requirement</th>
                                    <th>Required</th>
                                    <th>Current</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $requirements = checkRequirements(); ?>
                                <?php foreach ($requirements as $key => $req): ?>
                                    <?php if ($key === 'all_passed') continue; ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($req['name']); ?></td>
                                        <td><?php echo htmlspecialchars($req['required']); ?></td>
                                        <td><?php echo htmlspecialchars($req['current']); ?></td>
                                        <td>
                                            <?php if ($req['status']): ?>
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Pass</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Fail</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if ($requirements['all_passed']): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>All requirements are met! Click Continue to proceed.
                            </div>
                            <a href="install.php?step=2" class="btn btn-primary">Continue to Database Setup</a>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>Some requirements are not met. Please fix them before continuing.
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="location.reload()">Check Again</button>
                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif ($step === 2): ?>
                <!-- Step 2: Database Configuration -->
                <div class="card">
                    <div class="card-header">
                        <h4>Step 2: Database Configuration</h4>
                    </div>
                    <div class="card-body">
                        <p>Configure your MySQL database connection details.</p>

                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="db_host" class="form-label">Database Host</label>
                                        <input type="text" class="form-control" id="db_host" name="db_host"
                                               value="<?php echo htmlspecialchars($_POST['db_host'] ?? 'localhost'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="db_name" class="form-label">Database Name</label>
                                        <input type="text" class="form-control" id="db_name" name="db_name"
                                               value="<?php echo htmlspecialchars($_POST['db_name'] ?? 'school_management'); ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="db_user" class="form-label">Database Username</label>
                                        <input type="text" class="form-control" id="db_user" name="db_user"
                                               value="<?php echo htmlspecialchars($_POST['db_user'] ?? 'root'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="db_pass" class="form-label">Database Password</label>
                                        <input type="password" class="form-control" id="db_pass" name="db_pass"
                                               value="<?php echo htmlspecialchars($_POST['db_pass'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="install.php?step=1" class="btn btn-secondary">Back</a>
                                <button type="submit" class="btn btn-primary">Test Connection & Continue</button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php elseif ($step === 3): ?>
                <!-- Step 3: Administrator Account -->
                <div class="card">
                    <div class="card-header">
                        <h4>Step 3: Administrator Account Setup</h4>
                    </div>
                    <div class="card-body">
                        <p>Create the main administrator account for your school management system.</p>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="admin_username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="admin_username" name="admin_username"
                                       value="<?php echo htmlspecialchars($_POST['admin_username'] ?? 'admin'); ?>" required>
                                <div class="form-text">Choose a unique username for the administrator.</div>
                            </div>
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email"
                                       value="<?php echo htmlspecialchars($_POST['admin_email'] ?? 'admin@school.com'); ?>" required>
                                <div class="form-text">Administrator's email address.</div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="admin_password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                        <div class="form-text">Minimum 8 characters.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="admin_confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="admin_confirm_password" name="admin_confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="install.php?step=2" class="btn btn-secondary">Back</a>
                                <button type="submit" class="btn btn-success">Complete Installation</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>