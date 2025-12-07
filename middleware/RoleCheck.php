<?php
/**
 * Role Check Middleware
 */

class AdminMiddleware {
    public function handle() {
        $session = Session::getInstance();

        if (!$session->isAdmin()) {
            // Redirect to unauthorized page or dashboard
            header('Location: /unauthorized');
            exit;
        }
    }
}

class StudentMiddleware {
    public function handle() {
        $session = Session::getInstance();

        if (!$session->isStudent()) {
            // Redirect to unauthorized page or dashboard
            header('Location: /unauthorized');
            exit;
        }
    }
}
?>