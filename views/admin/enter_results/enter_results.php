<?php
// Admin Enter Results Page
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Enter Results - <?php echo htmlspecialchars($exam['exam_name']); ?></h4>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success" onclick="saveAllResults()">
                            <i class="fas fa-save"></i> Save All Results
                        </button>
                        <a href="/admin/exams" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Exams
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Roll No</th>
                                    <?php foreach ($exam['subjects'] as $subject): ?>
                                    <th><?php echo htmlspecialchars($subject['subject_name']); ?> (<?php echo htmlspecialchars($subject['subject_code']); ?>)</th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <?php foreach ($exam['subjects'] as $subject): ?>
                                    <td>
                                        <div class="form-group mb-0">
                                            <input type="number" class="form-control form-control-sm marks-input"
                                                   id="marks_<?php echo $student['id']; ?>_<?php echo $subject['id']; ?>"
                                                   name="marks[<?php echo $student['id']; ?>][<?php echo $subject['id']; ?>]"
                                                   placeholder="Marks" min="0" max="100" step="0.5">
                                            <input type="text" class="form-control form-control-sm grade-input mt-1"
                                                   id="grade_<?php echo $student['id']; ?>_<?php echo $subject['id']; ?>"
                                                   name="grade[<?php echo $student['id']; ?>][<?php echo $subject['id']; ?>]"
                                                   placeholder="Grade">
                                            <textarea class="form-control form-control-sm remarks-input mt-1"
                                                      id="remarks_<?php echo $student['id']; ?>_<?php echo $subject['id']; ?>"
                                                      name="remarks[<?php echo $student['id']; ?>][<?php echo $subject['id']; ?>]"
                                                      placeholder="Remarks" rows="1"></textarea>
                                        </div>
                                    </td>
                                    <?php endforeach; ?>
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

<script>
function saveAllResults() {
    const formData = new FormData();
    formData.append('exam_id', '<?php echo $exam['id']; ?>');

    // Collect all marks, grades, and remarks
    document.querySelectorAll('.marks-input').forEach(input => {
        if (input.value) {
            const ids = input.id.split('_');
            const studentId = ids[1];
            const subjectId = ids[2];
            formData.append(`marks_${studentId}_${subjectId}`, input.value);
        }
    });

    document.querySelectorAll('.grade-input').forEach(input => {
        if (input.value) {
            const ids = input.id.split('_');
            const studentId = ids[1];
            const subjectId = ids[2];
            formData.append(`grade_${studentId}_${subjectId}`, input.value);
        }
    });

    document.querySelectorAll('.remarks-input').forEach(input => {
        if (input.value) {
            const ids = input.id.split('_');
            const studentId = ids[1];
            const subjectId = ids[2];
            formData.append(`remarks_${studentId}_${subjectId}`, input.value);
        }
    });

    fetch('/admin/save-results', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-Token': '<?php echo $csrf_token; ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success('Results saved successfully');
        } else {
            toastr.error('Failed to save results');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred while saving results');
    });
}
</script>