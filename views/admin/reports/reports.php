<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: 0.75rem 1rem;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background: #0d6efd;
        }
        .content-wrapper {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            .content-wrapper {
                margin-left: 250px;
            }
        }
        .report-card {
            transition: transform 0.2s, box-shadow 0.2s;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar d-md-block collapse" id="sidebar">
        <div class="sidebar-sticky">
            <div class="p-3">
                <h5 class="text-white mb-4">
                    <i class="fas fa-school"></i> School Admin
                </h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/dashboard">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/students">
                            <i class="fas fa-users"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/classes">
                            <i class="fas fa-chalkboard"></i> Classes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/attendance">
                            <i class="fas fa-calendar-check"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/exams">
                            <i class="fas fa-file-alt"></i> Exams
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/fees">
                            <i class="fas fa-money-bill"></i> Fees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/events">
                            <i class="fas fa-calendar"></i> Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/gallery">
                            <i class="fas fa-images"></i> Gallery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/homepage">
                            <i class="fas fa-home"></i> Homepage
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/admin/reports">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/settings">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                </ul>
            </div>
            <div class="p-3 border-top border-secondary">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle fa-2x text-white me-2"></i>
                    <div>
                        <small class="text-white">Admin</small><br>
                        <a href="/logout" class="text-white-50 small">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand mb-0 h1">Reports & Analytics</span>
                <div class="d-flex">
                    <span class="badge bg-info me-2">
                        Academic Year: <?php echo htmlspecialchars($academic_year['year_name'] ?? 'Not Set'); ?>
                    </span>
                    <a href="/admin/select-academic-year" class="btn btn-sm btn-outline-primary">Change Year</a>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Reports & Analytics</h2>
                    <p class="text-muted">Generate comprehensive reports and analyze school performance</p>
                </div>
                <div>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-download"></i> Export All
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportAllReports('pdf')">
                                <i class="fas fa-file-pdf"></i> PDF Report
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportAllReports('excel')">
                                <i class="fas fa-file-excel"></i> Excel Report
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportAllReports('csv')">
                                <i class="fas fa-file-csv"></i> CSV Data
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Report Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card report-card bg-primary text-white h-100" onclick="generateStudentReport()">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h6 class="card-title">Student Report</h6>
                            <p class="card-text small">Enrollment, demographics, and academic performance</p>
                            <small>Click to generate</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card report-card bg-success text-white h-100" onclick="generateAttendanceReport()">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-check fa-2x mb-2"></i>
                            <h6 class="card-title">Attendance Report</h6>
                            <p class="card-text small">Daily attendance, trends, and defaulters</p>
                            <small>Click to generate</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card report-card bg-info text-white h-100" onclick="generateFeeReport()">
                        <div class="card-body text-center">
                            <i class="fas fa-money-bill fa-2x mb-2"></i>
                            <h6 class="card-title">Fee Collection Report</h6>
                            <p class="card-text small">Payment status, outstanding fees, and trends</p>
                            <small>Click to generate</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card report-card bg-warning text-white h-100" onclick="generateExamReport()">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <h6 class="card-title">Examination Report</h6>
                            <p class="card-text small">Exam results, grades, and performance analysis</p>
                            <small>Click to generate</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Report Filters</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" id="reportType">
                                <option value="students">Student Report</option>
                                <option value="attendance">Attendance Report</option>
                                <option value="fees">Fee Report</option>
                                <option value="exams">Exam Report</option>
                                <option value="events">Event Report</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Class</label>
                            <select class="form-select" id="reportClass">
                                <option value="">All Classes</option>
                                <!-- Classes will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" class="form-control" id="fromDate">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" class="form-control" id="toDate">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Actions</label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" onclick="generateCustomReport()">
                                    <i class="fas fa-chart-bar"></i> Generate
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Student Enrollment by Class</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="enrollmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Monthly Fee Collection</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="feeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Attendance Trends</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="attendanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Exam Performance</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="examChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Reports Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Reports</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshReports()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="reportsTable" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Report Type</th>
                                    <th>Parameters</th>
                                    <th>Generated By</th>
                                    <th>Generated On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recent_reports)): ?>
                                    <?php foreach ($recent_reports as $report): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary"><?php echo ucfirst($report['report_type']); ?></span>
                                            </td>
                                            <td><?php echo htmlspecialchars($report['parameters'] ?? 'All data'); ?></td>
                                            <td><?php echo htmlspecialchars($report['generated_by_name']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($report['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-info" title="View Report"
                                                            onclick="viewReport(<?php echo $report['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-primary" title="Download PDF"
                                                            onclick="downloadReport(<?php echo $report['id']; ?>, 'pdf')">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-success" title="Download Excel"
                                                            onclick="downloadReport(<?php echo $report['id']; ?>, 'excel')">
                                                        <i class="fas fa-file-excel"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                            <p>No reports generated yet.</p>
                                            <p class="text-muted">Generate your first report using the options above.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Preview Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reportModalTitle">Report Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="reportContent">
                        <!-- Report content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printReport()">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportReport('pdf')">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#reportsTable').DataTable({
                pageLength: 10,
                order: [[3, 'desc']]
            });

            // Load initial charts
            loadCharts();

            // Set default date range (current month)
            const now = new Date();
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

            $('#fromDate').val(firstDay.toISOString().split('T')[0]);
            $('#toDate').val(lastDay.toISOString().split('T')[0]);
        });

        function loadCharts() {
            // Enrollment Chart
            const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
            new Chart(enrollmentCtx, {
                type: 'bar',
                data: {
                    labels: ['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5'],
                    datasets: [{
                        label: 'Students',
                        data: [45, 42, 38, 35, 40],
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Fee Collection Chart
            const feeCtx = document.getElementById('feeChart').getContext('2d');
            new Chart(feeCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Collected (₹)',
                        data: [45000, 52000, 48000, 55000, 50000, 58000],
                        borderColor: 'rgba(25, 135, 84, 1)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Attendance Chart
            const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
            new Chart(attendanceCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Late'],
                    datasets: [{
                        data: [85, 10, 5],
                        backgroundColor: [
                            'rgba(25, 135, 84, 0.8)',
                            'rgba(220, 53, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Exam Performance Chart
            const examCtx = document.getElementById('examChart').getContext('2d');
            new Chart(examCtx, {
                type: 'radar',
                data: {
                    labels: ['Mathematics', 'Science', 'English', 'Social Studies', 'Hindi'],
                    datasets: [{
                        label: 'Average Score (%)',
                        data: [85, 78, 92, 88, 82],
                        borderColor: 'rgba(13, 110, 253, 1)',
                        backgroundColor: 'rgba(13, 110, 253, 0.2)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }

        function generateStudentReport() {
            const reportType = 'students';
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();

            generateReport(reportType, { fromDate, toDate });
        }

        function generateAttendanceReport() {
            const reportType = 'attendance';
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();
            const classId = $('#reportClass').val();

            generateReport(reportType, { fromDate, toDate, classId });
        }

        function generateFeeReport() {
            const reportType = 'fees';
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();

            generateReport(reportType, { fromDate, toDate });
        }

        function generateExamReport() {
            const reportType = 'exams';
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();
            const classId = $('#reportClass').val();

            generateReport(reportType, { fromDate, toDate, classId });
        }

        function generateCustomReport() {
            const reportType = $('#reportType').val();
            const classId = $('#reportClass').val();
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();

            generateReport(reportType, { classId, fromDate, toDate });
        }

        function generateReport(type, params) {
            $('#reportModalTitle').text(ucfirst(type) + ' Report');
            $('#reportModal').modal('show');

            // Show loading
            $('#reportContent').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Generating report...</p></div>');

            $.ajax({
                url: '/api/v1/reports/' + type,
                method: 'POST',
                data: JSON.stringify(params),
                contentType: 'application/json',
                headers: {
                    'X-API-Token': '<?php echo $_SESSION["api_token"] ?? ""; ?>'
                },
                success: function(response) {
                    if (response.success) {
                        displayReport(response.data, type);
                    } else {
                        $('#reportContent').html('<div class="alert alert-danger">Error: ' + response.error + '</div>');
                    }
                },
                error: function(xhr) {
                    $('#reportContent').html('<div class="alert alert-danger">Error generating report: ' + xhr.responseJSON?.error || 'Unknown error' + '</div>');
                }
            });
        }

        function displayReport(data, type) {
            let html = '';

            switch (type) {
                case 'students':
                    html = generateStudentReportHTML(data);
                    break;
                case 'attendance':
                    html = generateAttendanceReportHTML(data);
                    break;
                case 'fees':
                    html = generateFeeReportHTML(data);
                    break;
                case 'exams':
                    html = generateExamReportHTML(data);
                    break;
                default:
                    html = '<div class="alert alert-info">Report generated successfully. Use export buttons to download.</div>';
            }

            $('#reportContent').html(html);
        }

        function generateStudentReportHTML(data) {
            return `
                <div class="report-header text-center mb-4">
                    <h4>Student Enrollment Report</h4>
                    <p class="text-muted">Academic Year: <?php echo htmlspecialchars($academic_year['year_name'] ?? 'Current'); ?></p>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4>${data.total_students || 0}</h4>
                                <small>Total Students</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>${data.active_students || 0}</h4>
                                <small>Active Students</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>${data.classes || 0}</h4>
                                <small>Classes</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4>${data.new_admissions || 0}</h4>
                                <small>New Admissions</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Total Students</th>
                                <th>Boys</th>
                                <th>Girls</th>
                                <th>Average Age</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.class_breakdown ? data.class_breakdown.map(cls => `
                                <tr>
                                    <td>${cls.class_name}</td>
                                    <td>${cls.total}</td>
                                    <td>${cls.boys}</td>
                                    <td>${cls.girls}</td>
                                    <td>${cls.avg_age}</td>
                                </tr>
                            `).join('') : ''}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function generateAttendanceReportHTML(data) {
            return `
                <div class="report-header text-center mb-4">
                    <h4>Attendance Report</h4>
                    <p class="text-muted">Period: ${$('#fromDate').val()} to ${$('#toDate').val()}</p>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>${data.overall_percentage || 0}%</h4>
                                <small>Overall Attendance</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4>${data.total_present || 0}</h4>
                                <small>Total Present</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4>${data.total_absent || 0}</h4>
                                <small>Total Absent</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Late</th>
                                <th>Attendance %</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.class_attendance ? data.class_attendance.map(cls => `
                                <tr>
                                    <td>${cls.class_name}</td>
                                    <td>${cls.present}</td>
                                    <td>${cls.absent}</td>
                                    <td>${cls.late}</td>
                                    <td>${cls.percentage}%</td>
                                </tr>
                            `).join('') : ''}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function generateFeeReportHTML(data) {
            return `
                <div class="report-header text-center mb-4">
                    <h4>Fee Collection Report</h4>
                    <p class="text-muted">Period: ${$('#fromDate').val()} to ${$('#toDate').val()}</p>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>₹${(data.total_collected || 0).toLocaleString()}</h4>
                                <small>Total Collected</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4>₹${(data.total_pending || 0).toLocaleString()}</h4>
                                <small>Total Pending</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>${data.collection_rate || 0}%</h4>
                                <small>Collection Rate</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fee Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Fee collection details will be displayed here</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            `;
        }

        function generateExamReportHTML(data) {
            return `
                <div class="report-header text-center mb-4">
                    <h4>Examination Report</h4>
                    <p class="text-muted">Academic Year: <?php echo htmlspecialchars($academic_year['year_name'] ?? 'Current'); ?></p>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4>${data.total_exams || 0}</h4>
                                <small>Total Exams</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4>${data.average_score || 0}%</h4>
                                <small>Average Score</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4>${data.pass_percentage || 0}%</h4>
                                <small>Pass Percentage</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Exam</th>
                                <th>Class</th>
                                <th>Average Score</th>
                                <th>Pass %</th>
                                <th>Highest Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.exam_results ? data.exam_results.map(exam => `
                                <tr>
                                    <td>${exam.exam_name}</td>
                                    <td>${exam.class_name}</td>
                                    <td>${exam.average_score}%</td>
                                    <td>${exam.pass_percentage}%</td>
                                    <td>${exam.highest_score}%</td>
                                </tr>
                            `).join('') : ''}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function exportAllReports(format) {
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();

            window.open(`/api/v1/reports/export-all?format=${format}&from=${fromDate}&to=${toDate}`, '_blank');
        }

        function viewReport(reportId) {
            // Load and display existing report
            alert('View report functionality will be implemented');
        }

        function downloadReport(reportId, format) {
            window.open(`/api/v1/reports/download/${reportId}?format=${format}`, '_blank');
        }

        function printReport() {
            window.print();
        }

        function exportReport(format) {
            const reportType = $('#reportType').val();
            const fromDate = $('#fromDate').val();
            const toDate = $('#toDate').val();

            window.open(`/api/v1/reports/${reportType}/export?format=${format}&from=${fromDate}&to=${toDate}`, '_blank');
        }

        function refreshReports() {
            location.reload();
        }

        function clearFilters() {
            $('#reportType, #reportClass, #fromDate, #toDate').val('');
        }

        function ucfirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    </script>
</body>
</html>