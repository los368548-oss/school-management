<?php
// Admin Attendance Report Page
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Attendance Report - <?php echo htmlspecialchars($class['class_name'] . ' ' . $class['section']); ?></h4>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" onclick="exportReport()">
                            <i class="fas fa-download"></i> Export PDF
                        </button>
                        <a href="/admin/attendance" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Attendance
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Class:</strong> <?php echo htmlspecialchars($class['class_name'] . ' ' . $class['section']); ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Period:</strong> <?php echo htmlspecialchars($start_date); ?> to <?php echo htmlspecialchars($end_date); ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Students:</strong> <?php echo count($report['students']); ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Working Days:</strong> <?php echo $report['total_working_days']; ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Roll No</th>
                                    <th>Student Name</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Total Days</th>
                                    <th>Attendance %</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['students'] as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td><?php echo $student['present_days']; ?></td>
                                    <td><?php echo $student['absent_days']; ?></td>
                                    <td><?php echo $student['late_days']; ?></td>
                                    <td><?php echo $student['total_days']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $student['attendance_percentage'] >= 75 ? 'success' : ($student['attendance_percentage'] >= 60 ? 'warning' : 'danger'); ?>">
                                            <?php echo number_format($student['attendance_percentage'], 1); ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($student['attendance_percentage'] >= 75): ?>
                                            <span class="text-success">Good</span>
                                        <?php elseif ($student['attendance_percentage'] >= 60): ?>
                                            <span class="text-warning">Average</span>
                                        <?php else: ?>
                                            <span class="text-danger">Poor</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Class Summary</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="attendanceChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Attendance Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-success"><?php echo $report['class_stats']['present_percentage']; ?>%</h4>
                                                <small class="text-muted">Average Attendance</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-info"><?php echo $report['class_stats']['total_present']; ?></h4>
                                                <small class="text-muted">Total Present Days</small>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-danger"><?php echo $report['class_stats']['total_absent']; ?></h4>
                                                <small class="text-muted">Total Absent Days</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h4 class="text-warning"><?php echo $report['class_stats']['total_late']; ?></h4>
                                                <small class="text-muted">Total Late Days</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function exportReport() {
    window.print();
}

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const data = <?php echo json_encode($report['chart_data']); ?>;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent', 'Late'],
            datasets: [{
                data: [data.present, data.absent, data.late],
                backgroundColor: [
                    '#28a745',
                    '#dc3545',
                    '#ffc107'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
});
</script>