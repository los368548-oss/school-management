<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class - School Management System</title>
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
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="/admin/classes">Classes</a></li>
                        <li class="breadcrumb-item active">Edit Class</li>
                    </ol>
                </nav>
                <h2><i class="fas fa-edit"></i> Edit Class</h2>
            </div>
        </div>

        <!-- Form -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($errors['general'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($errors['general']); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="/admin/classes/edit/<?php echo $class['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Class Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errors['class_name']) ? 'is-invalid' : ''; ?>" name="class_name" value="<?php echo htmlspecialchars($class['class_name']); ?>" required>
                                    <?php if (isset($errors['class_name'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['class_name']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Section <span class="text-danger">*</span></label>
                                    <select class="form-select <?php echo isset($errors['section']) ? 'is-invalid' : ''; ?>" name="section" required>
                                        <option value="">Select Section</option>
                                        <option value="A" <?php echo $class['section'] === 'A' ? 'selected' : ''; ?>>A</option>
                                        <option value="B" <?php echo $class['section'] === 'B' ? 'selected' : ''; ?>>B</option>
                                        <option value="C" <?php echo $class['section'] === 'C' ? 'selected' : ''; ?>>C</option>
                                        <option value="D" <?php echo $class['section'] === 'D' ? 'selected' : ''; ?>>D</option>
                                    </select>
                                    <?php if (isset($errors['section'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['section']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Capacity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php echo isset($errors['capacity']) ? 'is-invalid' : ''; ?>" name="capacity" value="<?php echo htmlspecialchars($class['capacity']); ?>" min="1" max="100" required>
                                    <?php if (isset($errors['capacity'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['capacity']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select <?php echo isset($errors['status']) ? 'is-invalid' : ''; ?>" name="status" required>
                                        <option value="active" <?php echo $class['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $class['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                    <?php if (isset($errors['status'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['status']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Class Teacher</label>
                                    <select class="form-select" name="class_teacher_id">
                                        <option value="">Select Class Teacher (Optional)</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?php echo $teacher['id']; ?>" <?php echo ($class['class_teacher_id'] ?? '') == $teacher['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($teacher['full_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Class
                                </button>
                                <a href="/admin/classes" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Class Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Total Students:</span>
                                    <strong><?php echo htmlspecialchars($class['student_count'] ?? 0); ?></strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Capacity:</span>
                                    <strong><?php echo htmlspecialchars($class['capacity']); ?></strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Available Seats:</span>
                                    <strong><?php echo htmlspecialchars(($class['capacity'] ?? 0) - ($class['student_count'] ?? 0)); ?></strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Class Teacher:</span>
                                    <strong><?php echo htmlspecialchars($class['teacher_name'] ?? 'Not Assigned'); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($class['student_count']) && $class['student_count'] > 0): ?>
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Warning</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                This class has <?php echo $class['student_count']; ?> students assigned.
                                Changing the status to inactive may affect student records.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>