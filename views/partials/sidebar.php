<nav id="sidebar" class="bg-light border-end vh-100 position-fixed" style="width: 250px; left: 0; top: 76px; z-index: 1000; overflow-y: auto;">
    <div class="p-3">
        <!-- Quick Stats -->
        <div class="mb-4">
            <h6 class="text-muted mb-3">Quick Stats</h6>
            <div class="row g-2">
                <div class="col-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body p-2 text-center">
                            <div class="fs-4" id="totalStudents">0</div>
                            <small>Students</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card bg-success text-white">
                        <div class="card-body p-2 text-center">
                            <div class="fs-4" id="totalClasses">0</div>
                            <small>Classes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/admin/dashboard') ? 'active' : ''; ?>" href="/admin/dashboard">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/students') === 0) ? 'active' : ''; ?>" href="/admin/students">
                    <i class="fas fa-users me-2"></i>Students
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/classes') === 0) ? 'active' : ''; ?>" href="/admin/classes">
                    <i class="fas fa-school me-2"></i>Classes & Subjects
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/attendance') === 0) ? 'active' : ''; ?>" href="/admin/attendance">
                    <i class="fas fa-calendar-check me-2"></i>Attendance
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/exams') === 0) ? 'active' : ''; ?>" href="/admin/exams">
                    <i class="fas fa-graduation-cap me-2"></i>Exams & Results
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/fees') === 0) ? 'active' : ''; ?>" href="/admin/fees">
                    <i class="fas fa-money-bill-wave me-2"></i>Fees
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/events') === 0) ? 'active' : ''; ?>" href="/admin/events">
                    <i class="fas fa-calendar-alt me-2"></i>Events
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/gallery') === 0) ? 'active' : ''; ?>" href="/admin/gallery">
                    <i class="fas fa-images me-2"></i>Gallery
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/reports') === 0) ? 'active' : ''; ?>" href="/admin/reports">
                    <i class="fas fa-chart-bar me-2"></i>Reports
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/admin/settings') === 0) ? 'active' : ''; ?>" href="/admin/settings">
                    <i class="fas fa-cog me-2"></i>Settings
                </a>
            </li>
        </ul>

        <!-- User Profile Section -->
        <div class="mt-4 pt-3 border-top">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                </div>
                <div class="flex-grow-1 ms-2">
                    <div class="fw-bold"><?php echo htmlspecialchars($this->session->getUser()['username'] ?? 'User'); ?></div>
                    <small class="text-muted"><?php echo htmlspecialchars($this->session->getUser()['role_name'] ?? 'Role'); ?></small>
                </div>
            </div>
            <div class="mt-2">
                <a href="/logout" class="btn btn-outline-danger btn-sm w-100">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
// Load quick stats
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/v1/dashboard/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalStudents').textContent = data.data.students || 0;
                document.getElementById('totalClasses').textContent = data.data.classes || 0;
            }
        })
        .catch(error => console.error('Error loading stats:', error));
});
</script>