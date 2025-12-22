<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                        <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/students">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/classes">Classes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/attendance">Attendance</a>
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

        <!-- Today's Attendance Summary -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Today's Attendance Summary (<?php echo date('l, F j, Y'); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary"><?php echo $attendance_stats['total_students']; ?></h3>
                                        <p class="mb-0">Total Students</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3><?php echo $attendance_stats['present_today']; ?></h3>
                                        <p class="mb-0">Present Today</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3><?php echo $attendance_stats['absent_today']; ?></h3>
                                        <p class="mb-0">Absent Today</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class-wise Attendance -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Class-wise Attendance</h5>
                        <div>
                            <input type="date" id="attendanceDate" class="form-control d-inline-block w-auto me-2"
                                   value="<?php echo date('Y-m-d'); ?>" onchange="changeDate()">
                            <button class="btn btn-primary" onclick="markAttendance()">
                                <i class="fas fa-calendar-check"></i> Mark Attendance
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Total Students</th>
                                        <th>Present</th>
                                        <th>Absent</th>
                                        <th>Attendance %</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($class_summary as $class): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($class['class_name'] . ' ' . $class['section']); ?></td>
                                            <td><?php echo $class['total_students']; ?></td>
                                            <td><span class="badge bg-success"><?php echo $class['present_count'] ?? 0; ?></span></td>
                                            <td><span class="badge bg-danger"><?php echo $class['absent_count'] ?? 0; ?></span></td>
                                            <td>
                                                <?php
                                                $percentage = $class['total_students'] > 0 ?
                                                    round((($class['present_count'] ?? 0) / $class['total_students']) * 100, 1) : 0;
                                                $colorClass = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                                                ?>
                                                <span class="badge bg-<?php echo $colorClass; ?>"><?php echo $percentage; ?>%</span>
                                            </td>
                                            <td>
                                                <a href="/admin/mark-attendance?class_id=<?php echo $class['id']; ?>&date=<?php echo date('Y-m-d'); ?>"
                                                   class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-edit"></i> Mark
                                                </a>
                                                <a href="/admin/attendance-report?class_id=<?php echo $class['id']; ?>"
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-chart-bar"></i> Report
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function changeDate() {
            const date = document.getElementById('attendanceDate').value;
            // Reload page with new date parameter
            window.location.href = '/admin/attendance?date=' + date;
        }

        function markAttendance() {
            const date = document.getElementById('attendanceDate').value;
            // Redirect to mark attendance for first class (you can enhance this to show class selection)
            const firstClass = <?php echo !empty($class_summary) ? $class_summary[0]['id'] : 'null'; ?>;
            if (firstClass) {
                window.location.href = '/admin/mark-attendance?class_id=' + firstClass + '&date=' + date;
            }
        }
    </script>
</body>
</html>