<?php
/**
 * Fee Model
 *
 * Handles fee-related database operations
 */

class Fee extends BaseModel {
    protected $table = 'fees';
    protected $fillable = [
        'fee_name', 'fee_type', 'class_id', 'academic_year_id',
        'amount', 'frequency', 'due_date', 'status'
    ];

    /**
     * Get fees for current academic year
     */
    public function getForCurrentYear($classId = null) {
        $academicYearId = $this->getCurrentAcademicYearId();
        if (!$academicYearId) {
            return [];
        }

        $sql = "SELECT f.*, c.class_name, c.section
                FROM {$this->table} f
                LEFT JOIN classes c ON f.class_id = c.id
                WHERE f.academic_year_id = ?";

        $params = [$academicYearId];

        if ($classId) {
            $sql .= " AND (f.class_id = ? OR f.class_id IS NULL)";
            $params[] = $classId;
        }

        $sql .= " ORDER BY f.fee_name";

        return $this->db->select($sql, $params);
    }

    /**
     * Get fee payments for student
     */
    public function getPaymentsForStudent($studentId, $academicYearId = null) {
        if (!$academicYearId) {
            $academicYearId = $this->getCurrentAcademicYearId();
        }

        return $this->db->select(
            "SELECT fp.*, f.fee_name, f.fee_type, f.amount as fee_amount,
                    DATE_FORMAT(fp.payment_date, '%d/%m/%Y') as formatted_date
             FROM fee_payments fp
             JOIN fees f ON fp.fee_id = f.id
             WHERE fp.student_id = ? AND fp.academic_year_id = ?
             ORDER BY fp.payment_date DESC",
            [$studentId, $academicYearId]
        );
    }

    /**
     * Get outstanding fees for student
     */
    public function getOutstandingFees($studentId, $academicYearId = null) {
        if (!$academicYearId) {
            $academicYearId = $this->getCurrentAcademicYearId();
        }

        return $this->db->select(
            "SELECT f.fee_name, f.fee_type, f.amount,
                    COALESCE(SUM(fp.amount_paid), 0) as paid_amount,
                    (f.amount - COALESCE(SUM(fp.amount_paid), 0)) as pending_amount,
                    DATE_FORMAT(f.due_date, '%d/%m/%Y') as due_date
             FROM fees f
             LEFT JOIN fee_payments fp ON f.id = fp.fee_id AND fp.student_id = ?
             WHERE f.academic_year_id = ?
             GROUP BY f.id
             HAVING pending_amount > 0
             ORDER BY f.due_date ASC",
            [$studentId, $academicYearId]
        );
    }

    /**
     * Create fee payment
     */
    public function createPayment($paymentData) {
        // Validate payment data
        $requiredFields = ['student_id', 'fee_id', 'amount_paid', 'payment_date'];

        foreach ($requiredFields as $field) {
            if (!isset($paymentData[$field]) || $paymentData[$field] === '') {
                throw new Exception("Field '{$field}' is required");
            }
        }

        // Set academic year if not provided
        if (!isset($paymentData['academic_year_id'])) {
            $paymentData['academic_year_id'] = $this->getCurrentAcademicYearId();
        }

        // Generate receipt number if not provided
        if (!isset($paymentData['receipt_number'])) {
            $paymentData['receipt_number'] = $this->generateReceiptNumber();
        }

        // Set collected by if not provided
        if (!isset($paymentData['collected_by'])) {
            $user = Security::getCurrentUser();
            $paymentData['collected_by'] = $user ? $user['id'] : 1;
        }

        return $this->db->insert('fee_payments', $paymentData);
    }

    /**
     * Get fee collection summary
     */
    public function getCollectionSummary($academicYearId = null) {
        if (!$academicYearId) {
            $academicYearId = $this->getCurrentAcademicYearId();
        }

        return $this->db->select(
            "SELECT c.class_name, c.section,
                    COUNT(DISTINCT s.id) as total_students,
                    SUM(f.amount) as total_fees,
                    SUM(COALESCE(fp.amount_paid, 0)) as collected_amount,
                    (SUM(f.amount) - SUM(COALESCE(fp.amount_paid, 0))) as pending_amount
             FROM classes c
             JOIN students s ON c.id = s.class_id
             LEFT JOIN fees f ON (f.class_id = c.id OR f.class_id IS NULL) AND f.academic_year_id = ?
             LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.academic_year_id = ?
             WHERE c.academic_year_id = ? AND s.academic_year_id = ?
             GROUP BY c.id
             ORDER BY c.class_name, c.section",
            [$academicYearId, $academicYearId, $academicYearId, $academicYearId]
        );
    }

    /**
     * Get monthly fee collection report
     */
    public function getMonthlyCollectionReport($year = null, $month = null) {
        $academicYearId = $this->getCurrentAcademicYearId();

        if (!$year) {
            $year = date('Y');
        }

        $sql = "SELECT DATE_FORMAT(fp.payment_date, '%Y-%m') as month,
                       SUM(fp.amount_paid) as total_collected,
                       COUNT(DISTINCT fp.student_id) as students_paid,
                       COUNT(fp.id) as total_payments
                FROM fee_payments fp
                WHERE fp.academic_year_id = ? AND YEAR(fp.payment_date) = ?";

        $params = [$academicYearId, $year];

        if ($month) {
            $sql .= " AND MONTH(fp.payment_date) = ?";
            $params[] = $month;
        }

        $sql .= " GROUP BY DATE_FORMAT(fp.payment_date, '%Y-%m')
                  ORDER BY month DESC";

        return $this->db->select($sql, $params);
    }

    /**
     * Get fee payment history
     */
    public function getPaymentHistory($filters = []) {
        $academicYearId = $this->getCurrentAcademicYearId();

        $sql = "SELECT fp.*, f.fee_name, f.fee_type,
                       s.scholar_number, s.first_name, s.last_name,
                       c.class_name, c.section,
                       CONCAT(u.first_name, ' ', u.last_name) as collected_by_name,
                       DATE_FORMAT(fp.payment_date, '%d/%m/%Y') as formatted_date
                FROM fee_payments fp
                JOIN fees f ON fp.fee_id = f.id
                JOIN students s ON fp.student_id = s.id
                JOIN classes c ON s.class_id = c.id
                LEFT JOIN user_profiles u ON fp.collected_by = u.user_id
                WHERE fp.academic_year_id = ?";

        $params = [$academicYearId];

        // Apply filters
        if (!empty($filters['class_id'])) {
            $sql .= " AND s.class_id = ?";
            $params[] = $filters['class_id'];
        }

        if (!empty($filters['fee_type'])) {
            $sql .= " AND f.fee_type = ?";
            $params[] = $filters['fee_type'];
        }

        if (!empty($filters['payment_mode'])) {
            $sql .= " AND fp.payment_mode = ?";
            $params[] = $filters['payment_mode'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND fp.payment_date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND fp.payment_date <= ?";
            $params[] = $filters['date_to'];
        }

        $sql .= " ORDER BY fp.payment_date DESC, fp.id DESC";

        return $this->db->select($sql, $params);
    }

    /**
     * Get fee defaulters
     */
    public function getDefaulters($academicYearId = null) {
        if (!$academicYearId) {
            $academicYearId = $this->getCurrentAcademicYearId();
        }

        return $this->db->select(
            "SELECT s.id, s.scholar_number,
                    CONCAT(s.first_name, ' ', s.last_name) as student_name,
                    c.class_name, c.section, s.roll_number,
                    SUM(f.amount) as total_fees,
                    COALESCE(SUM(fp.amount_paid), 0) as paid_amount,
                    (SUM(f.amount) - COALESCE(SUM(fp.amount_paid), 0)) as pending_amount
             FROM students s
             JOIN classes c ON s.class_id = c.id
             LEFT JOIN fees f ON (f.class_id = c.id OR f.class_id IS NULL) AND f.academic_year_id = ?
             LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.academic_year_id = ?
             WHERE s.academic_year_id = ? AND s.status = 'active'
             GROUP BY s.id
             HAVING pending_amount > 0
             ORDER BY pending_amount DESC, c.class_name, c.section, s.roll_number",
            [$academicYearId, $academicYearId, $academicYearId]
        );
    }

    /**
     * Generate receipt data for printing
     */
    public function generateReceipt($paymentId) {
        $receipt = $this->db->selectOne(
            "SELECT fp.*, f.fee_name, f.fee_type, f.amount as fee_amount,
                    s.scholar_number, s.admission_number,
                    CONCAT(s.first_name, ' ', s.last_name) as student_name,
                    s.father_name, s.mother_name,
                    c.class_name, c.section,
                    ay.year_name,
                    CONCAT(u.first_name, ' ', u.last_name) as collected_by_name,
                    DATE_FORMAT(fp.payment_date, '%d/%m/%Y') as formatted_date
             FROM fee_payments fp
             JOIN fees f ON fp.fee_id = f.id
             JOIN students s ON fp.student_id = s.id
             JOIN classes c ON s.class_id = c.id
             JOIN academic_years ay ON fp.academic_year_id = ay.id
             LEFT JOIN user_profiles u ON fp.collected_by = u.user_id
             WHERE fp.id = ?",
            [$paymentId]
        );

        return $receipt;
    }

    /**
     * Generate next receipt number
     */
    private function generateReceiptNumber() {
        $date = date('Ymd');
        $lastReceipt = $this->db->selectOne(
            "SELECT receipt_number FROM fee_payments
             WHERE receipt_number LIKE ?
             ORDER BY id DESC LIMIT 1",
            [$date . '%']
        );

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt['receipt_number'], -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $date . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get fee statistics
     */
    public function getFeeStats($academicYearId = null) {
        if (!$academicYearId) {
            $academicYearId = $this->getCurrentAcademicYearId();
        }

        $stats = $this->db->selectOne(
            "SELECT
                SUM(f.amount) as total_fees,
                SUM(COALESCE(fp.amount_paid, 0)) as total_collected,
                COUNT(DISTINCT s.id) as total_students,
                COUNT(DISTINCT CASE WHEN (SUM(f.amount) - COALESCE(SUM(fp.amount_paid), 0)) > 0 THEN s.id END) as defaulters
             FROM students s
             JOIN classes c ON s.class_id = c.id
             LEFT JOIN fees f ON (f.class_id = c.id OR f.class_id IS NULL) AND f.academic_year_id = ?
             LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.academic_year_id = ?
             WHERE s.academic_year_id = ? AND s.status = 'active'",
            [$academicYearId, $academicYearId, $academicYearId]
        );

        $stats['pending_amount'] = $stats['total_fees'] - $stats['total_collected'];
        $stats['collection_percentage'] = $stats['total_fees'] > 0 ?
            round(($stats['total_collected'] / $stats['total_fees']) * 100, 2) : 0;

        return $stats;
    }
}
?>