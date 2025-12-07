<?php
// Extract data
$class = $class ?? [];
$subjects = $subjects ?? [];
$student_count = $student_count ?? 0;
?>

<div class="row">
    <!-- Class Information -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Class Information</h5>
            </div>
            <div class="card-body text-center">
                <div class="display-4 text-primary mb-2">
                    <?php echo htmlspecialchars($class['name'] . ' ' . $class['section']); ?>
                </div>
                <h5 class="card-title"><?php echo htmlspecialchars($class['name'] . $class['section']); ?> Class</h5>
                <p class="text-muted mb-2">Academic Year: <?php echo htmlspecialchars($class['academic_year']); ?></p>

                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h5 mb-0"><?php echo $student_count; ?></div>
                            <small class="text-muted">Students</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h5 mb-0"><?php echo count($subjects); ?></div>
                            <small class="text-muted">Subjects</small>
                        </div>
                    </div>
                </div>

                <?php if ($class['class_teacher_name']): ?>
                    <div class="mb-3">
                        <strong>Class Teacher:</strong><br>
                        <?php echo htmlspecialchars($class['class_teacher_name']); ?>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <span class="badge bg-warning">No Class Teacher Assigned</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/classes/<?php echo $class['id']; ?>/edit" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit Class
                    </a>
                    <a href="/admin/attendance?class_id=<?php echo $class['id']; ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-calendar-check me-1"></i>Mark Attendance
                    </a>
                    <a href="/admin/exams?class_id=<?php echo $class['id']; ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-graduation-cap me-1"></i>View Exams
                    </a>
                    <a href="/admin/fees?class_id=<?php echo $class['id']; ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-money-bill-wave me-1"></i>View Fees
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteClass(<?php echo $class['id']; ?>)">
                        <i class="fas fa-trash me-1"></i>Delete Class
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Details -->
    <div class="col-lg-8">
        <!-- Subjects -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Subjects</h5>
                <button type="button" class="btn btn-primary btn-sm" onclick="addSubjectModal()">
                    <i class="fas fa-plus me-1"></i>Add Subject
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($subjects)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Subject Code</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subjects as $subject): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($subject['name']); ?></td>
                                        <td><code><?php echo htmlspecialchars($subject['code']); ?></code></td>
                                        <td><?php echo htmlspecialchars($subject['description'] ?: 'No description'); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    onclick="removeSubject(<?php echo $subject['id']; ?>, '<?php echo htmlspecialchars($subject['name']); ?>')">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <h5>No Subjects Assigned</h5>
                        <p class="text-muted">Add subjects to this class to start managing curriculum.</p>
                        <button type="button" class="btn btn-primary" onclick="addSubjectModal()">
                            <i class="fas fa-plus me-1"></i>Add First Subject
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Class Statistics -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Class Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-primary mb-1"><?php echo $student_count; ?></div>
                            <small class="text-muted">Total Students</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-success mb-1"><?php echo count($subjects); ?></div>
                            <small class="text-muted">Subjects</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-info mb-1">0</div>
                            <small class="text-muted">Exams This Year</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-warning mb-1">0%</div>
                            <small class="text-muted">Avg Attendance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <div class="timeline-title">Class Created</div>
                            <div class="timeline-text">
                                Class <?php echo htmlspecialchars($class['name'] . ' ' . $class['section']); ?> was created for academic year <?php echo htmlspecialchars($class['academic_year']); ?>
                            </div>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($class['created_at'])); ?></small>
                        </div>
                    </div>

                    <?php if ($class['class_teacher_name']): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Class Teacher Assigned</div>
                                <div class="timeline-text">
                                    <?php echo htmlspecialchars($class['class_teacher_name']); ?> was assigned as class teacher
                                </div>
                                <small class="text-muted">Recently</small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($subjects)): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Subjects Added</div>
                                <div class="timeline-text">
                                    <?php echo count($subjects); ?> subject(s) have been assigned to this class
                                </div>
                                <small class="text-muted">Recently</small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Subject to Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSubjectForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
                <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Select Subject</label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">Choose a subject...</option>
                            <!-- Subjects will be loaded via AJAX -->
                        </select>
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

<!-- Delete Class Modal -->
<div class="modal fade" id="deleteClassModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($class['name'] . ' ' . $class['section']); ?></strong>?</p>
                <p class="text-danger small">This action cannot be undone and will remove all associated data including student enrollments and exam records.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteClass">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
function addSubjectModal() {
    // Load available subjects
    fetch('/api/subjects')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('subject_id');
            select.innerHTML = '<option value="">Choose a subject...</option>';

            if (data.success && data.data) {
                data.data.forEach(subject => {
                    // Check if subject is already assigned
                    const isAssigned = <?php echo json_encode(array_column($subjects, 'id')); ?>.includes(subject.id);
                    if (!isAssigned) {
                        select.innerHTML += `<option value="${subject.id}">${subject.name} (${subject.code})</option>`;
                    }
                });
            }
        })
        .catch(error => console.error('Error loading subjects:', error));

    new bootstrap.Modal(document.getElementById('addSubjectModal')).show();
}

document.getElementById('addSubjectForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/admin/classes/assign-subject', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add subject'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the subject');
    });
});

function removeSubject(subjectId, subjectName) {
    if (confirm(`Are you sure you want to remove "${subjectName}" from this class?`)) {
        const formData = new FormData();
        formData.append('csrf_token', '<?php echo $csrf_token ?? ''; ?>');
        formData.append('subject_id', subjectId);
        formData.append('class_id', '<?php echo $class['id']; ?>');

        fetch('/admin/classes/remove-subject', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to remove subject'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the subject');
        });
    }
}

function deleteClass(classId) {
    new bootstrap.Modal(document.getElementById('deleteClassModal')).show();
}

document.getElementById('confirmDeleteClass').addEventListener('click', function() {
    fetch(`/admin/classes/${<?php echo $class['id']; ?>}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-Token': '<?php echo $csrf_token ?? ''; ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/admin/classes';
        } else {
            alert('Error: ' + (data.message || 'Failed to delete class'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the class');
    });
});
</script>