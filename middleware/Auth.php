<?php
/**
 * Authentication Middleware
 */

class AuthMiddleware {
    public function handle() {
        $session = Session::getInstance();

        if (!$session->isLoggedIn()) {
            // Redirect to login page
            header('Location: /login');
            exit;
        }

        // Update session activity
        $session->updateActivity();
    }
}
?>