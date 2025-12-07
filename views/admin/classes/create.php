<?php
// Extract data
$csrf_token = $csrf_token ?? '';
$available_teachers = $available_teachers ?? [];
$current_year = $current_year ?? date('Y') . '-' . (date('Y') + 1);
$errors = $session->getFlash('errors') ?? [];
$old_input = $session->getFlash('old_input') ?? [];
?>

<form method="POST" action="/admin/classes">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Class Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Class Name *</label>
                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>"
                                   id="name" name="name"
                                   value="<?php echo htmlspecialchars($old_input['name'] ?? ''); ?>"
                                   placeholder="e.g., 10, 11, 12" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['name'][0]; ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Enter the class number (e.g., 1, 2, 3, ..., 12)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="section" class="form-label">Section *</label>
                            <select class="form-select <?php echo isset($errors['section']) ? 'is-invalid' : ''; ?>"
                                    id="section" name="section" required>
                                <option value="">Select Section</option>
                                <option value="A" <?php echo ($old_input['section'] ?? '') === 'A' ? 'selected' : ''; ?>>A</option>
                                <option value="B" <?php echo ($old_input['section'] ?? '') === 'B' ? 'selected' : ''; ?>>B</option>
                                <option value="C" <?php echo ($old_input['section'] ?? '') === 'C' ? 'selected' : ''; ?>>C</option>
                                <option value="D" <?php echo ($old_input['section'] ?? '') === 'D' ? 'selected' : ''; ?>>D</option>
                            </select>
                            <?php if (isset($errors['section'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['section'][0]; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="academic_year" class="form-label">Academic Year *</label>
                            <input type="text" class="form-control <?php echo isset($errors['academic_year']) ? 'is-invalid' : ''; ?>"
                                   id="academic_year" name="academic_year"
                                   value="<?php echo htmlspecialchars($old_input['academic_year'] ?? $current_year); ?>" required>
                            <?php if (isset($errors['academic_year'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['academic_year'][0]; ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Format: YYYY-YYYY (e.g., 2024-2025)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="class_teacher_id" class="form-label">Class Teacher</label>
                            <select class="form-select" id="class_teacher_id" name="class_teacher_id">
                                <option value="">Select Class Teacher (Optional)</option>
                                <?php foreach ($available_teachers as $teacher): ?>
                                    <option value="<?php echo $teacher['id']; ?>"
                                            <?php echo ($old_input['class_teacher_id'] ?? '') == $teacher['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($teacher['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Assign a teacher to be the class teacher</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Class Preview</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="display-4 text-primary mb-2" id="classPreview">
                            <?php echo htmlspecialchars($old_input['name'] ?? '10') . ' ' . htmlspecialchars($old_input['section'] ?? 'A'); ?>
                        </div>
                        <small class="text-muted">Class Name & Section</small>
                    </div>
                    <hr>
                    <div class="small">
                        <p><strong>Academic Year:</strong> <span id="yearPreview"><?php echo htmlspecialchars($old_input['academic_year'] ?? $current_year); ?></span></p>
                        <p><strong>Class Teacher:</strong> <span id="teacherPreview">
                            <?php
                            if (!empty($old_input['class_teacher_id'])) {
                                foreach ($available_teachers as $teacher) {
                                    if ($teacher['id'] == $old_input['class_teacher_id']) {
                                        echo htmlspecialchars($teacher['username']);
                                        break;
                                    }
                                }
                            } else {
                                echo 'Not assigned';
                            }
                            ?>
                        </span></p>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Information</h6>
                </div>
                <div class="card-body">
                    <h6>Creating a New Class</h6>
                    <ul class="small mb-0">
                        <li>Class name should be numeric (1-12)</li>
                        <li>Each class can have multiple sections (A, B, C, D)</li>
                        <li>Academic year format: YYYY-YYYY</li>
                        <li>Class teacher assignment is optional</li>
                        <li>You can add subjects to the class after creation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="/admin/classes" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Classes
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Create Class
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Update preview in real-time
document.getElementById('name').addEventListener('input', updatePreview);
document.getElementById('section').addEventListener('change', updatePreview);
document.getElementById('academic_year').addEventListener('input', updatePreview);
document.getElementById('class_teacher_id').addEventListener('change', updateTeacherPreview);

function updatePreview() {
    const name = document.getElementById('name').value || '10';
    const section = document.getElementById('section').value || 'A';
    document.getElementById('classPreview').textContent = name + ' ' + section;
}

function updateTeacherPreview() {
    const teacherId = document.getElementById('class_teacher_id').value;
    const teacherSelect = document.getElementById('class_teacher_id');
    const selectedOption = teacherSelect.options[teacherSelect.selectedIndex];
    const teacherName = selectedOption ? selectedOption.text : 'Not assigned';

    document.getElementById('teacherPreview').textContent = teacherName;
}

// Update year preview
document.getElementById('academic_year').addEventListener('input', function() {
    const year = this.value || '<?php echo $current_year; ?>';
    document.getElementById('yearPreview').textContent = year;
});
</script>