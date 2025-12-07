<?php
// Extract data
$student = $student ?? [];
?>

<div class="row">
    <!-- Student Photo and Basic Info -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Student Photo</h5>
            </div>
            <div class="card-body text-center">
                <?php if ($student['photo_path']): ?>
                    <img src="<?php echo htmlspecialchars($student['photo_path']); ?>"
                         alt="Student Photo" class="img-fluid rounded mb-3" style="max-width: 200px;">
                <?php else: ?>
                    <div class="bg-light rounded d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width: 200px; height: 200px;">
                        <i class="fas fa-user fa-4x text-muted"></i>
                    </div>
                <?php endif; ?>

                <h5 class="card-title"><?php echo htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . $student['last_name']); ?></h5>
                <p class="text-muted mb-2">Scholar No: <?php echo htmlspecialchars($student['scholar_number']); ?></p>
                <p class="text-muted mb-0">Admission No: <?php echo htmlspecialchars($student['admission_number']); ?></p>

                <div class="mt-3">
                    <span class="badge bg-<?php echo $student['is_active'] ? 'success' : 'danger'; ?>">
                        <?php echo $student['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/students/<?php echo $student['id']; ?>/edit" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit Student
                    </a>
                    <a href="/admin/attendance?student_id=<?php echo $student['id']; ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-calendar-check me-1"></i>View Attendance
                    </a>
                    <a href="/admin/fees?student_id=<?php echo $student['id']; ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-money-bill-wave me-1"></i>View Fees
                    </a>
                    <a href="/admin/exams?student_id=<?php echo $student['id']; ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-graduation-cap me-1"></i>View Results
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteStudent(<?php echo $student['id']; ?>)">
                        <i class="fas fa-trash me-1"></i>Delete Student
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Details -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Full Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . $student['last_name']); ?></p>
                        <p><strong>Scholar Number:</strong> <?php echo htmlspecialchars($student['scholar_number']); ?></p>
                        <p><strong>Admission Number:</strong> <?php echo htmlspecialchars($student['admission_number']); ?></p>
                        <p><strong>Admission Date:</strong> <?php echo date('d M Y', strtotime($student['admission_date'])); ?></p>
                        <p><strong>Date of Birth:</strong> <?php echo date('d M Y', strtotime($student['date_of_birth'])); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($student['gender']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Class:</strong> <?php echo htmlspecialchars($student['class_name'] . ' ' . $student['class_section']); ?></p>
                        <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($student['blood_group'] ?: 'Not specified'); ?></p>
                        <p><strong>Caste/Category:</strong> <?php echo htmlspecialchars($student['caste_category'] ?: 'Not specified'); ?></p>
                        <p><strong>Religion:</strong> <?php echo htmlspecialchars($student['religion'] ?: 'Not specified'); ?></p>
                        <p><strong>Nationality:</strong> <?php echo htmlspecialchars($student['nationality']); ?></p>
                        <p><strong>Medical Conditions:</strong> <?php echo htmlspecialchars($student['medical_conditions'] ?: 'None'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Contact Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Mobile Number:</strong> <?php echo htmlspecialchars($student['mobile_number'] ?: 'Not provided'); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email'] ?: 'Not provided'); ?></p>
                        <p><strong>Aadhar Number:</strong> <?php echo htmlspecialchars($student['aadhar_number'] ?: 'Not provided'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Samagra Number:</strong> <?php echo htmlspecialchars($student['samagra_number'] ?: 'Not provided'); ?></p>
                        <p><strong>Aapaar ID:</strong> <?php echo htmlspecialchars($student['apaar_id'] ?: 'Not provided'); ?></p>
                        <p><strong>PAN Number:</strong> <?php echo htmlspecialchars($student['pan_number'] ?: 'Not provided'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Address Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Village/Town Address</h6>
                        <p><?php echo nl2br(htmlspecialchars($student['village_address'] ?: 'Not provided')); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Permanent Address</h6>
                        <p><?php echo nl2br(htmlspecialchars($student['permanent_address'] ?: 'Not provided')); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Family Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Father's Name:</strong> <?php echo htmlspecialchars($student['father_name'] ?: 'Not provided'); ?></p>
                        <p><strong>Mother's Name:</strong> <?php echo htmlspecialchars($student['mother_name'] ?: 'Not provided'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Guardian's Name:</strong> <?php echo htmlspecialchars($student['guardian_name'] ?: 'Not provided'); ?></p>
                        <p><strong>Guardian's Contact:</strong> <?php echo htmlspecialchars($student['guardian_contact'] ?: 'Not provided'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Academic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Previous School:</strong> <?php echo htmlspecialchars($student['previous_school'] ?: 'Not provided'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Academic Year:</strong> <?php echo htmlspecialchars($student['academic_year'] ?? date('Y') . '-' . (date('Y') + 1)); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></strong>?</p>
                <p class="text-danger small">This action cannot be undone and will remove all associated records.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
function deleteStudent(studentId) {
    const studentName = '<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>';
    document.querySelector('.modal-body p strong').textContent = studentName;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    fetch(`/admin/students/${<?php echo $student['id']; ?>}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-Token': '<?php echo $csrf_token ?? ''; ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/admin/students';
        } else {
            alert('Error: ' + (data.message || 'Failed to delete student'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the student');
    });
});
</script>