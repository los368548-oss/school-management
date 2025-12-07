<?php
// Extract data
$student = $student ?? [];
$attendance_calendar = $attendance_calendar ?? [];
$attendance_stats = $attendance_stats ?? [];
$month = $month ?? date('m');
$year = $year ?? date('Y');
?>

<div class="row">
    <!-- Attendance Summary -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Attendance Summary</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="display-4 text-primary"><?php echo $attendance_stats['percentage'] ?? 0; ?>%</div>
                    <p class="text-muted mb-2">This Month</p>
                </div>

                <div class="row text-center mb-3">
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

                <div class="mb-3">
                    <strong>Total Working Days:</strong> <?php echo $attendance_stats['total_days'] ?? 0; ?>
                </div>

                <div class="progress mb-2">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: <?php echo $attendance_stats['percentage'] ?? 0; ?>%"
                         aria-valuenow="<?php echo $attendance_stats['percentage'] ?? 0; ?>" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small class="text-muted">Monthly Attendance Rate</small>
            </div>
        </div>

        <!-- Month/Year Selector -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Select Period</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="/student/attendance">
                    <div class="mb-3">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-select" id="month" name="month">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"
                                        <?php echo $month == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : ''; ?>>
                                    <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-select" id="year" name="year">
                            <?php
                            $currentYear = date('Y');
                            for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++):
                            ?>
                                <option value="<?php echo $i; ?>" <?php echo $year == $i ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>View Attendance
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance Calendar -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Attendance Calendar - <?php echo date('F Y', strtotime("$year-$month-01")); ?></h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="previousMonth()">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="nextMonth()">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php
                $firstDay = date('N', strtotime("$year-$month-01"));
                $daysInMonth = date('t', strtotime("$year-$month-01"));
                $today = date('Y-m-d');
                $currentMonth = date('m');
                $currentYear = date('Y');

                // Create attendance lookup array
                $attendanceLookup = [];
                foreach ($attendance_calendar as $record) {
                    $attendanceLookup[$record['date']] = $record['status'];
                }
                ?>

                <!-- Calendar Header -->
                <div class="calendar">
                    <div class="calendar-header d-flex">
                        <?php
                        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        foreach ($days as $day):
                        ?>
                            <div class="calendar-day-header text-center fw-bold py-2">
                                <?php echo $day; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Calendar Body -->
                    <div class="calendar-body">
                        <?php
                        $dayCount = 1;
                        $totalCells = 42; // 6 weeks * 7 days

                        for ($cell = 1; $cell <= $totalCells; $cell++):
                            $isCurrentMonth = ($cell >= $firstDay && $dayCount <= $daysInMonth);
                            $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $dayCount);
                            $isToday = ($currentDate === $today);
                            $isFuture = ($currentDate > $today);
                            $attendanceStatus = $attendanceLookup[$currentDate] ?? null;

                            $cellClasses = ['calendar-cell'];
                            if (!$isCurrentMonth) $cellClasses[] = 'calendar-cell-empty';
                            if ($isToday) $cellClasses[] = 'calendar-cell-today';
                            if ($isFuture) $cellClasses[] = 'calendar-cell-future';
                            if ($attendanceStatus) $cellClasses[] = 'calendar-cell-' . strtolower($attendanceStatus);
                        ?>
                            <div class="<?php echo implode(' ', $cellClasses); ?>">
                                <?php if ($isCurrentMonth): ?>
                                    <div class="calendar-date"><?php echo $dayCount; ?></div>
                                    <?php if ($attendanceStatus): ?>
                                        <div class="calendar-status">
                                            <span class="badge bg-<?php
                                                echo $attendanceStatus === 'Present' ? 'success' :
                                                     ($attendanceStatus === 'Absent' ? 'danger' : 'warning');
                                            ?> badge-sm">
                                                <?php echo substr($attendanceStatus, 0, 1); ?>
                                            </span>
                                        </div>
                                    <?php elseif (!$isFuture && $currentMonth == $month && $currentYear == $year): ?>
                                        <div class="calendar-status">
                                            <small class="text-muted">Not marked</small>
                                        </div>
                                    <?php endif; ?>
                                    <?php $dayCount++; ?>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-3">
                    <h6>Legend:</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2">P</span>
                            <small>Present</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger me-2">A</span>
                            <small>Absent</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-warning me-2">L</span>
                            <small>Late</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="calendar-cell-today me-2" style="width: 20px; height: 20px; border-radius: 50%;"></div>
                            <small>Today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Summary Table -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Monthly Summary</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $monthlyStats = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Not Marked' => 0];
                            for ($day = 1; $day <= $daysInMonth; $day++):
                                $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                                $dayName = date('l', strtotime($date));
                                $status = $attendanceLookup[$date] ?? 'Not Marked';
                                $isWeekend = in_array($dayName, ['Saturday', 'Sunday']);
                                $isFuture = $date > $today;

                                if ($status !== 'Not Marked') {
                                    $monthlyStats[$status]++;
                                } elseif (!$isFuture && !$isWeekend) {
                                    $monthlyStats['Not Marked']++;
                                }
                            ?>
                                <tr class="<?php echo $isWeekend ? 'table-light' : ''; ?>">
                                    <td><?php echo date('d M Y', strtotime($date)); ?></td>
                                    <td><?php echo $dayName; ?></td>
                                    <td>
                                        <?php if ($status !== 'Not Marked'): ?>
                                            <span class="badge bg-<?php
                                                echo $status === 'Present' ? 'success' :
                                                     ($status === 'Absent' ? 'danger' : 'warning');
                                            ?>">
                                                <?php echo $status; ?>
                                            </span>
                                        <?php elseif ($isFuture): ?>
                                            <span class="text-muted">-</span>
                                        <?php elseif ($isWeekend): ?>
                                            <span class="text-muted">Weekend</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Not Marked</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isWeekend): ?>
                                            <small class="text-muted">Holiday</small>
                                        <?php elseif ($isFuture): ?>
                                            <small class="text-muted">Future date</small>
                                        <?php elseif ($status === 'Not Marked'): ?>
                                            <small class="text-warning">Attendance not marked</small>
                                        <?php else: ?>
                                            <small class="text-success">Marked</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.calendar {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

.calendar-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.calendar-day-header {
    flex: 1;
    font-size: 0.875rem;
    color: #6c757d;
}

.calendar-body {
    display: flex;
    flex-wrap: wrap;
}

.calendar-cell {
    flex: 0 0 14.2857%;
    height: 80px;
    border-right: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    padding: 4px;
    position: relative;
}

.calendar-cell:nth-child(7n) {
    border-right: none;
}

.calendar-cell-empty {
    background-color: #f8f9fa;
}

.calendar-cell-today {
    background-color: #e3f2fd;
    border: 2px solid #2196f3;
}

.calendar-cell-future {
    background-color: #fafafa;
}

.calendar-cell-present {
    background-color: rgba(25, 135, 84, 0.1);
}

.calendar-cell-absent {
    background-color: rgba(220, 53, 69, 0.1);
}

.calendar-cell-late {
    background-color: rgba(255, 193, 7, 0.1);
}

.calendar-date {
    font-weight: 600;
    margin-bottom: 2px;
}

.calendar-status {
    font-size: 0.75rem;
}
</style>

<script>
function previousMonth() {
    const currentMonth = <?php echo (int)$month; ?>;
    const currentYear = <?php echo (int)$year; ?>;

    let newMonth = currentMonth - 1;
    let newYear = currentYear;

    if (newMonth < 1) {
        newMonth = 12;
        newYear = currentYear - 1;
    }

    window.location.href = `/student/attendance?month=${String(newMonth).padStart(2, '0')}&year=${newYear}`;
}

function nextMonth() {
    const currentMonth = <?php echo (int)$month; ?>;
    const currentYear = <?php echo (int)$year; ?>;

    let newMonth = currentMonth + 1;
    let newYear = currentYear;

    if (newMonth > 12) {
        newMonth = 1;
        newYear = currentYear + 1;
    }

    window.location.href = `/student/attendance?month=${String(newMonth).padStart(2, '0')}&year=${newYear}`;
}
</script>