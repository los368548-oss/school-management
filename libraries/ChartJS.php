<?php
/**
 * ChartJS Library Wrapper
 *
 * Wrapper class for Chart.js library to generate charts
 * This is a placeholder - in production, include Chart.js via CDN
 */

class ChartGenerator {
    private $chartData = [];

    /**
     * Generate enrollment chart data
     */
    public static function getEnrollmentChartData() {
        $db = Database::getInstance();
        $academicYearId = Session::getAcademicYear();

        if (!$academicYearId) {
            return self::getEmptyChartData('No Academic Year Selected');
        }

        $data = $db->select(
            "SELECT c.class_name, c.section, COUNT(s.id) as count
             FROM classes c
             LEFT JOIN students s ON c.id = s.class_id AND s.academic_year_id = ?
             WHERE c.academic_year_id = ?
             GROUP BY c.id
             ORDER BY c.class_name, c.section",
            [$academicYearId, $academicYearId]
        );

        $labels = [];
        $counts = [];

        foreach ($data as $row) {
            $labels[] = $row['class_name'] . ' ' . $row['section'];
            $counts[] = (int)$row['count'];
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Students',
                    'data' => $counts,
                    'backgroundColor' => 'rgba(13, 110, 253, 0.8)',
                    'borderColor' => 'rgba(13, 110, 253, 1)',
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true
                    ]
                ]
            ]
        ];
    }

    /**
     * Generate fee collection chart data
     */
    public static function getFeeCollectionChartData() {
        $db = Database::getInstance();
        $academicYearId = Session::getAcademicYear();

        if (!$academicYearId) {
            return self::getEmptyChartData('No Academic Year Selected');
        }

        // Get monthly fee collection for last 6 months
        $data = $db->select(
            "SELECT DATE_FORMAT(payment_date, '%M %Y') as month,
                    SUM(amount_paid) as collected
             FROM fee_payments
             WHERE academic_year_id = ? AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY YEAR(payment_date), MONTH(payment_date)
             ORDER BY payment_date",
            [$academicYearId]
        );

        $labels = [];
        $amounts = [];

        foreach ($data as $row) {
            $labels[] = $row['month'];
            $amounts[] = (float)$row['collected'];
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Collected (₹)',
                    'data' => $amounts,
                    'borderColor' => 'rgba(25, 135, 84, 1)',
                    'backgroundColor' => 'rgba(25, 135, 84, 0.1)',
                    'tension' => 0.4
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false
            ]
        ];
    }

    /**
     * Generate attendance chart data
     */
    public static function getAttendanceChartData() {
        $db = Database::getInstance();
        $academicYearId = Session::getAcademicYear();

        if (!$academicYearId) {
            return self::getEmptyChartData('No Academic Year Selected');
        }

        // Get attendance summary for current month
        $data = $db->selectOne(
            "SELECT
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late
             FROM attendance
             WHERE academic_year_id = ? AND MONTH(attendance_date) = MONTH(CURDATE())",
            [$academicYearId]
        );

        $present = (int)($data['present'] ?? 0);
        $absent = (int)($data['absent'] ?? 0);
        $late = (int)($data['late'] ?? 0);

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Present', 'Absent', 'Late'],
                'datasets' => [[
                    'data' => [$present, $absent, $late],
                    'backgroundColor' => [
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ]
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false
            ]
        ];
    }

    /**
     * Generate exam performance chart data
     */
    public static function getExamPerformanceChartData() {
        $db = Database::getInstance();
        $academicYearId = Session::getAcademicYear();

        if (!$academicYearId) {
            return self::getEmptyChartData('No Academic Year Selected');
        }

        // Get average scores by subject for latest exam
        $data = $db->select(
            "SELECT s.subject_name,
                    ROUND(AVG((er.marks_obtained / es.max_marks) * 100), 1) as average_score
             FROM exam_results er
             JOIN subjects s ON er.subject_id = s.id
             JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
             JOIN exams e ON er.exam_id = e.id
             WHERE e.academic_year_id = ?
             GROUP BY s.id
             ORDER BY s.subject_name
             LIMIT 8",
            [$academicYearId]
        );

        $labels = [];
        $scores = [];

        foreach ($data as $row) {
            $labels[] = $row['subject_name'];
            $scores[] = (float)$row['average_score'];
        }

        return [
            'type' => 'radar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Average Score (%)',
                    'data' => $scores,
                    'borderColor' => 'rgba(13, 110, 253, 1)',
                    'backgroundColor' => 'rgba(13, 110, 253, 0.2)'
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'r' => [
                        'beginAtZero' => true,
                        'max' => 100
                    ]
                ]
            ]
        ];
    }

    /**
     * Generate student growth chart data
     */
    public static function getStudentGrowthChartData() {
        $db = Database::getInstance();

        // Get student count by month for last 12 months
        $data = $db->select(
            "SELECT DATE_FORMAT(created_at, '%M %Y') as month,
                    COUNT(*) as count
             FROM students
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY YEAR(created_at), MONTH(created_at)
             ORDER BY created_at"
        );

        $labels = [];
        $counts = [];

        foreach ($data as $row) {
            $labels[] = $row['month'];
            $counts[] = (int)$row['count'];
        }

        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'New Students',
                    'data' => $counts,
                    'borderColor' => 'rgba(23, 162, 184, 1)',
                    'backgroundColor' => 'rgba(23, 162, 184, 0.1)',
                    'tension' => 0.4
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false
            ]
        ];
    }

    /**
     * Generate revenue chart data
     */
    public static function getRevenueChartData() {
        $db = Database::getInstance();
        $academicYearId = Session::getAcademicYear();

        if (!$academicYearId) {
            return self::getEmptyChartData('No Academic Year Selected');
        }

        // Get monthly revenue (fees + expenses)
        $data = $db->select(
            "SELECT DATE_FORMAT(payment_date, '%M %Y') as month,
                    SUM(amount_paid) as income,
                    (SELECT SUM(amount) FROM expenses WHERE DATE_FORMAT(expense_date, '%M %Y') = month) as expenses
             FROM fee_payments
             WHERE academic_year_id = ? AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY YEAR(payment_date), MONTH(payment_date)
             ORDER BY payment_date",
            [$academicYearId]
        );

        $labels = [];
        $income = [];
        $expenses = [];

        foreach ($data as $row) {
            $labels[] = $row['month'];
            $income[] = (float)$row['income'];
            $expenses[] = (float)($row['expenses'] ?? 0);
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Income (₹)',
                        'data' => $income,
                        'backgroundColor' => 'rgba(25, 135, 84, 0.8)',
                        'borderColor' => 'rgba(25, 135, 84, 1)',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Expenses (₹)',
                        'data' => $expenses,
                        'backgroundColor' => 'rgba(220, 53, 69, 0.8)',
                        'borderColor' => 'rgba(220, 53, 69, 1)',
                        'borderWidth' => 1
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true
                    ]
                ]
            ]
        ];
    }

    /**
     * Generate empty chart data
     */
    private static function getEmptyChartData($message = 'No Data Available') {
        return [
            'type' => 'bar',
            'data' => [
                'labels' => [$message],
                'datasets' => [[
                    'label' => 'Data',
                    'data' => [0],
                    'backgroundColor' => 'rgba(108, 117, 125, 0.8)',
                    'borderColor' => 'rgba(108, 117, 125, 1)',
                    'borderWidth' => 1
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true
                    ]
                ]
            ]
        ];
    }

    /**
     * Render chart as JSON for JavaScript
     */
    public static function renderChart($chartType) {
        $method = 'get' . ucfirst($chartType) . 'ChartData';
        if (method_exists(self::class, $method)) {
            return json_encode(self::$method());
        }
        return json_encode(self::getEmptyChartData('Chart type not found'));
    }
}
?>