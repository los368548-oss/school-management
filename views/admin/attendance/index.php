<?php
// Extract data
$classes = $classes ?? [];
$csrf_token = $csrf_token ?? '';
?>

<div class="row">
    <!-- Attendance Overview -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Mark Daily Attendance</h5>
            </div>
            <div class="card-body">
                <form id="attendanceForm" method="POST" action="/admin/attendance/mark">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="class_id" class="form-label">Select Class *</label>
                            <select class="form-select" id="class_id" name="class_id" required>
                                <option value="">Choose a class...</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class['id']; ?>">
                                        <?php echo htmlspecialchars($class['name'] . ' ' . $class['section']); ?>
                                        <?php if ($class['class_teacher_name']): ?>
                                            (<?php echo htmlspecialchars($class['class_teacher_name']); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="date" class="form-label">Attendance Date *</label>
                            <input type="date" class="form-control" id="date" name="date"
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div id="studentsContainer" style="display: none;">
                        <h6 class="mb-3">Mark Attendance for Students</h6>
                        <div class="table-responsive">
                            <table class="table table-striped" id="attendanceTable">
                                <thead>
                                    <tr>
                                        <th>Roll No</th>
                                        <th>Student Name</th>
                                        <th>Scholar Number</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentsTableBody">
                                    <!-- Students will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary me-2" onclick="markAllPresent()">
                                <i class="fas fa-check-circle me-1"></i>Mark All Present
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="markAllAbsent()">
                                <i class="fas fa-times-circle me-1"></i>Mark All Absent
                            </button>
                        </div>
                    </div>

                    <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading students...</p>
                    </div>

                    <div id="noStudentsMessage" class="text-center py-4" style="display: none;">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5>No Students Found</h5>
                        <p class="text-muted">This class has no enrolled students.</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance Summary & Actions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Attendance Summary</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="h5 text-success mb-0" id="presentCount">0</div>
                            <small class="text-muted">Present</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="h5 text-danger mb-0" id="absentCount">0</div>
                            <small class="text-muted">Absent</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="h5 text-warning mb-0" id="lateCount">0</div>
                            <small class="text-muted">Late</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" id="saveAttendanceBtn" style="display: none;">
                        <i class="fas fa-save me-1"></i>Save Attendance
                    </button>
                    <a href="/admin/attendance/report" class="btn btn-info">
                        <i class="fas fa-chart-bar me-1"></i>View Reports
                    </a>
                    <a href="/admin/attendance/bulk" class="btn btn-secondary">
                        <i class="fas fa-upload me-1"></i>Bulk Import
                    </a>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Today's Overview</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <p><strong>Date:</strong> <span id="currentDate"><?php echo date('l, F j, Y'); ?></span></p>
                    <p><strong>Classes with Attendance:</strong> <span id="classesMarked">0</span></p>
                    <p><strong>Total Students:</strong> <span id="totalStudents">0</span></p>
                    <p><strong>Overall Attendance:</strong> <span id="overallAttendance">0%</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance History -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Attendance Records</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Class</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Late</th>
                                <th>Attendance %</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recentAttendanceTable">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Select a class to view attendance records</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentStudents = [];
let attendanceData = {};

document.getElementById('class_id').addEventListener('change', function() {
    const classId = this.value;
    const date = document.getElementById('date').value;

    if (classId && date) {
        loadStudentsForAttendance(classId, date);
    } else {
        document.getElementById('studentsContainer').style.display = 'none';
    }
});

document.getElementById('date').addEventListener('change', function() {
    const classId = document.getElementById('class_id').value;
    const date = this.value;

    if (classId && date) {
        loadStudentsForAttendance(classId, date);
    }
});

function loadStudentsForAttendance(classId, date) {
    document.getElementById('loadingIndicator').style.display = 'block';
    document.getElementById('studentsContainer').style.display = 'none';
    document.getElementById('noStudentsMessage').style.display = 'none';

    fetch(`/api/attendance/students?class_id=${classId}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingIndicator').style.display = 'none';

            if (data.success && data.students && data.students.length > 0) {
                currentStudents = data.students;
                displayStudentsForAttendance(data.students, data.is_marked);
                document.getElementById('studentsContainer').style.display = 'block';
                loadRecentAttendance(classId);
            } else {
                document.getElementById('noStudentsMessage').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            document.getElementById('loadingIndicator').style.display = 'none';
            alert('Error loading students. Please try again.');
        });
}

function displayStudentsForAttendance(students, isMarked) {
    const tbody = document.getElementById('studentsTableBody');
    tbody.innerHTML = '';

    attendanceData = {};

    students.forEach((student, index) => {
        const status = student.attendance_status || 'Present';
        attendanceData[student.id] = status;

        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${student.first_name} ${student.last_name}</td>
                <td>${student.scholar_number}</td>
                <td>
                    <select class="form-select form-select-sm attendance-status" data-student-id="${student.id}">
                        <option value="Present" ${status === 'Present' ? 'selected' : ''}>Present</option>
                        <option value="Absent" ${status === 'Absent' ? 'selected' : ''}>Absent</option>
                        <option value="Late" ${status === 'Late' ? 'selected' : ''}>Late</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-success me-1" onclick="markStudentPresent(${student.id})">
                        <i class="fas fa-check"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger me-1" onclick="markStudentAbsent(${student.id})">
                        <i class="fas fa-times"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="markStudentLate(${student.id})">
                        <i class="fas fa-clock"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });

    // Add event listeners to status dropdowns
    document.querySelectorAll('.attendance-status').forEach(select => {
        select.addEventListener('change', function() {
            const studentId = this.dataset.studentId;
            attendanceData[studentId] = this.value;
            updateSummary();
        });
    });

    updateSummary();

    // Show/hide save button based on whether attendance is already marked
    document.getElementById('saveAttendanceBtn').style.display = isMarked ? 'none' : 'block';
}

function markStudentPresent(studentId) {
    document.querySelector(`[data-student-id="${studentId}"]`).value = 'Present';
    attendanceData[studentId] = 'Present';
    updateSummary();
}

function markStudentAbsent(studentId) {
    document.querySelector(`[data-student-id="${studentId}"]`).value = 'Absent';
    attendanceData[studentId] = 'Absent';
    updateSummary();
}

function markStudentLate(studentId) {
    document.querySelector(`[data-student-id="${studentId}"]`).value = 'Late';
    attendanceData[studentId] = 'Late';
    updateSummary();
}

function markAllPresent() {
    document.querySelectorAll('.attendance-status').forEach(select => {
        select.value = 'Present';
        attendanceData[select.dataset.studentId] = 'Present';
    });
    updateSummary();
}

function markAllAbsent() {
    document.querySelectorAll('.attendance-status').forEach(select => {
        select.value = 'Absent';
        attendanceData[select.dataset.studentId] = 'Absent';
    });
    updateSummary();
}

function updateSummary() {
    let present = 0, absent = 0, late = 0;

    Object.values(attendanceData).forEach(status => {
        switch (status) {
            case 'Present': present++; break;
            case 'Absent': absent++; break;
            case 'Late': late++; break;
        }
    });

    document.getElementById('presentCount').textContent = present;
    document.getElementById('absentCount').textContent = absent;
    document.getElementById('lateCount').textContent = late;
}

function loadRecentAttendance(classId) {
    fetch(`/api/attendance/recent?class_id=${classId}&limit=5`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('recentAttendanceTable');

            if (data.success && data.records && data.records.length > 0) {
                tbody.innerHTML = '';
                data.records.forEach(record => {
                    const row = `
                        <tr>
                            <td>${new Date(record.date).toLocaleDateString()}</td>
                            <td>${record.class_name} ${record.class_section}</td>
                            <td><span class="badge bg-success">${record.present_count}</span></td>
                            <td><span class="badge bg-danger">${record.absent_count}</span></td>
                            <td><span class="badge bg-warning">${record.late_count}</span></td>
                            <td>${record.attendance_percentage}%</td>
                            <td>
                                <a href="/admin/attendance/report?class_id=${classId}&date=${record.date}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            }
        })
        .catch(error => console.error('Error loading recent attendance:', error));
}

// Save attendance
document.getElementById('saveAttendanceBtn').addEventListener('click', function() {
    const classId = document.getElementById('class_id').value;
    const date = document.getElementById('date').value;

    if (!classId || !date) {
        alert('Please select a class and date.');
        return;
    }

    // Prepare attendance data
    const attendance = {};
    Object.keys(attendanceData).forEach(studentId => {
        attendance[studentId] = attendanceData[studentId];
    });

    const formData = new FormData();
    formData.append('csrf_token', '<?php echo $csrf_token; ?>');
    formData.append('class_id', classId);
    formData.append('date', date);
    formData.append('attendance', JSON.stringify(attendance));

    this.disabled = true;
    this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';

    fetch('/admin/attendance/mark', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Attendance saved successfully!');
            this.style.display = 'none';
            loadRecentAttendance(classId);
        } else {
            alert('Error: ' + (data.message || 'Failed to save attendance'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving attendance');
    })
    .finally(() => {
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-save me-1"></i>Save Attendance';
    });
});
</script>