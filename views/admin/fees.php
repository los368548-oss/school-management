<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Management - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
        .fee-card {
            transition: transform 0.2s;
        }
        .fee-card:hover {
            transform: translateY(-2px);
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
                        <a class="nav-link active" href="/admin/fees">
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
                        <a class="nav-link" href="/admin/reports">
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
                <span class="navbar-brand mb-0 h1">Fee Management</span>
                <div class="d-flex">
                    <span class="badge bg-info me-2">
                        Academic Year: <?php echo htmlspecialchars($academic_year['year_name'] ?? 'Not Set'); ?>
                    </span>
                    <a href="/admin/select-academic-year" class="btn btn-sm btn-outline-primary">Change Year</a>
                </div>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="container-fluid p-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">Fee Management</h2>
                    <p class="text-muted">Manage fee structures, collect payments, and track outstanding fees</p>
                </div>
                <div>
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addFeeModal">
                        <i class="fas fa-plus"></i> Add Fee Structure
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#collectFeeModal">
                        <i class="fas fa-money-bill-wave"></i> Collect Fee
                    </button>
                </div>
            </div>

            <!-- Fee Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card fee-card bg-primary text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-rupee-sign fa-2x mb-2"></i>
                            <h4 class="card-title">₹<?php echo number_format($fee_stats['total_fees'] ?? 0); ?></h4>
                            <p class="card-text">Total Fees</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card fee-card bg-success text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <h4 class="card-title">₹<?php echo number_format($fee_stats['collected_fees'] ?? 0); ?></h4>
                            <p class="card-text">Collected</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card fee-card bg-warning text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <h4 class="card-title">₹<?php echo number_format($fee_stats['pending_amount'] ?? 0); ?></h4>
                            <p class="card-text">Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card fee-card bg-danger text-white h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h4 class="card-title"><?php echo number_format($fee_stats['defaulters'] ?? 0); ?></h4>
                            <p class="card-text">Defaulters</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs mb-4" id="feeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="structure-tab" data-bs-toggle="tab" data-bs-target="#structure" type="button" role="tab">
                        <i class="fas fa-list"></i> Fee Structure
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">
                        <i class="fas fa-credit-card"></i> Payments
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="defaulters-tab" data-bs-toggle="tab" data-bs-target="#defaulters" type="button" role="tab">
                        <i class="fas fa-exclamation-triangle"></i> Defaulters
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                        <i class="fas fa-chart-bar"></i> Reports
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="feeTabsContent">
                <!-- Fee Structure Tab -->
                <div class="tab-pane fade show active" id="structure" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Fee Structure</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="feeStructureTable" class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Fee Name</th>
                                            <th>Type</th>
                                            <th>Class</th>
                                            <th>Amount</th>
                                            <th>Frequency</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($fee_structure)): ?>
                                            <?php foreach ($fee_structure as $fee): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($fee['fee_name']); ?></td>
                                                    <td>
                                                        <span class="badge bg-secondary"><?php echo ucfirst($fee['fee_type']); ?></span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($fee['class_name'] ?? 'All Classes'); ?></td>
                                                    <td>₹<?php echo number_format($fee['amount'], 2); ?></td>
                                                    <td><?php echo ucfirst($fee['frequency']); ?></td>
                                                    <td><?php echo $fee['due_date'] ? date('d/m/Y', strtotime($fee['due_date'])) : '-'; ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $fee['status'] === 'active' ? 'success' : 'warning'; ?>">
                                                            <?php echo ucfirst($fee['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="fas fa-list fa-3x mb-3"></i>
                                                    <p>No fee structures found. Create your first fee structure.</p>
                                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeeModal">
                                                        <i class="fas fa-plus"></i> Add Fee Structure
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Tab -->
                <div class="tab-pane fade" id="payments" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Fee Payments</h5>
                            <div>
                                <button class="btn btn-sm btn-outline-success me-2" onclick="exportPayments()">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#collectFeeModal">
                                    <i class="fas fa-plus"></i> New Payment
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="paymentSearch" placeholder="Search by student name or receipt no">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" id="paymentModeFilter">
                                        <option value="">All Modes</option>
                                        <option value="cash">Cash</option>
                                        <option value="online">Online</option>
                                        <option value="cheque">Cheque</option>
                                        <option value="upi">UPI</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" class="form-control" id="paymentFromDate">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" class="form-control" id="paymentToDate">
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-outline-primary me-2" onclick="filterPayments()">Filter</button>
                                    <button class="btn btn-outline-secondary" onclick="clearFilters()">Clear</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="paymentsTable" class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Receipt No</th>
                                            <th>Student</th>
                                            <th>Class</th>
                                            <th>Fee Type</th>
                                            <th>Amount</th>
                                            <th>Payment Mode</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($recent_payments)): ?>
                                            <?php foreach ($recent_payments as $payment): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($payment['receipt_number']); ?></td>
                                                    <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($payment['class_name'] . ' ' . $payment['section']); ?></td>
                                                    <td><?php echo htmlspecialchars($payment['fee_name']); ?></td>
                                                    <td>₹<?php echo number_format($payment['amount_paid'], 2); ?></td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo ucfirst($payment['payment_mode']); ?></span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($payment['payment_date'])); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-info" title="Print Receipt"
                                                                    onclick="printReceipt(<?php echo $payment['id']; ?>)">
                                                                <i class="fas fa-print"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-primary" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="fas fa-credit-card fa-3x mb-3"></i>
                                                    <p>No payments found.</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Defaulters Tab -->
                <div class="tab-pane fade" id="defaulters" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Fee Defaulters</h5>
                            <button class="btn btn-sm btn-outline-warning" onclick="sendReminders()">
                                <i class="fas fa-envelope"></i> Send Reminders
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i>
                                Students with outstanding fees are listed below. Total outstanding: <strong>₹<?php echo number_format($fee_stats['pending_amount'] ?? 0); ?></strong>
                            </div>

                            <div class="table-responsive">
                                <table id="defaultersTable" class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Student</th>
                                            <th>Class</th>
                                            <th>Roll No</th>
                                            <th>Total Fees</th>
                                            <th>Paid</th>
                                            <th>Pending</th>
                                            <th>Days Overdue</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($defaulters)): ?>
                                            <?php foreach ($defaulters as $defaulter): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($defaulter['student_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($defaulter['class_name'] . ' ' . $defaulter['section']); ?></td>
                                                    <td><?php echo htmlspecialchars($defaulter['roll_number']); ?></td>
                                                    <td>₹<?php echo number_format($defaulter['total_fees'], 2); ?></td>
                                                    <td>₹<?php echo number_format($defaulter['paid_amount'], 2); ?></td>
                                                    <td>
                                                        <span class="badge bg-danger">₹<?php echo number_format($defaulter['pending_amount'], 2); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning"><?php echo $defaulter['days_overdue'] ?? 0; ?> days</span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-success" title="Collect Payment"
                                                                    onclick="collectFee(<?php echo $defaulter['id']; ?>)">
                                                                <i class="fas fa-money-bill-wave"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-info" title="Send Reminder">
                                                                <i class="fas fa-envelope"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                                    <p>Great! No fee defaulters found.</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Tab -->
                <div class="tab-pane fade" id="reports" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card fee-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-pie fa-3x text-primary mb-3"></i>
                                    <h5>Fee Collection Report</h5>
                                    <p class="text-muted">Monthly and yearly fee collection analytics</p>
                                    <button class="btn btn-primary" onclick="generateCollectionReport()">
                                        <i class="fas fa-chart-bar"></i> Generate Report
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card fee-card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                                    <h5>Export Data</h5>
                                    <p class="text-muted">Export fee data to Excel/CSV format</p>
                                    <button class="btn btn-success" onclick="exportFeeData()">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Fee Modal -->
    <div class="modal fade" id="addFeeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Fee Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addFeeForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Fee Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="fee_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="fee_type" required>
                                    <option value="">Select Type</option>
                                    <option value="tuition">Tuition Fee</option>
                                    <option value="transport">Transport Fee</option>
                                    <option value="exam">Exam Fee</option>
                                    <option value="sports">Sports Fee</option>
                                    <option value="library">Library Fee</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Class (Optional)</label>
                                <select class="form-select" name="class_id">
                                    <option value="">All Classes</option>
                                    <!-- Add classes dynamically -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Frequency <span class="text-danger">*</span></label>
                                <select class="form-select" name="frequency" required>
                                    <option value="yearly">Yearly</option>
                                    <option value="half_yearly">Half Yearly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="one_time">One Time</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" name="due_date">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Fee Structure</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Collect Fee Modal -->
    <div class="modal fade" id="collectFeeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Collect Fee Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="collectFeeForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Student <span class="text-danger">*</span></label>
                                <select class="form-select" name="student_id" id="studentSelect" required>
                                    <option value="">Select Student</option>
                                    <!-- Add students dynamically -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="fee_id" id="feeSelect" required>
                                    <option value="">Select Fee</option>
                                    <!-- Add fees dynamically -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="amount_paid" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
                                <select class="form-select" name="payment_mode" required>
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="upi">UPI</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Transaction/Cheque Number</label>
                                <input type="text" class="form-control" name="transaction_id" placeholder="Optional">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" rows="2" placeholder="Optional remarks"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Collect Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#feeStructureTable, #paymentsTable, #defaultersTable').DataTable({
                pageLength: 25,
                order: [[0, 'asc']]
            });

            // Handle fee form submission
            $('#addFeeForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '/api/v1/fees',
                    method: 'POST',
                    data: JSON.stringify(Object.fromEntries(formData)),
                    contentType: 'application/json',
                    headers: {
                        'X-API-Token': '<?php echo $_SESSION["api_token"] ?? ""; ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#addFeeModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(xhr) {
                        alert('Error adding fee structure: ' + xhr.responseJSON?.error || 'Unknown error');
                    }
                });
            });

            // Handle payment form submission
            $('#collectFeeForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '/api/v1/fees/payments',
                    method: 'POST',
                    data: JSON.stringify(Object.fromEntries(formData)),
                    contentType: 'application/json',
                    headers: {
                        'X-API-Token': '<?php echo $_SESSION["api_token"] ?? ""; ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#collectFeeModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response.error);
                        }
                    },
                    error: function(xhr) {
                        alert('Error collecting payment: ' + xhr.responseJSON?.error || 'Unknown error');
                    }
                });
            });
        });

        function printReceipt(paymentId) {
            window.open('/print/fee-receipt?payment_id=' + paymentId, '_blank');
        }

        function collectFee(studentId) {
            $('#collectFeeModal').modal('show');
            // Pre-select student
            $('#studentSelect').val(studentId);
        }

        function exportPayments() {
            window.location.href = '/api/v1/fees/payments/export';
        }

        function generateCollectionReport() {
            window.location.href = '/api/v1/fees/reports/collection';
        }

        function exportFeeData() {
            window.location.href = '/api/v1/fees/export';
        }

        function sendReminders() {
            if (confirm('Send fee payment reminders to all defaulters?')) {
                $.ajax({
                    url: '/api/v1/fees/reminders',
                    method: 'POST',
                    headers: {
                        'X-API-Token': '<?php echo $_SESSION["api_token"] ?? ""; ?>'
                    },
                    success: function(response) {
                        alert('Reminders sent successfully!');
                    },
                    error: function(xhr) {
                        alert('Error sending reminders: ' + xhr.responseJSON?.error || 'Unknown error');
                    }
                });
            }
        }
    </script>
</body>
</html>