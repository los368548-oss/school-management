<?php
/**
 * Fee Model
 */

class Fee extends BaseModel {
    protected $table = 'fees';
    protected $fillable = ['class_id', 'fee_type', 'amount', 'academic_year', 'due_date', 'description', 'created_by'];

    /**
     * Get fee with class information
     */
    public function findWithClass($id) {
        $result = $this->db->query("
            SELECT f.*, c.name as class_name, c.section as class_section,
                   u.username as created_by_name
            FROM {$this->table} f
            LEFT JOIN classes c ON f.class_id = c.id
            LEFT JOIN users u ON f.created_by = u.id
            WHERE f.{$this->primaryKey} = ?
        ")->bind(1, $id)->single();

        return $result ? $this->processResult($result) : null;
    }

    /**
     * Get fees by class
     */
    public function getFeesByClass($classId) {
        $results = $this->db->query("
            SELECT f.*, u.username as created_by_name
            FROM {$this->table} f
            LEFT JOIN users u ON f.created_by = u.id
            WHERE f.class_id = ?
            ORDER BY f.fee_type ASC
        ")->bind(1, $classId)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Get student fee status
     */
    public function getStudentFeeStatus($studentId) {
        $result = $this->db->query("
            SELECT
                COALESCE(SUM(f.amount), 0) as total_fees,
                COALESCE(SUM(fp.amount), 0) as paid_amount,
                (COALESCE(SUM(f.amount), 0) - COALESCE(SUM(fp.amount), 0)) as pending_amount,
                COUNT(DISTINCT f.id) as fee_count,
                COUNT(DISTINCT fp.id) as payment_count
            FROM students s
            LEFT JOIN {$this->table} f ON s.class_id = f.class_id
            LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.fee_id = f.id
            WHERE s.id = ?
        ")->bind(1, $studentId)->single();

        return $result ?: [
            'total_fees' => 0,
            'paid_amount' => 0,
            'pending_amount' => 0,
            'fee_count' => 0,
            'payment_count' => 0
        ];
    }

    /**
     * Record fee payment
     */
    public function recordPayment($paymentData) {
        $feePaymentModel = new FeePayment();
        return $feePaymentModel->create($paymentData);
    }

    /**
     * Get fee collection summary
     */
    public function getFeeCollectionSummary($classId = null, $academicYear = null) {
        $whereClause = '1=1';
        $params = [];

        if ($classId) {
            $whereClause .= ' AND s.class_id = ?';
            $params[] = $classId;
        }

        if ($academicYear) {
            $whereClause .= ' AND f.academic_year = ?';
            $params[] = $academicYear;
        }

        $sql = "
            SELECT
                SUM(f.amount) as total_fees,
                SUM(COALESCE(fp.amount, 0)) as collected_amount,
                (SUM(f.amount) - SUM(COALESCE(fp.amount, 0))) as pending_amount,
                COUNT(DISTINCT s.id) as total_students,
                COUNT(DISTINCT CASE WHEN COALESCE(fp.amount, 0) >= f.amount THEN s.id END) as paid_students
            FROM students s
            CROSS JOIN {$this->table} f
            LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.fee_id = f.id
            WHERE {$whereClause} AND s.is_active = 1
        ";

        $stmt = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }

        $result = $stmt->single();

        if ($result) {
            $result['collection_percentage'] = $result['total_fees'] > 0
                ? round(($result['collected_amount'] / $result['total_fees']) * 100, 2)
                : 0;
        }

        return $result ?: [
            'total_fees' => 0,
            'collected_amount' => 0,
            'pending_amount' => 0,
            'total_students' => 0,
            'paid_students' => 0,
            'collection_percentage' => 0
        ];
    }

    /**
     * Get overdue fees
     */
    public function getOverdueFees($days = 30) {
        $dueDate = date('Y-m-d', strtotime("-{$days} days"));

        $results = $this->db->query("
            SELECT
                s.id as student_id,
                s.scholar_number,
                s.first_name,
                s.last_name,
                c.name as class_name,
                c.section,
                f.fee_type,
                f.amount,
                f.due_date,
                DATEDIFF(CURDATE(), f.due_date) as days_overdue,
                COALESCE(SUM(fp.amount), 0) as paid_amount,
                (f.amount - COALESCE(SUM(fp.amount), 0)) as pending_amount
            FROM {$this->table} f
            JOIN students s ON f.class_id = s.class_id
            JOIN classes c ON f.class_id = c.id
            LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.fee_id = f.id
            WHERE f.due_date < ? AND s.is_active = 1
            GROUP BY s.id, s.scholar_number, s.first_name, s.last_name, c.name, c.section, f.fee_type, f.amount, f.due_date
            HAVING pending_amount > 0
            ORDER BY days_overdue DESC, s.first_name ASC
        ")->bind(1, $dueDate)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Generate fee receipt
     */
    public function generateReceipt($paymentId) {
        $result = $this->db->query("
            SELECT
                fp.*,
                s.scholar_number,
                s.first_name,
                s.last_name,
                c.name as class_name,
                c.section,
                f.fee_type,
                f.academic_year,
                u.username as received_by_name
            FROM fee_payments fp
            JOIN students s ON fp.student_id = s.id
            JOIN fees f ON fp.fee_id = f.id
            JOIN classes c ON s.class_id = c.id
            LEFT JOIN users u ON fp.received_by = u.id
            WHERE fp.id = ?
        ")->bind(1, $paymentId)->single();

        return $result ? $this->processResult($result) : null;
    }
}

/**
 * Fee Payment Model
 */
class FeePayment extends BaseModel {
    protected $table = 'fee_payments';
    protected $fillable = ['student_id', 'fee_id', 'amount', 'payment_date', 'payment_mode', 'transaction_id', 'cheque_number', 'receipt_number', 'remarks', 'received_by'];

    /**
     * Get payments by student
     */
    public function getPaymentsByStudent($studentId) {
        $results = $this->db->query("
            SELECT fp.*, f.fee_type, f.academic_year, u.username as received_by_name
            FROM {$this->table} fp
            JOIN fees f ON fp.fee_id = f.id
            LEFT JOIN users u ON fp.received_by = u.id
            WHERE fp.student_id = ?
            ORDER BY fp.payment_date DESC
        ")->bind(1, $studentId)->resultSet();

        return array_map([$this, 'processResult'], $results);
    }

    /**
     * Generate unique receipt number
     */
    public function generateReceiptNumber() {
        $date = date('Ymd');
        $result = $this->db->query("
            SELECT COUNT(*) as count FROM {$this->table}
            WHERE DATE(payment_date) = CURDATE()
        ")->single();

        $sequence = str_pad(($result['count'] ?? 0) + 1, 4, '0', STR_PAD_LEFT);
        return "RCP{$date}{$sequence}";
    }
}
?>