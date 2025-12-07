<?php
// Extract data
$student = $student ?? [];
$csrf_token = $csrf_token ?? '';
$errors = $session->getFlash('errors') ?? [];
$old_input = $session->getFlash('old_input') ?? [];
?>

<div class="row">
    <!-- Profile Photo and Basic Info -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Profile Photo</h5>
            </div>
            <div class="card-body text-center">
                <?php if ($student['photo_path']): ?>
                    <img src="<?php echo htmlspecialchars($student['photo_path']); ?>"
                         alt="Profile Photo" class="img-fluid rounded mb-3" style="max-width: 200px;">
                <?php else: ?>
                    <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 200px; height: 200px;">
                        <i class="fas fa-user fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>

                <h5 class="card-title"><?php echo htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . $student['last_name']); ?></h5>
                <p class="text-muted mb-2">Scholar No: <?php echo htmlspecialchars($student['scholar_number']); ?></p>
                <p class="text-muted mb-0">Class: <?php echo htmlspecialchars($student['class_name'] . ' ' . $student['class_section']); ?></p>

                <div class="mt-3">
                    <span class="badge bg-<?php echo $student['is_active'] ? 'success' : 'danger'; ?>">
                        <?php echo $student['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Account Security -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Account Security</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Last Login:</strong><br>
                    <small class="text-muted">Never</small>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="changePassword()">
                    <i class="fas fa-key me-1"></i>Change Password
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="col-lg-8">
        <form method="POST" action="/student/profile">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Scholar Number</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['scholar_number']); ?>" readonly>
                            <small class="text-muted">Cannot be changed</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Admission Number</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['admission_number']); ?>" readonly>
                            <small class="text-muted">Cannot be changed</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['first_name']); ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['middle_name'] ?: ''); ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['last_name']); ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" value="<?php echo htmlspecialchars($student['date_of_birth']); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['gender']); ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="mobile_number" class="form-label">Mobile Number</label>
                            <input type="tel" class="form-control <?php echo isset($errors['mobile_number']) ? 'is-invalid' : ''; ?>"
                                   id="mobile_number" name="mobile_number"
                                   value="<?php echo htmlspecialchars($old_input['mobile_number'] ?? $student['mobile_number']); ?>">
                            <?php if (isset($errors['mobile_number'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['mobile_number'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                                   id="email" name="email"
                                   value="<?php echo htmlspecialchars($old_input['email'] ?? $student['email']); ?>">
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['email'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Address Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="village_address" class="form-label">Village/Town Address</label>
                            <textarea class="form-control" id="village_address" name="village_address" rows="3"><?php echo htmlspecialchars($old_input['village_address'] ?? $student['village_address']); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Permanent Address</label>
                            <textarea class="form-control" rows="3" readonly><?php echo htmlspecialchars($student['permanent_address']); ?></textarea>
                            <small class="text-muted">Contact administrator to change permanent address</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Family Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Father's Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['father_name']); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['mother_name']); ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Guardian's Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['guardian_name']); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="guardian_contact" class="form-label">Guardian's Contact</label>
                            <input type="tel" class="form-control" id="guardian_contact" name="guardian_contact"
                                   value="<?php echo htmlspecialchars($old_input['guardian_contact'] ?? $student['guardian_contact']); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Academic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['class_name'] . ' ' . $student['class_section']); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['academic_year'] ?? date('Y') . '-' . (date('Y') + 1)); ?>" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Previous School</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['previous_school'] ?: 'Not specified'); ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Medical Conditions</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['medical_conditions'] ?: 'None'); ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="/student/dashboard" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Profile
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="changePasswordForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                        <small class="text-muted">Password must be at least 8 characters long</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function changePassword() {
    new bootstrap.Modal(document.getElementById('changePasswordModal')).show();
}

document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (newPassword !== confirmPassword) {
        alert('New passwords do not match!');
        return;
    }

    if (newPassword.length < 8) {
        alert('Password must be at least 8 characters long!');
        return;
    }

    const formData = new FormData(this);

    fetch('/student/change-password', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Password changed successfully!');
            bootstrap.Modal.getInstance(document.getElementById('changePasswordModal')).hide();
            document.getElementById('changePasswordForm').reset();
        } else {
            alert('Error: ' + (data.message || 'Failed to change password'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while changing the password');
    });
});
</script>