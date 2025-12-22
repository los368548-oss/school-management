<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .stat-card {
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/dashboard">
                <i class="fas fa-school"></i> School Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/students">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/classes">Classes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/attendance">Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/exams">Exams</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fees">Fees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/events">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/gallery">Gallery</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/homepage">Homepage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/reports">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/settings">Settings</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/admin/profile">Profile</a></li>
                            <li><a class="dropdown-item" href="/admin/change-password">Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <!-- Academic Year Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5 class="alert-heading">
                        <i class="fas fa-calendar-alt"></i> Current Academic Year: <?php echo htmlspecialchars($academic_year['year_name']); ?>
                    </h5>
                    <p class="mb-0">
                        <?php echo date('M d, Y', strtotime($academic_year['start_date'])); ?> -
                        <?php echo date('M d, Y', strtotime($academic_year['end_date'])); ?>
                        <a href="/admin/select-academic-year" class="alert-link ms-2">Change Year</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title"><?php echo number_format($stats['total_students']); ?></h5>
                                <p class="card-text">Total Students</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title"><?php echo number_format($stats['total_classes']); ?></h5>
                                <p class="card-text">Total Classes</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chalkboard fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title"><?php echo number_format($stats['total_exams']); ?></h5>
                                <p class="card-text">Total Exams</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h5 class="card-title">₹<?php echo number_format($stats['pending_fees']); ?></h5>
                                <p class="card-text">Pending Fees</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-rupee-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="/admin/students" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-user-plus"></i><br>Add Student
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="/admin/classes" class="btn btn-outline-success w-100">
                                    <i class="fas fa-plus-circle"></i><br>Add Class
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="/admin/attendance" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-calendar-check"></i><br>Mark Attendance
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="/admin/fees" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-money-bill"></i><br>Collect Fees
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities & Upcoming Events -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Activities</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recent_activities)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_activities as $activity): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($activity['action']); ?></h6>
                                            <small><?php echo date('M d, H:i', strtotime($activity['created_at'])); ?></small>
                                        </div>
                                        <p class="mb-1"><?php echo htmlspecialchars($activity['details'] ?? ''); ?></p>
                                        <small class="text-muted">By: <?php echo htmlspecialchars($activity['user_name'] ?? 'System'); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No recent activities</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Upcoming Events</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($upcoming_events)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($upcoming_events as $event): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                            <small><?php echo date('M d, Y', strtotime($event['event_date'])); ?></small>
                                        </div>
                                        <p class="mb-1"><?php echo htmlspecialchars(substr($event['description'], 0, 100)); ?>...</p>
                                        <?php if ($event['venue']): ?>
                                            <small class="text-muted">Venue: <?php echo htmlspecialchars($event['venue']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No upcoming events</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>