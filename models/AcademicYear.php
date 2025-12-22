<?php
/**
 * Academic Year Model
 *
 * Handles academic year-related database operations
 */

class AcademicYear extends BaseModel {
    protected $table = 'academic_years';
    protected $fillable = [
        'year_name', 'start_date', 'end_date', 'is_active'
    ];

    /**
     * Get active academic year
     */
    public function getActive() {
        $result = $this->db->selectOne("SELECT * FROM {$this->table} WHERE is_active = 1");
        return $result ? $this->createInstance($result) : null;
    }

    /**
     * Set active academic year
     */
    public function setActive($yearId) {
        $this->db->beginTransaction();

        try {
            // Deactivate all years
            $this->db->query("UPDATE {$this->table} SET is_active = 0");

            // Activate selected year
            $this->db->query("UPDATE {$this->table} SET is_active = 1 WHERE id = ?", [$yearId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    /**
     * Get all academic years ordered by start date
     */
    public function getAllOrdered() {
        $results = $this->db->select("SELECT * FROM {$this->table} ORDER BY start_date DESC");
        return array_map([$this, 'createInstance'], $results);
    }

    /**
     * Check if date falls within academic year
     */
    public function isDateInYear($yearId, $date) {
        $year = $this->find($yearId);
        if (!$year) {
            return false;
        }

        $checkDate = strtotime($date);
        $startDate = strtotime($year->start_date);
        $endDate = strtotime($year->end_date);

        return $checkDate >= $startDate && $checkDate <= $endDate;
    }

    /**
     * Get current academic year based on today's date
     */
    public function getCurrentByDate() {
        $today = date('Y-m-d');
        $result = $this->db->selectOne(
            "SELECT * FROM {$this->table}
             WHERE start_date <= ? AND end_date >= ?
             ORDER BY start_date DESC
             LIMIT 1",
            [$today, $today]
        );
        return $result ? $this->createInstance($result) : null;
    }

    /**
     * Create new academic year
     */
    public function createYear($yearData) {
        // Validate year data
        if (empty($yearData['year_name']) || empty($yearData['start_date']) || empty($yearData['end_date'])) {
            throw new Exception('Year name, start date, and end date are required');
        }

        // Check for overlapping years
        $overlap = $this->db->selectOne(
            "SELECT id FROM {$this->table}
             WHERE (start_date <= ? AND end_date >= ?) OR
                   (start_date <= ? AND end_date >= ?) OR
                   (start_date >= ? AND end_date <= ?)",
            [
                $yearData['end_date'], $yearData['start_date'],
                $yearData['start_date'], $yearData['end_date'],
                $yearData['start_date'], $yearData['end_date']
            ]
        );

        if ($overlap) {
            throw new Exception('Academic year dates overlap with existing year');
        }

        return $this->create($yearData);
    }

    /**
     * Get academic year statistics
     */
    public function getStats($yearId) {
        $stats = [];

        // Student count
        $stats['students'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM students WHERE academic_year_id = ?",
            [$yearId]
        )['count'];

        // Class count
        $stats['classes'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM classes WHERE academic_year_id = ?",
            [$yearId]
        )['count'];

        // Exam count
        $stats['exams'] = $this->db->selectOne(
            "SELECT COUNT(*) as count FROM exams WHERE academic_year_id = ?",
            [$yearId]
        )['count'];

        // Fee collection
        $feeStats = $this->db->selectOne(
            "SELECT SUM(amount) as total_fees, SUM(COALESCE(fp.amount_paid, 0)) as collected
             FROM fees f
             LEFT JOIN fee_payments fp ON f.id = fp.fee_id
             WHERE f.academic_year_id = ?",
            [$yearId]
        );

        $stats['total_fees'] = $feeStats['total_fees'] ?? 0;
        $stats['collected_fees'] = $feeStats['collected'] ?? 0;
        $stats['pending_fees'] = $stats['total_fees'] - $stats['collected_fees'];

        return $stats;
    }
}
?>