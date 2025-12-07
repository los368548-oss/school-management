<?php
// Extract data
$students = $students ?? [];
$classes = $classes ?? [];
$search = $search ?? '';
$selected_class = $selected_class ?? null;
?>

<div class="row">
    <!-- Filters -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Students</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="Name, Scholar Number...">
                    </div>
                    <div class="col-md-3">
                        <label for="class_id" class="form-label">Filter by Class</label>
                        <select class="form-select" id="class_id" name="class_id">
                            <option value="">All Classes</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class['id']; ?>"
                                        <?php echo $selected_class == $class['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($class['name'] . ' ' . $class['section']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="/admin/students" class="btn btn-secondary w-100">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Students (<?php echo $students['total'] ?? 0; ?> total)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($students['data'])): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No students found</h5>
                        <p class="text-muted">Start by adding your first student.</p>
                        <a href="/admin/students/create" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add First Student
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Photo</th>
                                    <th>Scholar No</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Gender</th>
                                    <th>Contact</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students['data'] as $student): ?>
                                    <tr>
                                        <td>
                                            <?php if ($student['photo_path']): ?>
                                                <img src="<?php echo htmlspecialchars($student['photo_path']); ?>"
                                                     alt="Photo" class="rounded-circle" width="40" height="40">
                                            <?php else: ?>
                                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['scholar_number']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($student['first_name'] . ' ' . ($student['middle_name'] ? $student['middle_name'] . ' ' : '') . $student['last_name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars(($student['class_name'] ?? '') . ' ' . ($student['class_section'] ?? '')); ?></td>
                                        <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                        <td>
                                            <?php if ($student['mobile_number']): ?>
                                                <i class="fas fa-phone text-muted me-1"></i><?php echo htmlspecialchars($student['mobile_number']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">No contact</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/students/<?php echo $student['id']; ?>"
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/admin/students/<?php echo $student['id']; ?>/edit"
                                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteStudent(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>')"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($students['last_page'] > 1): ?>
                        <nav aria-label="Students pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php if ($students['current_page'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $students['current_page'] - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $selected_class ? '&class_id=' . $selected_class : ''; ?>">
                                            Previous
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $students['current_page'] - 2); $i <= min($students['last_page'], $students['current_page'] + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $students['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $selected_class ? '&class_id=' . $selected_class : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($students['current_page'] < $students['last_page']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $students['current_page'] + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $selected_class ? '&class_id=' . $selected_class : ''; ?>">
                                            Next
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
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
                <p>Are you sure you want to delete <strong id="deleteStudentName"></strong>?</p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteStudentId = null;

function deleteStudent(id, name) {
    deleteStudentId = id;
    document.getElementById('deleteStudentName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (!deleteStudentId) return;

    fetch(`/admin/students/${deleteStudentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-Token': '<?php echo $csrf_token ?? ''; ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
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