<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img src="assets/logos/schoollogs/logo.png" alt="School Logo" height="50" class="me-2">
            <div>
                <div class="fw-bold text-primary">A.s.higher secondary school</div>
                <small class="text-muted d-none d-md-block">Excellence in Education</small>
            </div>
        </a>

        <!-- Mobile menu toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/') ? 'active' : ''; ?>" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/about') ? 'active' : ''; ?>" href="/about">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/courses') ? 'active' : ''; ?>" href="/courses">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/events') ? 'active' : ''; ?>" href="/events">Events</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/gallery') ? 'active' : ''; ?>" href="/gallery">Gallery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] === '/contact') ? 'active' : ''; ?>" href="/contact">Contact</a>
                </li>
                <li class="nav-item ms-2">
                    <a class="btn btn-primary btn-sm" href="/login">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>