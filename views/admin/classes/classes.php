<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes & Subjects - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .class-card {
            transition: transform 0.2s;
        }
        .class-card:hover {
            transform: translateY(-2px);
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
                        <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/students">Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/classes">Classes</a>
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

        <!-- Flash Messages -->
        <?php if (!empty($this->getFlash('success'))): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo htmlspecialchars(implode(', ', $this->getFlash('success'))); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($this->getFlash('error'))): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo htmlspecialchars(implode(', ', $this->getFlash('error'))); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <ul class="nav nav-tabs" id="classTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes" type="button" role="tab">Classes</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">Subjects</button>
            </li>
        </ul>

        <div class="tab-content mt-4" id="classTabsContent">
            <!-- Classes Tab -->
            <div class="tab-pane fade show active" id="classes" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Classes</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
                        <i class="fas fa-plus"></i> Add Class
                    </button>
                </div>

                <div class="row">
                    <?php foreach ($classes as $class): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card class-card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <?php echo htmlspecialchars($class['class_name'] . ' ' . $class['section']); ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <strong>Students:</strong> <?php echo $class['student_count']; ?>/<?php echo $class['capacity']; ?><br>
                                        <strong>Class Teacher:</strong> <?php echo htmlspecialchars($class['teacher_name'] ?? 'Not Assigned'); ?><br>
                                        <strong>Status:</strong>
                                        <span class="badge bg-<?php echo $class['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($class['status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="viewClassSubjects(<?php echo $class['id']; ?>)">
                                        <i class="fas fa-book"></i> Subjects
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="editClass(<?php echo $class['id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Subjects Tab -->
            <div class="tab-pane fade" id="subjects" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Subjects</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">
                        <i class="fas fa-plus"></i> Add Subject
                    </button>
                </div>

                <div class="row">
                    <?php foreach ($subjects as $subject): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($subject['subject_name']); ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($subject['subject_code']); ?></h6>
                                    <p class="card-text"><?php echo htmlspecialchars($subject['description'] ?? 'No description'); ?></p>
                                    <span class="badge bg-<?php echo $subject['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($subject['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Class Modal -->
    <div class="modal fade" id="addClassModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="/admin/add-class">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="mb-3">
                            <label class="form-label">Class Name</label>
                            <input type="text" class="form-control" name="class_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section</label>
                            <select class="form-select" name="section" required>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Capacity</label>
                            <input type="number" class="form-control" name="capacity" value="50" min="1" max="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Class Teacher</label>
                            <select class="form-select" name="class_teacher_id">
                                <option value="">Select Teacher (Optional)</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?php echo $teacher['id']; ?>">
                                        <?php echo htmlspecialchars($teacher['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Subject Modal -->
    <div class="modal fade" id="addSubjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Subject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="/admin/add-subject">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        <div class="mb-3">
                            <label class="form-label">Subject Name</label>
                            <input type="text" class="form-control" name="subject_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject Code</label>
                            <input type="text" class="form-control" name="subject_code" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewClassSubjects(classId) {
            // TODO: Implement view class subjects functionality
            alert('View subjects for class ' + classId);
        }

        function editClass(classId) {
            // TODO: Implement edit class functionality
            alert('Edit class ' + classId);
        }
    </script>
</body>
</html>