<?php
// Extract data
$student = $student ?? [];
$attendance_stats = $attendance_stats ?? [];
$recent_results = $recent_results ?? [];
$fee_status = $fee_status ?? [];
$upcoming_events = $upcoming_events ?? [];
$recent_attendance = $recent_attendance ?? [];
?>

<div class="row">
    <!-- Welcome Section -->
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="card-title mb-1">Welcome back, <?php echo htmlspecialchars($student['first_name'] ?? 'Student'); ?>!</h4>
                        <p class="card-text mb-0">Here's your academic overview for today.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="fs-1">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Attendance Overview -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Attendance Overview</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="display-4 text-primary"><?php echo $attendance_stats['percentage'] ?? 0; ?>%</div>
                    <p class="text-muted mb-2">This Month</p>
                </div>

                <div class="row text-center">
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="h6 text-success mb-0"><?php echo $attendance_stats['present_days'] ?? 0; ?></div>
                            <small class="text-muted">Present</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="h6 text-danger mb-0"><?php echo $attendance_stats['absent_days'] ?? 0; ?></div>
                            <small class="text-muted">Absent</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="h6 text-warning mb-0"><?php echo $attendance_stats['late_days'] ?? 0; ?></div>
                            <small class="text-muted">Late</small>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="/student/attendance" class="btn btn-primary btn-sm">
                        <i class="fas fa-calendar-check me-1"></i>View Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Results -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Results</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_results)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($recent_results, 0, 3) as $result): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($result['exam_name'] ?? 'Exam'); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($result['subject_name'] ?? ''); ?></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-<?php echo ($result['marks_obtained'] ?? 0) >= 35 ? 'success' : 'danger'; ?>">
                                        <?php echo $result['marks_obtained'] ?? 0; ?>/<?php echo $result['total_marks'] ?? 0; ?>
                                    </span>
                                    <small class="text-muted"><?php echo $result['grade'] ?? 'N/A'; ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="/student/results" class="btn btn-primary btn-sm">
                            <i class="fas fa-chart-bar me-1"></i>View All Results
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <h6>No Results Yet</h6>
                        <p class="text-muted small">Your exam results will appear here once available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Fee Status -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Fee Status</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <?php
                    $totalPending = $fee_status['total_pending'] ?? 0;
                    $statusColor = $totalPending > 0 ? 'danger' : 'success';
                    $statusText = $totalPending > 0 ? 'Pending' : 'Clear';
                    ?>
                    <div class="display-6 text-<?php echo $statusColor; ?>">
                        ₹<?php echo number_format($totalPending, 2); ?>
                    </div>
                    <p class="text-muted mb-2"><?php echo $statusText; ?></p>
                </div>

                <?php if ($totalPending > 0): ?>
                    <div class="alert alert-warning py-2">
                        <small>You have outstanding fees that need to be paid.</small>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success py-2">
                        <small>All fees are paid. Great job!</small>
                    </div>
                <?php endif; ?>

                <div class="mt-3">
                    <a href="/student/fees" class="btn btn-primary btn-sm">
                        <i class="fas fa-money-bill-wave me-1"></i>View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Attendance -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Attendance</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_attendance)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($recent_attendance, 0, 7) as $attendance): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($attendance['date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                echo $attendance['status'] === 'Present' ? 'success' :
                                                     ($attendance['status'] === 'Absent' ? 'danger' : 'warning');
                                            ?>">
                                                <?php echo $attendance['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <h6>No Attendance Records</h6>
                        <p class="text-muted small">Attendance records will appear here once marked.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Upcoming Events</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($upcoming_events)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($upcoming_events, 0, 4) as $event): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo date('M d', strtotime($event['event_date'])); ?>
                                    </small>
                                </div>
                                <p class="mb-1 small text-muted">
                                    <?php echo htmlspecialchars(substr($event['description'] ?? '', 0, 80)); ?>
                                    <?php if (strlen($event['description'] ?? '') > 80): ?>...<?php endif; ?>
                                </p>
                                <?php if ($event['location']): ?>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($event['location']); ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3 text-center">
                        <a href="/student/events" class="btn btn-primary btn-sm">
                            <i class="fas fa-calendar-alt me-1"></i>View All Events
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <h6>No Upcoming Events</h6>
                        <p class="text-muted small">Check back later for upcoming school events.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="/student/profile" class="text-decoration-none">
                            <div class="card h-100 border-primary">
                                <div class="card-body">
                                    <i class="fas fa-user fa-2x text-primary mb-2"></i>
                                    <h6 class="card-title">My Profile</h6>
                                    <p class="card-text small text-muted">View and update your profile information</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/student/attendance" class="text-decoration-none">
                            <div class="card h-100 border-success">
                                <div class="card-body">
                                    <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                                    <h6 class="card-title">Attendance</h6>
                                    <p class="card-text small text-muted">Check your attendance records</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/student/results" class="text-decoration-none">
                            <div class="card h-100 border-info">
                                <div class="card-body">
                                    <i class="fas fa-chart-bar fa-2x text-info mb-2"></i>
                                    <h6 class="card-title">Results</h6>
                                    <p class="card-text small text-muted">View your exam results</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="/student/fees" class="text-decoration-none">
                            <div class="card h-100 border-warning">
                                <div class="card-body">
                                    <i class="fas fa-money-bill-wave fa-2x text-warning mb-2"></i>
                                    <h6 class="card-title">Fees</h6>
                                    <p class="card-text small text-muted">Check fee status and payments</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>