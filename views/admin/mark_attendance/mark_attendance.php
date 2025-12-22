<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance - School Management System</title>
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

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/attendance">Attendance</a></li>
                <li class="breadcrumb-item active">Mark Attendance</li>
            </ol>
        </nav>

        <!-- Class Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-users"></i>
                            <?php echo htmlspecialchars($class['class_name'] . ' ' . $class['section']); ?> -
                            Attendance for <?php echo date('l, F j, Y', strtotime($attendance_date)); ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Class Teacher:</strong> <?php echo htmlspecialchars($class['teacher_name'] ?? 'Not Assigned'); ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Total Students:</strong> <?php echo $class['student_count']; ?>
                            </div>
                            <div class="col-md-4">
                                <strong>Date:</strong> <?php echo date('d/m/Y', strtotime($attendance_date)); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Form -->
        <form method="POST" action="/admin/save-attendance">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
            <input type="hidden" name="attendance_date" value="<?php echo $attendance_date; ?>">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mark Student Attendance</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success me-2" onclick="markAllPresent()">
                            <i class="fas fa-check"></i> All Present
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAllAbsent()">
                            <i class="fas fa-times"></i> All Absent
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Roll No</th>
                                    <th>Student Name</th>
                                    <th>Scholar Number</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo $student['roll_number'] ?? '-'; ?></td>
                                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['scholar_number']); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <input type="radio" class="btn-check" name="attendance[<?php echo $student['id']; ?>]"
                                                       id="present_<?php echo $student['id']; ?>" value="present"
                                                       <?php echo (isset($attendance_map[$student['id']]) && $attendance_map[$student['id']] === 'present') ? 'checked' : ''; ?>>
                                                <label class="btn btn-outline-success" for="present_<?php echo $student['id']; ?>">
                                                    <i class="fas fa-check"></i> Present
                                                </label>

                                                <input type="radio" class="btn-check" name="attendance[<?php echo $student['id']; ?>]"
                                                       id="absent_<?php echo $student['id']; ?>" value="absent"
                                                       <?php echo (isset($attendance_map[$student['id']]) && $attendance_map[$student['id']] === 'absent') ? 'checked' : ''; ?>>
                                                <label class="btn btn-outline-danger" for="absent_<?php echo $student['id']; ?>">
                                                    <i class="fas fa-times"></i> Absent
                                                </label>

                                                <input type="radio" class="btn-check" name="attendance[<?php echo $student['id']; ?>]"
                                                       id="late_<?php echo $student['id']; ?>" value="late"
                                                       <?php echo (isset($attendance_map[$student['id']]) && $attendance_map[$student['id']] === 'late') ? 'checked' : ''; ?>>
                                                <label class="btn btn-outline-warning" for="late_<?php echo $student['id']; ?>">
                                                    <i class="fas fa-clock"></i> Late
                                                </label>

                                                <input type="radio" class="btn-check" name="attendance[<?php echo $student['id']; ?>]"
                                                       id="halfday_<?php echo $student['id']; ?>" value="half_day"
                                                       <?php echo (isset($attendance_map[$student['id']]) && $attendance_map[$student['id']] === 'half_day') ? 'checked' : ''; ?>>
                                                <label class="btn btn-outline-info" for="halfday_<?php echo $student['id']; ?>">
                                                    <i class="fas fa-adjust"></i> Half Day
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                   name="remarks[<?php echo $student['id']; ?>]"
                                                   placeholder="Optional remarks">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="/admin/attendance" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Attendance
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function markAllPresent() {
            const radios = document.querySelectorAll('input[type="radio"][value="present"]');
            radios.forEach(radio => radio.checked = true);
        }

        function markAllAbsent() {
            const radios = document.querySelectorAll('input[type="radio"][value="absent"]');
            radios.forEach(radio => radio.checked = true);
        }
    </script>
</body>
</html>