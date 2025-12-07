<?php
// Extract data
$classes = $classes ?? [];
$subjects = $subjects ?? [];
?>

<div class="row">
    <!-- Classes Section -->
    <div class="col-xl-7 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Classes</h5>
                <a href="/admin/classes/create" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Class
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($classes)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-school fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No classes found</h6>
                        <p class="text-muted small">Start by creating your first class.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Class</th>
                                    <th>Academic Year</th>
                                    <th>Class Teacher</th>
                                    <th>Students</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classes as $class): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($class['name'] . ' ' . $class['section']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($class['academic_year']); ?></td>
                                        <td>
                                            <?php if ($class['teacher_username']): ?>
                                                <?php echo htmlspecialchars($class['teacher_username']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $class['student_count'] ?? 0; ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/classes/<?php echo $class['id']; ?>" class="btn btn-sm btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/admin/classes/<?php echo $class['id']; ?>/edit" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                        onclick="deleteClass(<?php echo $class['id']; ?>, '<?php echo htmlspecialchars($class['name'] . ' ' . $class['section']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Subjects Section -->
    <div class="col-xl-5 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Subjects</h5>
                <a href="/admin/subjects/create" class="btn btn-sm btn-secondary">
                    <i class="fas fa-plus me-1"></i>Add Subject
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($subjects)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No subjects found</h6>
                        <p class="text-muted small">Create subjects to assign to classes.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($subjects as $subject): ?>
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($subject['name']); ?></strong>
                                    <br>
                                    <small class="text-muted">Code: <?php echo htmlspecialchars($subject['code']); ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary mb-1"><?php echo $subject['class_count']; ?> classes</span>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info" title="Assign to Class"
                                                onclick="showAssignModal(<?php echo $subject['id']; ?>, '<?php echo htmlspecialchars($subject['name']); ?>')">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Assign Subject to Class Modal -->
<div class="modal fade" id="assignSubjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Subject to Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignSubjectForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
                <input type="hidden" name="subject_id" id="modalSubjectId">
                <div class="modal-body">
                    <p>Assign <strong id="subjectName"></strong> to:</p>
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Select Class</label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">Choose a class...</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['id']; ?>">
                                    <?php echo htmlspecialchars($class['name'] . ' ' . $class['section'] . ' (' . $class['academic_year'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteClass(classId, className) {
    if (confirm(`Are you sure you want to delete "${className}"? This action cannot be undone.`)) {
        fetch(`/admin/classes/${classId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the class.');
        });
    }
}

function showAssignModal(subjectId, subjectName) {
    document.getElementById('modalSubjectId').value = subjectId;
    document.getElementById('subjectName').textContent = subjectName;
    new bootstrap.Modal(document.getElementById('assignSubjectModal')).show();
}

document.getElementById('assignSubjectForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('/admin/subjects/assign', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('assignSubjectModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the subject.');
    });
});
</script>