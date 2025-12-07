<?php
// Extract data
$exams = $exams ?? [];
$classes = $classes ?? [];
?>

<div class="row">
    <div class="col-12">
        <!-- Exams Overview -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Exams
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo count($exams); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Upcoming Exams
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $upcoming = array_filter($exams, function($exam) {
                                        return strtotime($exam['start_date']) >= time();
                                    });
                                    echo count($upcoming);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Results Entered
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $withResults = array_filter($exams, function($exam) {
                                        return $exam['results_count'] > 0;
                                    });
                                    echo count($withResults);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    This Month
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php
                                    $thisMonth = array_filter($exams, function($exam) {
                                        return date('Y-m', strtotime($exam['start_date'])) === date('Y-m');
                                    });
                                    echo count($thisMonth);
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exams Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Exams</h5>
            </div>
            <div class="card-body">
                <?php if (empty($exams)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No exams found</h5>
                        <p class="text-muted">Start by creating your first exam.</p>
                        <a href="/admin/exams/create" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create First Exam
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Exam Name</th>
                                    <th>Type</th>
                                    <th>Class</th>
                                    <th>Duration</th>
                                    <th>Results</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exams as $exam): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($exam['name']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($exam['type']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars(($exam['class_name'] ?? 'N/A') . ' ' . ($exam['class_section'] ?? '')); ?></td>
                                        <td>
                                            <?php echo date('d M Y', strtotime($exam['start_date'])); ?> -
                                            <?php echo date('d M Y', strtotime($exam['end_date'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $exam['results_count'] > 0 ? 'success' : 'warning'; ?>">
                                                <?php echo $exam['results_count'] > 0 ? $exam['results_count'] . ' entered' : 'Pending'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $today = date('Y-m-d');
                                            if (strtotime($exam['end_date']) < strtotime($today)) {
                                                echo '<span class="badge bg-secondary">Completed</span>';
                                            } elseif (strtotime($exam['start_date']) <= strtotime($today) && strtotime($exam['end_date']) >= strtotime($today)) {
                                                echo '<span class="badge bg-success">Ongoing</span>';
                                            } else {
                                                echo '<span class="badge bg-info">Upcoming</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/exams/<?php echo $exam['id']; ?>"
                                                   class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="/admin/exams/<?php echo $exam['id']; ?>/results"
                                                   class="btn btn-sm btn-outline-success" title="Enter Results">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                        onclick="deleteExam(<?php echo $exam['id']; ?>, '<?php echo htmlspecialchars($exam['name']); ?>')">
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
</div>

<script>
function deleteExam(examId, examName) {
    if (confirm(`Are you sure you want to delete "${examName}"? This action cannot be undone and will delete all associated results.`)) {
        fetch(`/admin/exams/${examId}`, {
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
            alert('An error occurred while deleting the exam.');
        });
    }
}
</script>