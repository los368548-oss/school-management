<?php
/**
 * Report Model
 */

class Report extends BaseModel {
    protected $table = 'audit_logs'; // Using audit_logs as base for reports

    /**
     * Generate student report
     */
    public function generateStudentReport($filters = []) {
        $whereClause = 's.is_active = 1';
        $params = [];

        if (!empty($filters['class_id'])) {
            $whereClause .= ' AND s.class_id = ?';
            $params[] = $filters['class_id'];
        }

        if (!empty($filters['academic_year'])) {
            $whereClause .= ' AND s.admission_date >= ? AND s.admission_date <= ?';
            $params[] = $filters['academic_year'] . '-04-01';
            $params[] = ($filters['academic_year'] + 1) . '-03-31';
        }

        $sql = "
            SELECT
                s.*,
                c.name as class_name,
                c.section as class_section,
                COALESCE(att_stats.present_days, 0) as present_days,
                COALESCE(att_stats.total_days, 0) as total_attendance_days,
                ROUND(COALESCE(att_stats.attendance_percentage, 0), 2) as attendance_percentage,
                COALESCE(fee_stats.total_fees, 0) as total_fees,
                COALESCE(fee_stats.paid_amount, 0) as paid_amount,
                COALESCE(fee_stats.pending_amount, 0) as pending_amount
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN (
                SELECT
                    student_id,
                    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_days,
                    COUNT(*) as total_days,
                    (SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as attendance_percentage
                FROM attendance
                WHERE MONTH(date) = MONTH(CURRENT_DATE) AND YEAR(date) = YEAR(CURRENT_DATE)
                GROUP BY student_id
            ) att_stats ON s.id = att_stats.student_id
            LEFT JOIN (
                SELECT
                    s2.id as student_id,
                    SUM(f.amount) as total_fees,
                    SUM(COALESCE(fp.amount, 0)) as paid_amount,
                    (SUM(f.amount) - SUM(COALESCE(fp.amount, 0))) as pending_amount
                FROM students s2
                LEFT JOIN fees f ON s2.class_id = f.class_id
                LEFT JOIN fee_payments fp ON s2.id = fp.student_id AND fp.fee_id = f.id
                GROUP BY s2.id
            ) fee_stats ON s.id = fee_stats.student_id
            WHERE {$whereClause}
            ORDER BY c.name ASC, c.section ASC, s.first_name ASC, s.last_name ASC
        ";

        $stmt = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }

        return $stmt->resultSet();
    }

    /**
     * Generate attendance report
     */
    public function generateAttendanceReport($filters = []) {
        $whereClause = 's.is_active = 1';
        $params = [];

        if (!empty($filters['class_id'])) {
            $whereClause .= ' AND s.class_id = ?';
            $params[] = $filters['class_id'];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $whereClause .= ' AND a.date BETWEEN ? AND ?';
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        }

        $sql = "
            SELECT
                s.scholar_number,
                s.first_name,
                s.last_name,
                c.name as class_name,
                c.section as class_section,
                COUNT(a.id) as total_days,
                SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN a.status = 'Late' THEN 1 ELSE 0 END) as late_days,
                ROUND(
                    (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.id)) * 100,
                    2
                ) as attendance_percentage
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN attendance a ON s.id = a.student_id
            WHERE {$whereClause}
            GROUP BY s.id, s.scholar_number, s.first_name, s.last_name, c.name, c.section
            ORDER BY c.name ASC, c.section ASC, s.first_name ASC, s.last_name ASC
        ";

        $stmt = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }

        return $stmt->resultSet();
    }

    /**
     * Generate fee collection report
     */
    public function generateFeeReport($filters = []) {
        $whereClause = 's.is_active = 1';
        $params = [];

        if (!empty($filters['class_id'])) {
            $whereClause .= ' AND s.class_id = ?';
            $params[] = $filters['class_id'];
        }

        if (!empty($filters['academic_year'])) {
            $whereClause .= ' AND f.academic_year = ?';
            $params[] = $filters['academic_year'];
        }

        $sql = "
            SELECT
                s.scholar_number,
                s.first_name,
                s.last_name,
                c.name as class_name,
                c.section as class_section,
                f.fee_type,
                f.amount as fee_amount,
                COALESCE(fp.amount, 0) as paid_amount,
                (f.amount - COALESCE(fp.amount, 0)) as pending_amount,
                fp.payment_date,
                fp.receipt_number
            FROM students s
            CROSS JOIN fees f
            LEFT JOIN classes c ON s.class_id = c.id
            LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.fee_id = f.id
            WHERE {$whereClause}
            ORDER BY c.name ASC, c.section ASC, s.first_name ASC, s.last_name ASC, f.fee_type ASC
        ";

        $stmt = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }

        return $stmt->resultSet();
    }

    /**
     * Generate exam results report
     */
    public function generateExamReport($examId = null) {
        if (!$examId) {
            // Get all exams with summary
            $sql = "
                SELECT
                    e.id,
                    e.name as exam_name,
                    e.type as exam_type,
                    c.name as class_name,
                    c.section as class_section,
                    COUNT(er.id) as total_results,
                    AVG((er.marks_obtained / er.total_marks) * 100) as average_percentage,
                    MAX((er.marks_obtained / er.total_marks) * 100) as highest_percentage,
                    MIN((er.marks_obtained / er.total_marks) * 100) as lowest_percentage
                FROM exams e
                LEFT JOIN classes c ON e.class_id = c.id
                LEFT JOIN exam_results er ON e.id = er.exam_id
                GROUP BY e.id, e.name, e.type, c.name, c.section
                ORDER BY e.start_date DESC
            ";

            return $this->db->query($sql)->resultSet();
        } else {
            // Get detailed results for specific exam
            $sql = "
                SELECT
                    s.scholar_number,
                    s.first_name,
                    s.last_name,
                    GROUP_CONCAT(
                        CONCAT(sub.name, ': ', COALESCE(er.marks_obtained, 0), '/', COALESCE(er.total_marks, 0))
                        ORDER BY sub.name
                        SEPARATOR '; '
                    ) as subject_marks,
                    SUM(COALESCE(er.marks_obtained, 0)) as total_marks_obtained,
                    SUM(COALESCE(er.total_marks, 0)) as total_marks,
                    ROUND(
                        (SUM(COALESCE(er.marks_obtained, 0)) / SUM(COALESCE(er.total_marks, 0))) * 100,
                        2
                    ) as percentage,
                    er.grade
                FROM students s
                LEFT JOIN exam_results er ON s.id = er.student_id AND er.exam_id = ?
                LEFT JOIN subjects sub ON er.subject_id = sub.id
                WHERE s.class_id = (SELECT class_id FROM exams WHERE id = ?)
                GROUP BY s.id, s.scholar_number, s.first_name, s.last_name, er.grade
                ORDER BY percentage DESC, s.first_name ASC, s.last_name ASC
            ";

            $stmt = $this->db->query($sql);
            $stmt->bind(1, $examId);
            $stmt->bind(2, $examId);

            return $stmt->resultSet();
        }
    }

    /**
     * Generate audit log report
     */
    public function generateAuditReport($filters = []) {
        $whereClause = '1=1';
        $params = [];

        if (!empty($filters['user_id'])) {
            $whereClause .= ' AND al.user_id = ?';
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $whereClause .= ' AND al.action = ?';
            $params[] = $filters['action'];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $whereClause .= ' AND al.timestamp BETWEEN ? AND ?';
            $params[] = $filters['start_date'] . ' 00:00:00';
            $params[] = $filters['end_date'] . ' 23:59:59';
        }

        $sql = "
            SELECT
                al.*,
                u.username as user_name,
                u.email as user_email
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE {$whereClause}
            ORDER BY al.timestamp DESC
        ";

        $stmt = $this->db->query($sql);
        foreach ($params as $index => $param) {
            $stmt->bind($index + 1, $param);
        }

        return $stmt->resultSet();
    }

    /**
     * Export data to CSV
     */
    public function exportToCSV($data, $filename) {
        if (empty($data)) {
            return false;
        }

        $output = fopen('php://temp', 'w');

        // Write headers
        fputcsv($output, array_keys($data[0]));

        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        // Set headers for download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($csvContent));

        echo $csvContent;
        exit;
    }

    /**
     * Generate PDF report (placeholder - would need TCPDF or similar library)
     */
    public function generatePDF($data, $title, $filename) {
        // This would require a PDF library like TCPDF
        // For now, return false
        return false;
    }
}
?>