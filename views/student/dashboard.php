<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.9);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 8px;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.2);
            font-weight: 600;
        }
        .content-wrapper {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            .content-wrapper {
                margin-left: 280px;
            }
        }
        .stat-card {
            transition: transform 0.2s;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar d-md-block collapse" id="sidebar">
        <div class="sidebar-sticky p-3">
            <div class="text-center mb-4">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                     style="width: 60px; height: 60px;">
                    <i class="fas fa-user-graduate fa-2x text-primary"></i>
                </div>
                <h6 class="text-white mb-0">Student Portal</h6>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="/student/dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/profile">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/attendance">
                        <i class="fas fa-calendar-check"></i> Attendance
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/results">
                        <i class="fas fa-chart-line"></i> Results
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/fees">
                        <i class="fas fa-money-bill"></i> Fees
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/assignments">
                        <i class="fas fa-book"></i> Assignments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/timetable">
                        <i class="fas fa-clock"></i> Timetable
                    </a>
                </li>
            </ul>

            <div class="mt-auto pt-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center me-2"
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                    <div class="flex-grow-1">
                        <small class="text-white d-block"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></small>
                        <small class="text-white-50"><?php echo htmlspecialchars($student['class_name'] . ' ' . $student['section']); ?></small>
                    </div>
                </div>
                <a href="/logout" class="btn btn-outline-light btn-sm w-100 mt-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="d-flex align-items-center">
                    <h5 class="mb-0 me-3">Welcome back, <?php echo htmlspecialchars($student['first_name']); ?>!</h5>
                    <span class="badge bg-primary"><?php echo htmlspecialchars($academic_year['year_name']); ?></span>
                </div>

                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger rounded-pill ms-1">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><a class="dropdown-item" href="#">Fee payment due</a></li>
                            <li><a class="dropdown-item" href="#">New assignment posted</a></li>
                            <li><a class="dropdown-item" href="#">Exam schedule updated</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="container-fluid p-4">
            <!-- Welcome Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card welcome-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="card-title mb-2">Welcome to Your Dashboard</h3>
                                    <p class="card-text mb-0">
                                        Track your academic progress, view attendance, check results, and manage your profile.
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                         style="width: 80px; height: 80px;">
                                        <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                            <h4 class="card-title mb-1"><?php echo $attendance_summary['attendance_percentage'] ?? 0; ?>%</h4>
                            <p class="card-text mb-0">Attendance</p>
                            <small><?php echo $attendance_summary['present_days'] ?? 0; ?>/<?php echo $attendance_summary['total_days'] ?? 0; ?> days</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-info text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <h4 class="card-title mb-1"><?php echo count($recent_results); ?></h4>
                            <p class="card-text mb-0">Exam Results</p>
                            <small>Recent exams</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-rupee-sign fa-2x mb-2"></i>
                            <h4 class="card-title mb-1">₹<?php echo number_format($fee_status['pending_amount'] ?? 0); ?></h4>
                            <p class="card-text mb-0">Pending Fees</p>
                            <small>Outstanding amount</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stat-card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h4 class="card-title mb-1"><?php echo count($upcoming_exams); ?></h4>
                            <p class="card-text mb-0">Upcoming Exams</p>
                            <small>This month</small>
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
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="/student/profile" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                        <i class="fas fa-user fa-2x mb-2"></i>
                                        <span>Update Profile</span>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="/student/attendance" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                        <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                        <span>View Attendance</span>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="/student/results" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                                        <span>Check Results</span>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="/student/fees" class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                        <i class="fas fa-money-bill fa-2x mb-2"></i>
                                        <span>Pay Fees</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Upcoming Events -->
            <div class="row">
                <!-- Recent Exam Results -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Exam Results</h5>
                            <a href="/student/results" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_results)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach (array_slice($recent_results, 0, 3) as $result): ?>
                                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($result['subject_name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($result['exam_name']); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-<?php echo ($result['marks_obtained'] / $result['max_marks'] * 100) >= 40 ? 'success' : 'danger'; ?>">
                                                    <?php echo $result['marks_obtained']; ?>/<?php echo $result['max_marks']; ?>
                                                </span>
                                                <br>
                                                <small class="text-muted"><?php echo $result['grade']; ?></small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                                    <p>No exam results available yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Exams -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Upcoming Exams</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($upcoming_exams)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach (array_slice($upcoming_exams, 0, 3) as $exam): ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($exam['exam_name']); ?></h6>
                                                    <p class="mb-1 text-muted small"><?php echo htmlspecialchars($exam['exam_type']); ?> Exam</p>
                                                </div>
                                                <span class="badge bg-primary">
                                                    <?php echo date('M d', strtotime($exam['start_date'])); ?>
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo date('D, M d, Y', strtotime($exam['start_date'])); ?> -
                                                <?php echo date('D, M d, Y', strtotime($exam['end_date'])); ?>
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-calendar fa-3x mb-3"></i>
                                    <p>No upcoming exams scheduled.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Status Alert -->
            <?php if (($fee_status['pending_amount'] ?? 0) > 0): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Fee Payment Due:</strong> You have ₹<?php echo number_format($fee_status['pending_amount']); ?> pending fees.
                            <a href="/student/fees" class="alert-link">Pay now</a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Auto-refresh attendance percentage every 30 seconds
            setInterval(function() {
                // Could add AJAX call to refresh attendance data
            }, 30000);
        });
    </script>
</body>
</html>