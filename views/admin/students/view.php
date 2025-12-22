<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student - School Management System</title>
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
                        <a class="nav-link active" href="/admin/students">Students</a>
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
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/students">Students</a></li>
                <li class="breadcrumb-item active">View Student</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Student Profile -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Student Profile</h5>
                    </div>
                    <div class="card-body text-center">
                        <?php if ($student['photo_path']): ?>
                            <img src="/<?php echo htmlspecialchars($student['photo_path']); ?>" alt="Student Photo" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h4>
                        <p class="text-muted">Roll No: <?php echo htmlspecialchars($student['roll_number']); ?></p>
                        <p class="text-muted">Admission No: <?php echo htmlspecialchars($student['admission_number'] ?? 'N/A'); ?></p>
                        <div class="mt-3">
                            <a href="/admin/students/edit/<?php echo $student['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="btn btn-danger btn-sm" onclick="deleteStudent(<?php echo $student['id']; ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Class Information -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Class Information</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Class:</strong> <?php echo htmlspecialchars($class['class_name'] . ' ' . $class['section']); ?></p>
                        <p><strong>Academic Year:</strong> <?php echo htmlspecialchars($academic_year['year_name']); ?></p>
                        <p><strong>Status:</strong>
                            <span class="badge bg-<?php echo $student['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($student['status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Student Details -->
            <div class="col-md-8">
                <!-- Personal Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Date of Birth:</strong> <?php echo date('d M Y', strtotime($student['date_of_birth'])); ?></p>
                                <p><strong>Gender:</strong> <?php echo ucfirst($student['gender']); ?></p>
                                <p><strong>Admission Date:</strong> <?php echo date('d M Y', strtotime($student['admission_date'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Mobile:</strong> <?php echo htmlspecialchars($student['mobile_number'] ?? 'N/A'); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></p>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($student['address'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family Information -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Family Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Father's Name:</strong> <?php echo htmlspecialchars($student['father_name'] ?? 'N/A'); ?></p>
                                <p><strong>Mother's Name:</strong> <?php echo htmlspecialchars($student['mother_name'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Guardian Contact:</strong> <?php echo htmlspecialchars($student['guardian_contact'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Summary -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Attendance Summary (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($attendance): ?>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h3 class="text-primary"><?php echo $attendance['total_days']; ?></h3>
                                    <p class="mb-0">Total Days</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-success"><?php echo $attendance['present_days']; ?></h3>
                                    <p class="mb-0">Present</p>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="text-danger"><?php echo $attendance['total_days'] - $attendance['present_days']; ?></h3>
                                    <p class="mb-0">Absent</p>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: <?php echo $attendance['total_days'] > 0 ? ($attendance['present_days'] / $attendance['total_days']) * 100 : 0; ?>%">
                                        <?php echo $attendance['total_days'] > 0 ? round(($attendance['present_days'] / $attendance['total_days']) * 100, 1) : 0; ?>%
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No attendance data available for the last 30 days.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Fee Summary -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Fee Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($fees)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Fee Type</th>
                                            <th>Amount</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Payment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($fees as $fee): ?>
                                            <tr>
                                                <td><?php echo ucfirst($fee['fee_type']); ?></td>
                                                <td>$<?php echo number_format($fee['amount'], 2); ?></td>
                                                <td><?php echo date('d M Y', strtotime($fee['due_date'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $fee['status'] === 'paid' ? 'success' : ($fee['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                                        <?php echo ucfirst($fee['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $fee['payment_date'] ? date('d M Y', strtotime($fee['payment_date'])) : '-'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No fee records found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteStudent(studentId) {
            if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
                fetch(`/admin/students/delete/${studentId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo $csrf_token; ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Student deleted successfully');
                        window.location.href = '/admin/students';
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Error deleting student');
                });
            }
        }
    </script>
</body>
</html>