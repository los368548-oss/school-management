<?php
/**
 * Authentication Controller
 */

class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }

    /**
     * Show login form
     */
    public function loginForm() {
        // If already logged in, redirect to appropriate dashboard
        if ($this->session->isLoggedIn()) {
            $this->redirectBasedOnRole();
        }

        $csrf_token = $this->session->generateCsrfToken();

        $this->view('auth/login', [
            'title' => 'Login',
            'csrf_token' => $csrf_token,
            'show_header' => false,
            'show_sidebar' => false,
            'show_footer' => false
        ]);
    }

    /**
     * Process login
     */
    public function login() {
        // Validate CSRF token
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'username' => 'required',
            'password' => 'required|min:6'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please fill in all required fields.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/login');
        }

        $username = $data['username'];
        $password = $data['password'];
        $remember = isset($data['remember']) ? true : false;

        // Find user
        $user = $this->userModel->findByUsernameOrEmail($username);

        if (!$user || !$this->security->verifyPassword($password, $user['password'])) {
            $this->logAction('login_failed', ['username' => $username]);
            $this->session->setFlash('message', 'Invalid username or password.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/login');
        }

        // Check if user is active
        if (!$user['is_active']) {
            $this->session->setFlash('message', 'Your account has been deactivated.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/login');
        }

        // Set session
        $this->session->setUser($user);

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Log successful login
        $this->logAction('login_successful', ['user_id' => $user['id']]);

        // Redirect based on role
        $this->redirectBasedOnRole();
    }

    /**
     * Logout user
     */
    public function logout() {
        $userId = $this->session->getUserId();
        $this->logAction('logout', ['user_id' => $userId]);

        $this->session->destroy();
        $this->redirect('/login');
    }

    /**
     * Show forgot password form
     */
    public function forgotPassword() {
        $csrf_token = $this->session->generateCsrfToken();

        $this->view('auth/forgot_password', [
            'title' => 'Forgot Password',
            'csrf_token' => $csrf_token,
            'show_header' => false,
            'show_sidebar' => false,
            'show_footer' => false
        ]);
    }

    /**
     * Process password reset
     */
    public function resetPassword() {
        $this->validateCsrf();

        $data = $this->getPostData();

        $this->validator->setData($data);
        $this->validator->setRules([
            'email' => 'required|email'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please enter a valid email address.');
            $this->session->setFlash('message_type', 'danger');
            $this->redirect('/forgot-password');
        }

        $email = $data['email'];
        $user = $this->userModel->findBy(['email' => $email]);

        if ($user) {
            // Generate reset token (simplified - in production, use proper token generation)
            $resetToken = $this->security->generateRandomString(32);

            // Store token in database (you would need to add a password_resets table)
            // For now, just log it
            $this->logAction('password_reset_requested', ['email' => $email]);

            $this->session->setFlash('message', 'Password reset instructions have been sent to your email.');
            $this->session->setFlash('message_type', 'success');
        } else {
            // Don't reveal if email exists or not for security
            $this->session->setFlash('message', 'If the email exists, password reset instructions have been sent.');
            $this->session->setFlash('message_type', 'info');
        }

        $this->redirect('/forgot-password');
    }

    /**
     * Redirect based on user role
     */
    private function redirectBasedOnRole() {
        if ($this->session->isAdmin()) {
            $this->redirect('/admin/dashboard');
        } elseif ($this->session->isStudent()) {
            $this->redirect('/student/dashboard');
        } else {
            $this->redirect('/login');
        }
    }
}
?>