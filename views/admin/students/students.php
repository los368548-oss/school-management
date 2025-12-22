<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 0.75rem 1rem;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: #0d6efd;
        }
        .content-wrapper {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            .content-wrapper {
                margin-left: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar d-md-block collapse" id="sidebar">
        <div class="sidebar-sticky">
            <div class="p-3">
                <h5 class="text-white mb-4">
                    <i class="fas fa-school"></i> School Admin
                </h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/students">
                            <i class="fas fa-users"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/classes">
                            <i class="fas fa-chalkboard"></i> Classes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/attendance">
                            <i class="fas fa-calendar-check"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/exams">
                            <i class="fas fa-file-alt"></i> Exams
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fees">
                            <i class="fas fa-money-bill"></i> Fees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/events">
                            <i class="fas fa-calendar"></i> Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/gallery">
                            <i class="fas fa-images"></i> Gallery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/homepage">
                            <i class="fas fa-home"></i> Homepage
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/reports">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/settings">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
            <div class="p-3 border-top border-secondary">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle fa-2x text-white me-2"></i>
                    <div>
                        <small class="text-white">Admin</small><br>
                        <a href="/logout" class="text-white-50 small">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand mb-0 h1">Student Management</span>
                <div class="d-flex">
                    <span class="badge bg-info me-2">
                        Academic Year: <?php echo htmlspecialchars($academic_year['year_name'] ?? 'Not Set'); ?>
                    </span>
                    <a href="/admin/select-academic-year" class="btn btn-sm btn-outline-primary">Change Year</a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Students</h2>
                    <p class="text-muted">Manage student records and information</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fas fa-plus"></i> Add Student
                    </button>
                    <div class="btn-group ms-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel"></i> Excel</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf"></i> PDF</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-csv"></i> CSV</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Class</label>
                            <select class="form-select" id="classFilter">
                                <option value="">All Classes</option>
                                <option value="1">Class 1</option>
                                <option value="2">Class 2</option>
                                <!-- Add more classes dynamically -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Section</label>
                            <select class="form-select" id="sectionFilter">
                                <option value="">All Sections</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="transferred">Transferred</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="searchFilter" placeholder="Name, Scholar No, Admission No">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Photo</th>
                                    <th>Scholar No</th>
                                    <th>Admission No</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Roll No</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($students)): ?>
                                    <?php foreach ($students as $student): ?>
                                        <tr>
                                            <td>
                                                <?php if ($student['profile_image']): ?>
                                                    <img src="/uploads/profiles/<?php echo htmlspecialchars($student['profile_image']); ?>"
                                                         class="rounded-circle" width="40" height="40" alt="Profile">
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($student['scholar_number']); ?></td>
                                            <td><?php echo htmlspecialchars($student['admission_number']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($student['class_name'] . ' ' . $student['section']); ?></td>
                                            <td><?php echo htmlspecialchars($student['roll_number'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($student['mobile_number'] ?? '-'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo $student['status'] === 'active' ? 'success' :
                                                         ($student['status'] === 'inactive' ? 'warning' : 'secondary');
                                                ?>">
                                                    <?php echo ucfirst($student['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info" title="Attendance">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" title="Fees">
                                                        <i class="fas fa-money-bill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <p>No students found for the current academic year.</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                                <i class="fas fa-plus"></i> Add First Student
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addStudentForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <!-- Basic Information -->
                            <div class="col-12">
                                <h6 class="fw-bold text-primary">Basic Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Scholar Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="scholar_number" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="admission_number" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="admission_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Class & Section <span class="text-danger">*</span></label>
                                <select class="form-select" name="class_id" required>
                                    <option value="">Select Class</option>
                                    <!-- Add classes dynamically -->
                                </select>
                            </div>

                            <!-- Personal Information -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary">Personal Information</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middle_name">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_of_birth" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary">Contact Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" name="mobile_number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2"></textarea>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary">Additional Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Father's Name</label>
                                <input type="text" class="form-control" name="father_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mother's Name</label>
                                <input type="text" class="form-control" name="mother_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Blood Group</label>
                                <select class="form-select" name="blood_group">
                                    <option value="">Select Blood Group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Medical Conditions</label>
                                <input type="text" class="form-control" name="medical_conditions" placeholder="Any allergies or conditions">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#studentsTable').DataTable({
                pageLength: 25,
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0, 8] }
                ]
            });

            // Handle form submission
            $('#addStudentForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '/api/v1/students',
                    method: 'POST',
                    data: JSON.stringify(Object.fromEntries(formData)),
                    contentType: 'application/json',
                    headers: {
                        'X-API-Token': '<?php echo $_SESSION["api_token"] ?? ""; ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#addStudentModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(xhr) {
                        alert('Error adding student: ' + xhr.responseJSON?.error || 'Unknown error');
                    }
                });
            });
        });
    </script>
</body>
</html>