<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - School Management System</title>
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
                        <li class="breadcrumb-item"><a href="/admin/students">Students</a></li>
                        <li class="breadcrumb-item active">Edit Student</li>
                    </ol>
                </nav>
                <h2><i class="fas fa-edit"></i> Edit Student</h2>
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

                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                            <div class="row g-3">
                                <!-- Basic Information -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-primary">Basic Information</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Scholar Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errors['scholar_number']) ? 'is-invalid' : ''; ?>" name="scholar_number" value="<?php echo htmlspecialchars($student['scholar_number'] ?? ''); ?>" required>
                                    <?php if (isset($errors['scholar_number'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['scholar_number']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Admission Number</label>
                                    <input type="text" class="form-control" name="admission_number" value="<?php echo htmlspecialchars($student['admission_number'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?php echo isset($errors['admission_date']) ? 'is-invalid' : ''; ?>" name="admission_date" value="<?php echo htmlspecialchars($student['admission_date'] ?? ''); ?>" required>
                                    <?php if (isset($errors['admission_date'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['admission_date']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Class & Section <span class="text-danger">*</span></label>
                                    <select class="form-select <?php echo isset($errors['class_id']) ? 'is-invalid' : ''; ?>" name="class_id" required>
                                        <option value="">Select Class</option>
                                        <?php foreach ($classes as $class): ?>
                                            <option value="<?php echo $class['id']; ?>" <?php echo ($student['class_id'] ?? '') == $class['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($class['class_name'] . ' ' . $class['section']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['class_id'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['class_id']); ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Personal Information -->
                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold text-primary">Personal Information</h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" name="first_name" value="<?php echo htmlspecialchars($student['first_name'] ?? ''); ?>" required>
                                    <?php if (isset($errors['first_name'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['first_name']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" name="middle_name" value="<?php echo htmlspecialchars($student['middle_name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" name="last_name" value="<?php echo htmlspecialchars($student['last_name'] ?? ''); ?>" required>
                                    <?php if (isset($errors['last_name'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['last_name']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control <?php echo isset($errors['date_of_birth']) ? 'is-invalid' : ''; ?>" name="date_of_birth" value="<?php echo htmlspecialchars($student['date_of_birth'] ?? ''); ?>" required>
                                    <?php if (isset($errors['date_of_birth'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['date_of_birth']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select class="form-select <?php echo isset($errors['gender']) ? 'is-invalid' : ''; ?>" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo ($student['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($student['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo ($student['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                    <?php if (isset($errors['gender'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['gender']); ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold text-primary">Contact Information</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="tel" class="form-control" name="mobile_number" value="<?php echo htmlspecialchars($student['mobile_number'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                                </div>

                                <!-- Additional Information -->
                                <div class="col-12 mt-4">
                                    <h6 class="fw-bold text-primary">Additional Information</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Father's Name</label>
                                    <input type="text" class="form-control" name="father_name" value="<?php echo htmlspecialchars($student['father_name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Mother's Name</label>
                                    <input type="text" class="form-control" name="mother_name" value="<?php echo htmlspecialchars($student['mother_name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Blood Group</label>
                                    <select class="form-select" name="blood_group">
                                        <option value="">Select Blood Group</option>
                                        <option value="A+" <?php echo ($student['blood_group'] ?? '') === 'A+' ? 'selected' : ''; ?>>A+</option>
                                        <option value="A-" <?php echo ($student['blood_group'] ?? '') === 'A-' ? 'selected' : ''; ?>>A-</option>
                                        <option value="B+" <?php echo ($student['blood_group'] ?? '') === 'B+' ? 'selected' : ''; ?>>B+</option>
                                        <option value="B-" <?php echo ($student['blood_group'] ?? '') === 'B-' ? 'selected' : ''; ?>>B-</option>
                                        <option value="AB+" <?php echo ($student['blood_group'] ?? '') === 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                        <option value="AB-" <?php echo ($student['blood_group'] ?? '') === 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                        <option value="O+" <?php echo ($student['blood_group'] ?? '') === 'O+' ? 'selected' : ''; ?>>O+</option>
                                        <option value="O-" <?php echo ($student['blood_group'] ?? '') === 'O-' ? 'selected' : ''; ?>>O-</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Medical Conditions</label>
                                    <input type="text" class="form-control" name="medical_conditions" value="<?php echo htmlspecialchars($student['medical_conditions'] ?? ''); ?>" placeholder="Any allergies or conditions">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Roll Number <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control <?php echo isset($errors['roll_number']) ? 'is-invalid' : ''; ?>" name="roll_number" value="<?php echo htmlspecialchars($student['roll_number'] ?? ''); ?>" required>
                                    <?php if (isset($errors['roll_number'])): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['roll_number']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="active" <?php echo ($student['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($student['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="transferred" <?php echo ($student['status'] ?? '') === 'transferred' ? 'selected' : ''; ?>>Transferred</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control" name="photo" accept="image/*">
                                    <small class="text-muted">Leave empty to keep current photo</small>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Student
                                </button>
                                <a href="/admin/students" class="btn btn-secondary">
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
                        <h6 class="mb-0">Current Photo</h6>
                    </div>
                    <div class="card-body text-center">
                        <?php if (!empty($student['profile_image'])): ?>
                            <img src="/uploads/students/<?php echo htmlspecialchars($student['profile_image']); ?>" class="img-fluid rounded mb-3" alt="Profile Photo" style="max-width: 200px;">
                        <?php else: ?>
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center mb-3" style="width: 200px; height: 200px; margin: 0 auto;">
                                <i class="fas fa-user text-white fa-3x"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>