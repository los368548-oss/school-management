<nav id="sidebar" class="bg-light border-end vh-100 position-fixed" style="width: 250px; left: 0; top: 76px; z-index: 1000; overflow-y: auto;">
    <div class="p-3">
        <!-- Student Info -->
        <div class="mb-4">
            <h6 class="text-muted mb-3">My Information</h6>
            <div class="card">
                <div class="card-body text-center">
                    <?php
                    $student = $student ?? [];
                    if (!empty($student['photo_path'])): ?>
                        <img src="<?php echo htmlspecialchars($student['photo_path']); ?>"
                             alt="Profile Photo" class="rounded-circle mb-2" width="60" height="60">
                    <?php else: ?>
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                             style="width: 60px; height: 60px;">
                            <i class="fas fa-user text-white fa-lg"></i>
                        </div>
                    <?php endif; ?>
                    <h6 class="mb-1"><?php echo htmlspecialchars(($student['first_name'] ?? '') . ' ' . ($student['last_name'] ?? '')); ?></h6>
                    <small class="text-muted">
                        <?php echo htmlspecialchars(($student['class_name'] ?? '') . ' ' . ($student['class_section'] ?? '')); ?>
                    </small>
                    <br>
                    <small class="text-muted">Scholar No: <?php echo htmlspecialchars($student['scholar_number'] ?? ''); ?></small>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mb-4">
            <h6 class="text-muted mb-3">Quick Stats</h6>
            <div class="row g-2">
                <div class="col-6">
                    <div class="card bg-info text-white">
                        <div class="card-body p-2 text-center">
                            <div class="fs-4" id="attendancePercent">0%</div>
                            <small>Attendance</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body p-2 text-center">
                            <div class="fs-4" id="pendingFees">₹0</div>
                            <small>Pending Fees</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/student/dashboard') ? 'active' : ''; ?>" href="/student/dashboard">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/student/profile') ? 'active' : ''; ?>" href="/student/profile">
                    <i class="fas fa-user me-2"></i>My Profile
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/student/attendance') === 0) ? 'active' : ''; ?>" href="/student/attendance">
                    <i class="fas fa-calendar-check me-2"></i>Attendance
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/student/results') === 0) ? 'active' : ''; ?>" href="/student/results">
                    <i class="fas fa-graduation-cap me-2"></i>Exam Results
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/student/fees') === 0) ? 'active' : ''; ?>" href="/student/fees">
                    <i class="fas fa-money-bill-wave me-2"></i>Fees
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/student/events') === 0) ? 'active' : ''; ?>" href="/student/events">
                    <i class="fas fa-calendar-alt me-2"></i>Events
                </a>
            </li>
        </ul>

        <!-- Quick Links -->
        <div class="mt-4 pt-3 border-top">
            <h6 class="text-muted mb-2">Quick Links</h6>
            <div class="d-grid gap-2">
                <a href="/" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>School Website
                </a>
                <a href="/logout" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
// Load quick stats for student
document.addEventListener('DOMContentLoaded', function() {
    // Load attendance percentage
    fetch('/api/v1/attendance/stats?student_id=' + (<?php echo json_encode($this->getCurrentUserId()); ?>))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                document.getElementById('attendancePercent').textContent = data.data.percentage + '%';
            }
        })
        .catch(error => console.error('Error loading attendance:', error));

    // Load pending fees
    fetch('/api/v1/fees/status?student_id=' + (<?php echo json_encode($this->getCurrentUserId()); ?>))
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                document.getElementById('pendingFees').textContent = '₹' + data.data.pending_amount;
            }
        })
        .catch(error => console.error('Error loading fees:', error));
});
</script>