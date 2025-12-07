<?php
// Extract data
$student = $student ?? [];
$fee_status = $fee_status ?? [];
$payment_history = $payment_history ?? [];
?>

<div class="row">
    <!-- Fee Status Overview -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Fee Status</h5>
            </div>
            <div class="card-body text-center">
                <?php
                $totalPending = $fee_status['total_pending'] ?? 0;
                $totalPaid = $fee_status['total_paid'] ?? 0;
                $totalFees = $totalPending + $totalPaid;
                $paymentPercentage = $totalFees > 0 ? round(($totalPaid / $totalFees) * 100, 1) : 0;
                ?>

                <div class="mb-3">
                    <div class="display-5 text-<?php echo $totalPending > 0 ? 'danger' : 'success'; ?>">
                        ₹<?php echo number_format($totalPending, 2); ?>
                    </div>
                    <p class="text-muted mb-2"><?php echo $totalPending > 0 ? 'Pending' : 'All Clear'; ?></p>
                </div>

                <div class="row text-center mb-3">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h6 text-success mb-0">₹<?php echo number_format($totalPaid, 2); ?></div>
                            <small class="text-muted">Paid</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h6 text-danger mb-0">₹<?php echo number_format($totalPending, 2); ?></div>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>

                <div class="progress mb-2">
                    <div class="progress-bar bg-<?php echo $totalPending > 0 ? 'warning' : 'success'; ?>"
                         role="progressbar"
                         style="width: <?php echo $paymentPercentage; ?>%"
                         aria-valuenow="<?php echo $paymentPercentage; ?>"
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <small class="text-muted">Payment Progress: <?php echo $paymentPercentage; ?>%</small>

                <?php if ($totalPending > 0): ?>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm w-100" onclick="payFees()">
                            <i class="fas fa-credit-card me-1"></i>Pay Pending Fees
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <p><strong>Total Fee Structure:</strong> ₹<?php echo number_format($totalFees, 2); ?></p>
                    <p><strong>Payments Made:</strong> <?php echo count($payment_history); ?></p>
                    <p><strong>Last Payment:</strong>
                        <?php
                        if (!empty($payment_history)) {
                            $lastPayment = $payment_history[0];
                            echo date('d M Y', strtotime($lastPayment['payment_date']));
                        } else {
                            echo 'None';
                        }
                        ?>
                    </p>
                    <p><strong>Academic Year:</strong> <?php echo htmlspecialchars($student['academic_year'] ?? date('Y') . '-' . (date('Y') + 1)); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Fee Details -->
    <div class="col-lg-8">
        <!-- Current Fee Breakdown -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Current Fee Breakdown</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($fee_status['breakdown'])): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fee Type</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Pending Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fee_status['breakdown'] as $fee): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fee['fee_type']); ?></td>
                                        <td>₹<?php echo number_format($fee['total_amount'], 2); ?></td>
                                        <td>₹<?php echo number_format($fee['paid_amount'], 2); ?></td>
                                        <td>₹<?php echo number_format($fee['pending_amount'], 2); ?></td>
                                        <td><?php echo $fee['due_date'] ? date('d M Y', strtotime($fee['due_date'])) : 'N/A'; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $fee['pending_amount'] > 0 ? 'danger' : 'success'; ?>">
                                                <?php echo $fee['pending_amount'] > 0 ? 'Pending' : 'Paid'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                        <h6>No Fee Structure Found</h6>
                        <p class="text-muted">Fee structure information will be available once configured by the administration.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment History</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($payment_history)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Fee Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payment_history as $payment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($payment['receipt_number']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
                                        <td>₹<?php echo number_format($payment['amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($payment['payment_mode']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($payment['fee_type'] ?? 'General'); ?></td>
                                        <td>
                                            <span class="badge bg-success">Completed</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Download Receipt Button -->
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-outline-primary" onclick="downloadReceipt()">
                            <i class="fas fa-download me-1"></i>Download Latest Receipt
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h6>No Payment History</h6>
                        <p class="text-muted">Your fee payment history will appear here once payments are made.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Instructions -->
        <?php if ($totalPending > 0): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Payment Instructions</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Payment Methods Available:</h6>
                    <ul class="mb-0">
                        <li><strong>Online Payment:</strong> Pay securely through our online portal</li>
                        <li><strong>Cash Payment:</strong> Visit the school office during working hours</li>
                        <li><strong>Bank Transfer:</strong> Direct bank transfer to school account</li>
                        <li><strong>Cheque:</strong> Accepted at school office (clearance may take 2-3 days)</li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6>Important Notes:</h6>
                        <ul class="small">
                            <li>Late fee charges may apply after due date</li>
                            <li>Keep payment receipts for future reference</li>
                            <li>Contact school office for payment queries</li>
                            <li>Online payments are processed instantly</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Contact Information:</h6>
                        <p class="small mb-1"><strong>Phone:</strong> +91-XXXXXXXXXX</p>
                        <p class="small mb-1"><strong>Email:</strong> fees@school.com</p>
                        <p class="small mb-0"><strong>Office Hours:</strong> 9:00 AM - 4:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pay Pending Fees</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token ?? ''; ?>">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Note:</strong> This is a demo payment form. In the actual system, you would be redirected to a secure payment gateway.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Payment Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="payment_amount"
                                       value="<?php echo $totalPending; ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="payment_mode" class="form-label">Payment Mode</label>
                            <select class="form-select" id="payment_mode" name="payment_mode">
                                <option value="online">Online Payment</option>
                                <option value="cash">Cash (School Office)</option>
                                <option value="cheque">Cheque</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                    </div>

                    <div id="online_payment_fields" class="payment-fields">
                        <h6>Online Payment Options:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="online_method" id="credit_card" value="credit_card">
                                    <label class="form-check-label" for="credit_card">
                                        Credit/Debit Card
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="online_method" id="net_banking" value="net_banking">
                                    <label class="form-check-label" for="net_banking">
                                        Net Banking
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="online_method" id="upi" value="upi" checked>
                                    <label class="form-check-label" for="upi">
                                        UPI
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="online_method" id="wallet" value="wallet">
                                    <label class="form-check-label" for="wallet">
                                        Digital Wallet
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="cash_payment_fields" class="payment-fields d-none">
                        <div class="alert alert-info">
                            <strong>Cash Payment:</strong> Visit the school office with the exact amount. Receipt will be provided immediately.
                        </div>
                    </div>

                    <div id="cheque_payment_fields" class="payment-fields d-none">
                        <div class="mb-3">
                            <label for="cheque_number" class="form-label">Cheque Number</label>
                            <input type="text" class="form-control" id="cheque_number" name="cheque_number">
                        </div>
                        <div class="alert alert-warning">
                            <strong>Cheque Payment:</strong> Payment will be confirmed after cheque clearance (2-3 working days).
                        </div>
                    </div>

                    <div id="bank_transfer_fields" class="payment-fields d-none">
                        <div class="alert alert-info">
                            <strong>Bank Transfer Details:</strong><br>
                            Account Name: A.s.higher secondary school<br>
                            Account Number: XXXXXXXX<br>
                            IFSC Code: XXXXXXXX<br>
                            Bank: State Bank of India
                        </div>
                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID/Reference</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function payFees() {
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

document.getElementById('payment_mode').addEventListener('change', function() {
    const mode = this.value;

    // Hide all payment fields
    document.querySelectorAll('.payment-fields').forEach(field => {
        field.classList.add('d-none');
    });

    // Show selected payment fields
    document.getElementById(mode + '_payment_fields').classList.remove('d-none');
});

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    alert('Payment processing is not implemented in this demo. In a real system, you would be redirected to a secure payment gateway.');
    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
});

function downloadReceipt() {
    alert('Receipt download feature will be available soon. Please contact the school office for physical receipts.');
}
</script>