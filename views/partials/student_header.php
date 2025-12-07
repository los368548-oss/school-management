<header class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container-fluid">
        <!-- Logo and School Name -->
        <a class="navbar-brand d-flex align-items-center" href="/student/dashboard">
            <img src="assets/logos/schoollogs/logo.png" alt="School Logo" height="40" class="me-2">
            <div>
                <div class="fw-bold">Student Portal</div>
                <small class="text-light opacity-75">A.s.higher secondary school</small>
            </div>
        </a>

        <!-- Mobile menu toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/student/dashboard">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/profile">
                        <i class="fas fa-user me-1"></i>My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/attendance">
                        <i class="fas fa-calendar-check me-1"></i>Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/results">
                        <i class="fas fa-graduation-cap me-1"></i>Results
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/fees">
                        <i class="fas fa-money-bill-wave me-1"></i>Fees
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/events">
                        <i class="fas fa-calendar-alt me-1"></i>Events
                    </a>
                </li>
            </ul>

            <!-- User Menu -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars($this->session->getUser()['username'] ?? 'Student'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/student/profile">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</header>