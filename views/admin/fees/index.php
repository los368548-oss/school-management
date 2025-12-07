<?php
// Extract data
$classes = $classes ?? [];
$pending_fees = $pending_fees ?? [];
?>

<div class="row">
    <!-- Fee Statistics -->
    <div class="col-12 mb-4">
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Collected (This Month)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₹<?php echo number_format(0); // Would be calculated from database ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
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
                                    Pending Fees
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ₹<?php echo number_format(0); // Would be calculated from database ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                    Students with Pending Fees
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo count($pending_fees); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Defaulters
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo count(array_filter($pending_fees, function($student) { return $student['pending_amount'] > 5000; })); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Fees Table -->
    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Students with Pending Fees</h5>
                <a href="/admin/fees/report" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-chart-bar me-1"></i>View Full Report
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($pending_fees)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h6 class="text-success">All fees collected!</h6>
                        <p class="text-muted">No pending fees found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Scholar No</th>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Pending Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_fees as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['scholar_number']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['class_name'] . ' ' . $student['section']); ?></td>
                                        <td>
                                            <span class="badge bg-danger">₹<?php echo number_format($student['pending_amount']); ?></span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/admin/fees/collect?student_id=<?php echo $student['id']; ?>"
                                                   class="btn btn-sm btn-outline-success" title="Collect Fee">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-info"
                                                        onclick="viewFeeDetails(<?php echo $student['id']; ?>)"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
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

    <!-- Quick Actions & Fee Structure -->
    <div class="col-xl-4 mb-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/admin/fees/collect" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Collect New Payment
                    </a>
                    <a href="/admin/fees/report" class="btn btn-outline-secondary">
                        <i class="fas fa-chart-bar me-2"></i>Fee Reports
                    </a>
                    <button type="button" class="btn btn-outline-info" onclick="exportFeeData()">
                        <i class="fas fa-download me-2"></i>Export Data
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="sendReminders()">
                        <i class="fas fa-envelope me-2"></i>Send Reminders
                    </button>
                </div>
            </div>
        </div>

        <!-- Fee Structure Overview -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Fee Structure by Class</h6>
            </div>
            <div class="card-body">
                <?php if (empty($classes)): ?>
                    <p class="text-muted small">No classes configured yet.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($classes as $class): ?>
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($class['name'] . ' ' . $class['section']); ?></strong>
                                    <br>
                                    <small class="text-muted"><?php echo htmlspecialchars($class['academic_year']); ?></small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-primary">₹<?php echo number_format(0); // Would show total fees for class ?></span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" title="Edit Structure">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Fee Details Modal -->
<div class="modal fade" id="feeDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fee Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="feeDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewFeeDetails(studentId) {
    fetch(`/admin/fees/details?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.data.student;
                const feeStatus = data.data.fee_status;
                const fees = data.data.fees;
                const history = data.data.payment_history;

                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Student Information</h6>
                            <p><strong>Name:</strong> ${student.first_name} ${student.last_name}</p>
                            <p><strong>Scholar No:</strong> ${student.scholar_number}</p>
                            <p><strong>Class:</strong> ${student.class_name} ${student.section}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Fee Status</h6>
                            <p><strong>Total Fees:</strong> ₹${feeStatus.total_fees.toLocaleString()}</p>
                            <p><strong>Paid:</strong> ₹${feeStatus.paid_amount.toLocaleString()}</p>
                            <p><strong>Pending:</strong> <span class="text-danger">₹${feeStatus.pending_amount.toLocaleString()}</span></p>
                        </div>
                    </div>

                    <h6 class="mt-3">Fee Structure</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fee Type</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>`;

                fees.forEach(fee => {
                    content += `
                        <tr>
                            <td>${fee.fee_type}</td>
                            <td>₹${fee.amount.toLocaleString()}</td>
                        </tr>`;
                });

                content += `
                            </tbody>
                        </table>
                    </div>

                    <h6 class="mt-3">Payment History</h6>`;

                if (history.length === 0) {
                    content += '<p class="text-muted">No payment history found.</p>';
                } else {
                    content += `
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Mode</th>
                                        <th>Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                    history.forEach(payment => {
                        content += `
                            <tr>
                                <td>${new Date(payment.payment_date).toLocaleDateString()}</td>
                                <td>₹${payment.amount.toLocaleString()}</td>
                                <td>${payment.payment_mode}</td>
                                <td>${payment.receipt_number}</td>
                            </tr>`;
                    });

                    content += `
                                </tbody>
                            </table>
                        </div>`;
                }

                document.getElementById('feeDetailsContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('feeDetailsModal')).show();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading fee details.');
        });
}

function exportFeeData() {
    alert('Export functionality will be implemented');
}

function sendReminders() {
    alert('Reminder functionality will be implemented');
}
</script>