<?php
// Extract data
$student = $student ?? [];
$exam_results = $exam_results ?? [];
?>

<div class="row">
    <!-- Results Summary -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Academic Performance</h5>
            </div>
            <div class="card-body text-center">
                <?php
                $totalExams = count($exam_results);
                $totalMarks = 0;
                $totalMaxMarks = 0;
                $passedExams = 0;

                foreach ($exam_results as $result) {
                    $totalMarks += $result['marks_obtained'] ?? 0;
                    $totalMaxMarks += $result['total_marks'] ?? 0;
                    if (($result['marks_obtained'] ?? 0) >= 35) {
                        $passedExams++;
                    }
                }

                $overallPercentage = $totalMaxMarks > 0 ? round(($totalMarks / $totalMaxMarks) * 100, 2) : 0;
                ?>

                <div class="mb-3">
                    <div class="display-4 text-primary"><?php echo $overallPercentage; ?>%</div>
                    <p class="text-muted mb-2">Overall Percentage</p>
                </div>

                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h6 text-success mb-0"><?php echo $passedExams; ?></div>
                            <small class="text-muted">Passed</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h6 text-info mb-0"><?php echo $totalExams; ?></div>
                            <small class="text-muted">Total Exams</small>
                        </div>
                    </div>
                </div>

                <div class="progress mb-2">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: <?php echo $overallPercentage; ?>%"
                         aria-valuenow="<?php echo $overallPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small class="text-muted">Academic Performance</small>
            </div>
        </div>

        <!-- Grade Distribution -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Grade Distribution</h6>
            </div>
            <div class="card-body">
                <?php
                $grades = ['A+' => 0, 'A' => 0, 'B+' => 0, 'B' => 0, 'C+' => 0, 'C' => 0, 'D' => 0, 'F' => 0];

                foreach ($exam_results as $result) {
                    $marks = $result['marks_obtained'] ?? 0;
                    $total = $result['total_marks'] ?? 0;
                    $percentage = $total > 0 ? ($marks / $total) * 100 : 0;

                    if ($percentage >= 90) $grade = 'A+';
                    elseif ($percentage >= 80) $grade = 'A';
                    elseif ($percentage >= 70) $grade = 'B+';
                    elseif ($percentage >= 60) $grade = 'B';
                    elseif ($percentage >= 50) $grade = 'C+';
                    elseif ($percentage >= 40) $grade = 'C';
                    elseif ($percentage >= 35) $grade = 'D';
                    else $grade = 'F';

                    $grades[$grade]++;
                }
                ?>

                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>A+</span>
                        <span><?php echo $grades['A+']; ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $totalExams > 0 ? ($grades['A+'] / $totalExams) * 100 : 0; ?>%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>A</span>
                        <span><?php echo $grades['A']; ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $totalExams > 0 ? ($grades['A'] / $totalExams) * 100 : 0; ?>%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>B+</span>
                        <span><?php echo $grades['B+']; ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: <?php echo $totalExams > 0 ? ($grades['B+'] / $totalExams) * 100 : 0; ?>%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>B</span>
                        <span><?php echo $grades['B']; ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: <?php echo $totalExams > 0 ? ($grades['B'] / $totalExams) * 100 : 0; ?>%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>C+</span>
                        <span><?php echo $grades['C+']; ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: <?php echo $totalExams > 0 ? ($grades['C+'] / $totalExams) * 100 : 0; ?>%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>F</span>
                        <span><?php echo $grades['F']; ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: <?php echo $totalExams > 0 ? ($grades['F'] / $totalExams) * 100 : 0; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Results -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Exam Results</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($exam_results)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Exam Name</th>
                                    <th>Subject</th>
                                    <th>Marks Obtained</th>
                                    <th>Total Marks</th>
                                    <th>Percentage</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exam_results as $result): ?>
                                    <?php
                                    $marks = $result['marks_obtained'] ?? 0;
                                    $total = $result['total_marks'] ?? 0;
                                    $percentage = $total > 0 ? round(($marks / $total) * 100, 2) : 0;
                                    $isPass = $marks >= 35;
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($result['exam_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($result['subject_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo $marks; ?></td>
                                        <td><?php echo $total; ?></td>
                                        <td><?php echo $percentage; ?>%</td>
                                        <td>
                                            <span class="badge bg-<?php
                                                echo $percentage >= 90 ? 'success' :
                                                     ($percentage >= 80 ? 'success' :
                                                     ($percentage >= 70 ? 'info' :
                                                     ($percentage >= 60 ? 'info' :
                                                     ($percentage >= 50 ? 'warning' :
                                                     ($percentage >= 35 ? 'warning' : 'danger')))));
                                            ?>">
                                                <?php echo htmlspecialchars($result['grade'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $isPass ? 'success' : 'danger'; ?>">
                                                <?php echo $isPass ? 'Pass' : 'Fail'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo isset($result['exam_date']) ? date('d M Y', strtotime($result['exam_date'])) : 'N/A'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Results Summary -->
                    <div class="mt-4">
                        <h6>Summary</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="h5 text-primary"><?php echo $totalExams; ?></div>
                                        <small>Total Exams</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="h5 text-success"><?php echo $passedExams; ?></div>
                                        <small>Passed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="h5 text-danger"><?php echo $totalExams - $passedExams; ?></div>
                                        <small>Failed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <div class="h5 text-info"><?php echo $overallPercentage; ?>%</div>
                                        <small>Average %</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                        <h4>No Exam Results Available</h4>
                        <p class="text-muted">Your exam results will appear here once they are published by the administration.</p>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Results are typically published within 1-2 weeks after the exam date.
                            </small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Performance Chart Placeholder -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Performance Trend</h5>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h6>Performance Chart</h6>
                    <p class="text-muted small">Interactive performance chart will be available with more exam results.</p>
                    <small class="text-muted">Minimum 3 exam results required for trend analysis</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Download Results Modal -->
<?php if (!empty($exam_results)): ?>
<div class="modal fade" id="downloadResultsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Download your exam results in the following formats:</p>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="downloadResults('pdf')">
                        <i class="fas fa-file-pdf me-2"></i>Download as PDF
                    </button>
                    <button class="btn btn-success" onclick="downloadResults('excel')">
                        <i class="fas fa-file-excel me-2"></i>Download as Excel
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="mt-3 text-end">
    <button type="button" class="btn btn-primary" onclick="showDownloadModal()">
        <i class="fas fa-download me-1"></i>Download Results
    </button>
</div>
<?php endif; ?>

<script>
function showDownloadModal() {
    new bootstrap.Modal(document.getElementById('downloadResultsModal')).show();
}

function downloadResults(format) {
    // In a real implementation, this would call an API endpoint to generate and download the file
    alert(`Downloading results as ${format.toUpperCase()}... (This feature will be implemented soon)`);
    bootstrap.Modal.getInstance(document.getElementById('downloadResultsModal')).hide();
}
</script>