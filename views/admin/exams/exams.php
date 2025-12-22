<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examination Management - School Management System</title>
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
                        <a class="nav-link" href="/admin/attendance">Attendance</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/exams">Exams</a>
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

        <!-- Exams List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Examinations</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createExamModal">
                            <i class="fas fa-plus"></i> Create Exam
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exam Name</th>
                                        <th>Class</th>
                                        <th>Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Subjects</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($exams as $exam): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($exam['exam_name']); ?></td>
                                            <td><?php echo htmlspecialchars($exam['class_name'] . ' ' . $exam['section']); ?></td>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $exam['exam_type'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($exam['start_date'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($exam['end_date'])); ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo $exam['status'] === 'completed' ? 'success' :
                                                         ($exam['status'] === 'ongoing' ? 'warning' : 'secondary');
                                                ?>">
                                                    <?php echo ucfirst($exam['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $exam['subject_count']; ?></td>
                                            <td>
                                                <a href="/admin/enter-results?exam_id=<?php echo $exam['id']; ?>"
                                                   class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-edit"></i> Results
                                                </a>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                            type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-print"></i> Print
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="/admin/generate-admit-card?exam_id=<?php echo $exam['id']; ?>&student_id=1">
                                                            <i class="fas fa-id-card"></i> Admit Card
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="/admin/generate-marksheet?exam_id=<?php echo $exam['id']; ?>&student_id=1">
                                                            <i class="fas fa-certificate"></i> Marksheet
                                                        </a></li>
                                                    </ul>
                                                </div>
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

    <!-- Create Exam Modal -->
    <div class="modal fade" id="createExamModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Examination</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="/admin/create-exam">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Exam Name</label>
                                <input type="text" class="form-control" name="exam_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Exam Type</label>
                                <select class="form-select" name="exam_type">
                                    <option value="custom">Custom</option>
                                    <option value="mid_term">Mid Term</option>
                                    <option value="final">Final</option>
                                    <option value="unit_test">Unit Test</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Class</label>
                                <select class="form-select" name="class_id" required>
                                    <option value="">Select Class</option>
                                    <!-- Classes would be populated here -->
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>

                        <h6>Exam Subjects</h6>
                        <div id="subjects-container">
                            <div class="subject-row border p-3 mb-3 rounded">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">Subject</label>
                                        <select class="form-select" name="subjects[0][subject_id]" required>
                                            <option value="">Select Subject</option>
                                            <!-- Subjects would be populated here -->
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Date</label>
                                        <input type="date" class="form-control" name="subjects[0][exam_date]" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Start Time</label>
                                        <input type="time" class="form-control" name="subjects[0][start_time]" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">End Time</label>
                                        <input type="time" class="form-control" name="subjects[0][end_time]" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Max Marks</label>
                                        <input type="number" class="form-control" name="subjects[0][max_marks]" value="100" required>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeSubject(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSubject()">
                            <i class="fas fa-plus"></i> Add Subject
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Exam</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let subjectIndex = 1;

        function addSubject() {
            const container = document.getElementById('subjects-container');
            const newRow = container.querySelector('.subject-row').cloneNode(true);

            // Update input names
            const inputs = newRow.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace('[0]', '[' + subjectIndex + ']'));
                    input.value = '';
                }
            });

            container.appendChild(newRow);
            subjectIndex++;
        }

        function removeSubject(button) {
            if (document.querySelectorAll('.subject-row').length > 1) {
                button.closest('.subject-row').remove();
            }
        }
    </script>
</body>
</html>