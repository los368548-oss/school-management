<?php
/**
 * Authentication Controller
 *
 * Handles user login, logout, and authentication-related operations
 */

class AuthController extends BaseController {

    public function login() {
        // If already logged in, redirect to dashboard
        if (Security::isLoggedIn()) {
            $user = Security::getCurrentUser();
            $this->redirect('/' . $user['role'] . '/dashboard');
        }

        $errors = [];
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validate input
            $validationRules = [
                'username' => 'required',
                'password' => 'required'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                $username = $postData['username'];
                $password = $postData['password'];
                $remember = isset($postData['remember']) ? true : false;

                // Attempt login
                if ($this->authenticateUser($username, $password)) {
                    $user = Security::getCurrentUser();

                    // Set session
                    Session::setUser($user);

                    // Log activity
                    $this->logActivity('login', "User {$user['name']} logged in");

                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        // For admin, redirect to academic year selection first
                        $this->redirect('/admin/select-academic-year');
                    } else {
                        $this->redirect('/student/dashboard');
                    }
                } else {
                    $errors['auth'] = 'Invalid username or password';
                }
            }
        }

        // Show login form
        $data['errors'] = $errors;
        $data['csrf_token'] = Security::generateCSRFToken();

        $this->view('auth/login', $data);
    }

    public function logout() {
        $user = Security::getCurrentUser();
        if ($user) {
            $this->logActivity('logout', "User {$user['name']} logged out");
        }

        Security::logout();
        $this->redirect('/login');
    }

    public function forgotPassword() {
        // Basic forgot password implementation
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            $validationRules = [
                'email' => 'required|email'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                $email = $postData['email'];

                // Check if email exists
                $user = $this->db->selectOne("SELECT id, username FROM users WHERE email = ?", [$email]);

                if ($user) {
                    // Generate reset token (simplified)
                    $resetToken = Security::generateRandomString(32);
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    // Store reset token (you would typically create a password_resets table)
                    // For now, just show success message
                    $success = true;

                    // In a real implementation, send email with reset link
                    $this->logActivity('password_reset_request', "Password reset requested for email: {$email}");
                } else {
                    $errors['email'] = 'Email address not found';
                }
            }
        }

        $data = [
            'errors' => $errors,
            'success' => $success,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('auth/forgot_password', $data);
    }

    private function authenticateUser($username, $password) {
        // Check if username/email exists
        $user = $this->db->selectOne(
            "SELECT u.id, u.username, u.email, u.password, u.role, u.status,
                    CONCAT(up.first_name, ' ', up.last_name) as name
             FROM users u
             LEFT JOIN user_profiles up ON u.id = up.user_id
             WHERE (u.username = ? OR u.email = ?) AND u.status = 'active'",
            [$username, $username]
        );

        if ($user && Security::verifyPassword($password, $user['password'])) {
            // Update last login
            $this->db->update('users',
                ['last_login' => date('Y-m-d H:i:s')],
                'id = ?',
                [$user['id']]
            );

            return $user;
        }

        return false;
    }
}
?>