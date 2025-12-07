<?php
// Extract data
$csrf_token = $csrf_token ?? '';
?>

<div class="row">
    <!-- Report Categories -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Available Reports</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Student Reports -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-users me-2"></i>Student Reports
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Comprehensive student information including personal details, attendance, and academic performance.</p>
                                <div class="d-grid gap-2">
                                    <a href="/admin/reports/students" class="btn btn-primary btn-sm">
                                        <i class="fas fa-file-alt me-1"></i>Generate Report
                                    </a>
                                    <button class="btn btn-outline-primary btn-sm" onclick="exportReport('students')">
                                        <i class="fas fa-download me-1"></i>Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Reports -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-calendar-check me-2"></i>Attendance Reports
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Detailed attendance records with statistics, trends, and individual student attendance summaries.</p>
                                <div class="d-grid gap-2">
                                    <a href="/admin/reports/attendance" class="btn btn-success btn-sm">
                                        <i class="fas fa-file-alt me-1"></i>Generate Report
                                    </a>
                                    <button class="btn btn-outline-success btn-sm" onclick="exportReport('attendance')">
                                        <i class="fas fa-download me-1"></i>Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Reports -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-money-bill-wave me-2"></i>Fee Reports
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Financial reports including fee collection status, outstanding payments, and payment history.</p>
                                <div class="d-grid gap-2">
                                    <a href="/admin/reports/fees" class="btn btn-info btn-sm">
                                        <i class="fas fa-file-alt me-1"></i>Generate Report
                                    </a>
                                    <button class="btn btn-outline-info btn-sm" onclick="exportReport('fees')">
                                        <i class="fas fa-download me-1"></i>Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exam Reports -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-warning">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-graduation-cap me-2"></i>Exam Reports
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Examination results, performance analysis, and grade distributions across subjects and classes.</p>
                                <div class="d-grid gap-2">
                                    <a href="/admin/reports/exams" class="btn btn-warning btn-sm">
                                        <i class="fas fa-file-alt me-1"></i>Generate Report
                                    </a>
                                    <button class="btn btn-outline-warning btn-sm" onclick="exportReport('exams')">
                                        <i class="fas fa-download me-1"></i>Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Reports -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-calendar-alt me-2"></i>Event Reports
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">School events summary, participation statistics, and event management reports.</p>
                                <div class="d-grid gap-2">
                                    <a href="/admin/reports/events" class="btn btn-danger btn-sm">
                                        <i class="fas fa-file-alt me-1"></i>Generate Report
                                    </a>
                                    <button class="btn btn-outline-danger btn-sm" onclick="exportReport('events')">
                                        <i class="fas fa-download me-1"></i>Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Reports -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-history me-2"></i>Audit Reports
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">System activity logs, user actions, and security audit trails for compliance and monitoring.</p>
                                <div class="d-grid gap-2">
                                    <a href="/admin/reports/audit" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-file-alt me-1"></i>Generate Report
                                    </a>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportReport('audit')">
                                        <i class="fas fa-download me-1"></i>Export CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Statistics -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Overview</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-primary mb-1" id="totalStudents">0</div>
                            <small class="text-muted">Total Students</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-success mb-1" id="totalClasses">0</div>
                            <small class="text-muted">Total Classes</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-info mb-1" id="totalExams">0</div>
                            <small class="text-muted">Total Exams</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-warning mb-1" id="totalEvents">0</div>
                            <small class="text-muted">Total Events</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-danger mb-1" id="pendingFees">₹0</div>
                            <small class="text-muted">Pending Fees</small>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="border rounded p-3">
                            <div class="h4 text-secondary mb-1" id="avgAttendance">0%</div>
                            <small class="text-muted">Avg Attendance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="exportForm" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="report_type" id="exportReportType">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="exportFormat" class="form-label">Export Format</label>
                        <select class="form-select" id="exportFormat" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF (Coming Soon)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="exportClass" class="form-label">Filter by Class (Optional)</label>
                        <select class="form-select" id="exportClass" name="class_id">
                            <option value="">All Classes</option>
                            <!-- Classes will be loaded dynamically -->
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="exportStartDate" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="exportStartDate" name="start_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="exportEndDate" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="exportEndDate" name="end_date">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentReportType = '';

function exportReport(type) {
    currentReportType = type;
    document.getElementById('exportReportType').value = type;

    // Load classes for filter
    fetch('/api/classes')
        .then(response => response.json())
        .then(data => {
            const classSelect = document.getElementById('exportClass');
            classSelect.innerHTML = '<option value="">All Classes</option>';

            if (data.success && data.data) {
                data.data.forEach(cls => {
                    classSelect.innerHTML += `<option value="${cls.id}">${cls.name} ${cls.section}</option>`;
                });
            }
        })
        .catch(error => console.error('Error loading classes:', error));

    new bootstrap.Modal(document.getElementById('exportModal')).show();
}

document.getElementById('exportForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const params = new URLSearchParams();

    for (let [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }

    // Redirect to export endpoint
    window.location.href = `/admin/reports/export/${currentReportType}?${params.toString()}`;
});

// Load dashboard statistics
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/dashboard/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('totalStudents').textContent = data.data.students || 0;
                document.getElementById('totalClasses').textContent = data.data.classes || 0;
                document.getElementById('totalExams').textContent = data.data.exams || 0;
                document.getElementById('totalEvents').textContent = data.data.events || 0;
                document.getElementById('pendingFees').textContent = '₹' + (data.data.pending_fees || 0);
                document.getElementById('avgAttendance').textContent = (data.data.avg_attendance || 0) + '%';
            }
        })
        .catch(error => console.error('Error loading stats:', error));
});
</script>