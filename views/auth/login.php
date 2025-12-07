<?php
// Extract variables
$csrf_token = $csrf_token ?? '';
$flash_message = $session->getFlash('message');
$flash_type = $session->getFlash('message_type') ?: 'info';
?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="row w-100 justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <img src="assets/logos/schoollogs/logo.png" alt="School Logo" class="mb-3" style="max-height: 60px;">
                    <h4 class="mb-0">A.s.higher secondary school</h4>
                    <p class="mb-0">Management System</p>
                </div>
                <div class="card-body">
                    <?php if ($flash_message): ?>
                        <div class="alert alert-<?php echo $flash_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo $flash_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/login" id="loginForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <div class="mb-3">
                            <label for="username" class="form-label">Username or Email</label>
                            <input type="text" class="form-control" id="username" name="username"
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                            <div class="invalid-feedback">
                                Please enter your username or email.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">
                                Please enter your password.
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="loginBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="/forgot-password" class="text-decoration-none">Forgot your password?</a>
                    </div>
                </div>
                <div class="card-footer text-center text-muted">
                    <small>&copy; 2024 A.s.higher secondary school. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!username || !password) {
        e.preventDefault();
        alert('Please fill in all fields.');
        return false;
    }

    // Show loading state
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
});
</script>