<?php
/**
 * Report Controller
 *
 * Handles admin reporting operations
 */

class ReportController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    public function index() {
        $this->reports();
    }

    public function reports() {
        // Check if academic year is set
        $academicYearId = $this->getCurrentAcademicYear();
        if (!$academicYearId) {
            $this->redirect('/admin/select-academic-year');
        }

        $data = [
            'academic_year' => $this->getAcademicYearInfo($academicYearId),
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/reports/reports', $data);
    }

    public function generateStudentReport() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/reports');
        }

        $postData = $this->getPostData();

        if (!Security::validateCSRFToken($postData['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid security token');
            $this->redirect('/admin/reports');
        }

        $academicYearId = $this->getCurrentAcademicYear();
        if (!$academicYearId) {
            $this->redirect('/admin/select-academic-year');
        }

        $classId = $postData['class_id'] ?? null;
        $startDate = $postData['start_date'] ?? null;
        $endDate = $postData['end_date'] ?? null;

        $students = $this->db->select(
            "SELECT s.*, c.class_name, c.section
             FROM students s
             JOIN classes c ON s.class_id = c.id
             WHERE s.academic_year_id = ? " .
             ($classId ? "AND s.class_id = ?" : "") .
             " ORDER BY c.class_name, c.section, s.roll_number",
            $classId ? [$academicYearId, $classId] : [$academicYearId]
        );

        // Generate PDF or Excel based on format
        $format = $postData['format'] ?? 'pdf';

        if ($format === 'excel') {
            $this->generateExcelReport($students, 'student_report');
        } else {
            $this->generatePDFReport($students, 'student_report');
        }
    }

    public function generateAttendanceReport() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/reports');
        }

        $postData = $this->getPostData();

        if (!Security::validateCSRFToken($postData['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid security token');
            $this->redirect('/admin/reports');
        }

        $academicYearId = $this->getCurrentAcademicYear();
        if (!$academicYearId) {
            $this->redirect('/admin/select-academic-year');
        }

        $classId = $postData['class_id'] ?? null;
        $startDate = $postData['start_date'] ?? date('Y-m-01');
        $endDate = $postData['end_date'] ?? date('Y-m-t');

        $attendanceModel = $this->loadModel('Admin/Attendance/Attendance');
        $report = $attendanceModel->getClassReport($classId, $startDate, $endDate);

        $format = $postData['format'] ?? 'pdf';

        if ($format === 'excel') {
            $this->generateExcelReport($report, 'attendance_report');
        } else {
            $this->generatePDFReport($report, 'attendance_report');
        }
    }

    public function generateFeeReport() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/reports');
        }

        $postData = $this->getPostData();

        if (!Security::validateCSRFToken($postData['csrf_token'] ?? '')) {
            $this->setFlashMessage('error', 'Invalid security token');
            $this->redirect('/admin/reports');
        }

        $academicYearId = $this->getCurrentAcademicYear();
        if (!$academicYearId) {
            $this->redirect('/admin/select-academic-year');
        }

        $startDate = $postData['start_date'] ?? null;
        $endDate = $postData['end_date'] ?? null;

        $fees = $this->db->select(
            "SELECT fp.*, s.scholar_number, CONCAT(s.first_name, ' ', s.last_name) as student_name,
                    f.fee_name, f.amount as fee_amount, fp.amount_paid, fp.payment_date
             FROM fee_payments fp
             JOIN students s ON fp.student_id = s.id
             JOIN fees f ON fp.fee_id = f.id
             WHERE fp.academic_year_id = ? " .
             ($startDate && $endDate ? "AND fp.payment_date BETWEEN ? AND ?" : "") .
             " ORDER BY fp.payment_date DESC",
            $startDate && $endDate ? [$academicYearId, $startDate, $endDate] : [$academicYearId]
        );

        $format = $postData['format'] ?? 'pdf';

        if ($format === 'excel') {
            $this->generateExcelReport($fees, 'fee_report');
        } else {
            $this->generatePDFReport($fees, 'fee_report');
        }
    }

    private function generatePDFReport($data, $type) {
        // Use TCPDF library for PDF generation
        require_once BASE_PATH . 'libraries/TCPDF/tcpdf.php';

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator('School Management System');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle(ucfirst(str_replace('_', ' ', $type)));

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);

        $pdf->AddPage();

        // Add content based on type
        $html = $this->generateReportHTML($data, $type);
        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->Output($type . '_' . date('Y-m-d') . '.pdf', 'D');
    }

    private function generateExcelReport($data, $type) {
        // Simple CSV export for now
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $type . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        if (!empty($data)) {
            // Write headers
            fputcsv($output, array_keys($data[0]));

            // Write data
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }

    private function generateReportHTML($data, $type) {
        $html = '<h1>' . ucfirst(str_replace('_', ' ', $type)) . ' Report</h1>';
        $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';

        if (!empty($data)) {
            $html .= '<table border="1" cellpadding="5">';
            $html .= '<thead><tr>';

            foreach (array_keys($data[0]) as $header) {
                $html .= '<th>' . htmlspecialchars(ucfirst(str_replace('_', ' ', $header))) . '</th>';
            }

            $html .= '</tr></thead><tbody>';

            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ($row as $value) {
                    $html .= '<td>' . htmlspecialchars($value) . '</td>';
                }
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No data found for the selected criteria.</p>';
        }

        return $html;
    }
}
?>