<?php
/**
 * Print Controller
 *
 * Handles PDF generation for certificates, marksheets, admit cards, and receipts
 */

class PrintController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
    }

    /**
     * Generate admit card
     */
    public function admitCard($examId = null, $studentId = null) {
        if (!$examId) {
            $examId = $_GET['exam_id'] ?? null;
        }
        if (!$studentId) {
            $studentId = $_GET['student_id'] ?? null;
        }

        if (!$examId) {
            $this->json(['error' => 'Exam ID is required'], 400);
        }

        try {
            if ($studentId) {
                // Single admit card
                $admitCard = $this->generateSingleAdmitCard($examId, $studentId);
                $this->outputPDF($admitCard, 'admit_card_' . $studentId . '.pdf');
            } else {
                // Bulk admit cards for class
                $classId = $_GET['class_id'] ?? null;
                if (!$classId) {
                    $this->json(['error' => 'Class ID is required for bulk generation'], 400);
                }
                $admitCards = $this->generateBulkAdmitCards($examId, $classId);
                $this->outputPDF($admitCards, 'admit_cards_class_' . $classId . '.pdf');
            }
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to generate admit card: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate marksheet
     */
    public function marksheet($examId = null, $studentId = null) {
        if (!$examId) {
            $examId = $_GET['exam_id'] ?? null;
        }
        if (!$studentId) {
            $studentId = $_GET['student_id'] ?? null;
        }

        if (!$examId || !$studentId) {
            $this->json(['error' => 'Exam ID and Student ID are required'], 400);
        }

        try {
            $marksheet = $this->generateMarksheet($examId, $studentId);
            $this->outputPDF($marksheet, 'marksheet_' . $studentId . '.pdf');
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to generate marksheet: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate transfer certificate
     */
    public function transferCertificate($studentId = null) {
        if (!$studentId) {
            $studentId = $_GET['student_id'] ?? null;
        }

        if (!$studentId) {
            $this->json(['error' => 'Student ID is required'], 400);
        }

        try {
            $tc = $this->generateTransferCertificate($studentId);
            $this->outputPDF($tc, 'transfer_certificate_' . $studentId . '.pdf');
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to generate transfer certificate: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate fee receipt
     */
    public function feeReceipt($paymentId = null) {
        if (!$paymentId) {
            $paymentId = $_GET['payment_id'] ?? null;
        }

        if (!$paymentId) {
            $this->json(['error' => 'Payment ID is required'], 400);
        }

        try {
            $receipt = $this->generateFeeReceipt($paymentId);
            $this->outputPDF($receipt, 'fee_receipt_' . $paymentId . '.pdf');
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to generate fee receipt: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate ID card
     */
    public function idCard($studentId = null) {
        if (!$studentId) {
            $studentId = $_GET['student_id'] ?? null;
        }

        if (!$studentId) {
            $this->json(['error' => 'Student ID is required'], 400);
        }

        try {
            $idCard = $this->generateIdCard($studentId);
            $this->outputPDF($idCard, 'id_card_' . $studentId . '.pdf');
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to generate ID card: ' . $e->getMessage()], 500);
        }
    }

    // Private methods for PDF generation

    private function generateSingleAdmitCard($examId, $studentId) {
        // Get exam and student details
        $exam = $this->db->selectOne(
            "SELECT e.*, c.class_name, c.section
             FROM exams e
             JOIN classes c ON e.class_id = c.id
             WHERE e.id = ?",
            [$examId]
        );

        $student = $this->db->selectOne(
            "SELECT s.*, c.class_name, c.section
             FROM students s
             JOIN classes c ON s.class_id = c.id
             WHERE s.id = ?",
            [$studentId]
        );

        // Get exam subjects
        $subjects = $this->db->select(
            "SELECT es.*, s.subject_name, s.subject_code
             FROM exam_subjects es
             JOIN subjects s ON es.subject_id = s.id
             WHERE es.exam_id = ?
             ORDER BY es.exam_date, es.start_time",
            [$examId]
        );

        return $this->createAdmitCardPDF($exam, $student, $subjects);
    }

    private function generateBulkAdmitCards($examId, $classId) {
        // Get exam details
        $exam = $this->db->selectOne(
            "SELECT e.*, c.class_name, c.section
             FROM exams e
             JOIN classes c ON e.class_id = c.id
             WHERE e.id = ?",
            [$examId]
        );

        // Get all students in class
        $students = $this->db->select(
            "SELECT s.*, c.class_name, c.section
             FROM students s
             JOIN classes c ON s.class_id = c.id
             WHERE s.class_id = ? AND s.academic_year_id = ?
             ORDER BY s.roll_number",
            [$classId, $this->getCurrentAcademicYear()]
        );

        // Get exam subjects
        $subjects = $this->db->select(
            "SELECT es.*, s.subject_name, s.subject_code
             FROM exam_subjects es
             JOIN subjects s ON es.subject_id = s.id
             WHERE es.exam_id = ?
             ORDER BY es.exam_date, es.start_time",
            [$examId]
        );

        $pdf = new TCPDF('P', 'mm', 'A4');
        $pdf->SetCreator('School Management System');
        $pdf->SetAuthor('School Admin');
        $pdf->SetTitle('Admit Cards - ' . $exam['exam_name']);

        foreach ($students as $student) {
            $pdf->AddPage();
            $this->drawAdmitCardContent($pdf, $exam, $student, $subjects);
        }

        return $pdf;
    }

    private function generateMarksheet($examId, $studentId) {
        // Get exam details
        $exam = $this->db->selectOne(
            "SELECT e.*, c.class_name, c.section
             FROM exams e
             JOIN classes c ON e.class_id = c.id
             WHERE e.id = ?",
            [$examId]
        );

        // Get student details
        $student = $this->db->selectOne(
            "SELECT s.*, c.class_name, c.section
             FROM students s
             JOIN classes c ON s.class_id = c.id
             WHERE s.id = ?",
            [$studentId]
        );

        // Get exam results
        $results = $this->db->select(
            "SELECT er.*, s.subject_name, s.subject_code, es.max_marks, es.passing_marks
             FROM exam_results er
             JOIN subjects s ON er.subject_id = s.id
             JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
             WHERE er.exam_id = ? AND er.student_id = ?
             ORDER BY s.subject_name",
            [$examId, $studentId]
        );

        return $this->createMarksheetPDF($exam, $student, $results);
    }

    private function generateTransferCertificate($studentId) {
        // Get student details with academic record
        $student = $this->db->selectOne(
            "SELECT s.*, c.class_name, c.section,
                    ay.year_name,
                    CONCAT(s.first_name, ' ', s.last_name) as full_name
             FROM students s
             JOIN classes c ON s.class_id = c.id
             JOIN academic_years ay ON s.academic_year_id = ay.id
             WHERE s.id = ?",
            [$studentId]
        );

        // Get attendance summary
        $attendance = $this->db->selectOne(
            "SELECT COUNT(*) as total_days,
                    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days
             FROM attendance
             WHERE student_id = ? AND academic_year_id = ?",
            [$studentId, $student['academic_year_id']]
        );

        // Get academic performance (last exam)
        $performance = $this->db->selectOne(
            "SELECT AVG((er.marks_obtained / es.max_marks) * 100) as average_percentage
             FROM exam_results er
             JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
             JOIN exams e ON er.exam_id = e.id
             WHERE er.student_id = ? AND e.academic_year_id = ?
             ORDER BY e.end_date DESC LIMIT 1",
            [$studentId, $student['academic_year_id']]
        );

        return $this->createTCPDF($student, $attendance, $performance);
    }

    private function generateFeeReceipt($paymentId) {
        $feeModel = new Fee();
        $receiptData = $feeModel->generateReceipt($paymentId);

        return $this->createFeeReceiptPDF($receiptData);
    }

    private function generateIdCard($studentId) {
        $student = $this->db->selectOne(
            "SELECT s.*, c.class_name, c.section,
                    ay.year_name,
                    CONCAT(s.first_name, ' ', s.last_name) as full_name
             FROM students s
             JOIN classes c ON s.class_id = c.id
             JOIN academic_years ay ON s.academic_year_id = ay.id
             WHERE s.id = ?",
            [$studentId]
        );

        return $this->createIdCardPDF($student);
    }

    // PDF creation methods

    private function createAdmitCardPDF($exam, $student, $subjects) {
        $pdf = new TCPDF('P', 'mm', 'A4');
        $pdf->SetCreator('School Management System');
        $pdf->SetAuthor('School Admin');
        $pdf->SetTitle('Admit Card - ' . $student['first_name'] . ' ' . $student['last_name']);
        $pdf->SetSubject('Exam Admit Card');

        $pdf->AddPage();

        $this->drawAdmitCardContent($pdf, $exam, $student, $subjects);

        return $pdf;
    }

    private function drawAdmitCardContent($pdf, $exam, $student, $subjects) {
        // Header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 15, 'ADMIT CARD', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $exam['exam_name'], 0, 1, 'C');
        $pdf->Ln(5);

        // School logo and name (placeholder)
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'School Management System', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, '123 Education Street, Knowledge City', 0, 1, 'C');
        $pdf->Ln(10);

        // Student details
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 8, 'Student Name:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $student['first_name'] . ' ' . $student['last_name'], 0, 1);

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 8, 'Scholar Number:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $student['scholar_number'], 0, 1);

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 8, 'Class:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $student['class_name'] . ' ' . $student['section'], 0, 1);

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 8, 'Roll Number:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $student['roll_number'] ?? '-', 0, 1);

        $pdf->Ln(10);

        // Exam schedule
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Exam Schedule', 0, 1, 'C');
        $pdf->Ln(5);

        // Table headers
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(50, 8, 'Subject', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Date', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Day', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Time', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Max Marks', 1, 1, 'C');

        // Table content
        $pdf->SetFont('helvetica', '', 9);
        foreach ($subjects as $subject) {
            $pdf->Cell(50, 7, $subject['subject_name'], 1, 0);
            $pdf->Cell(25, 7, date('d/m/Y', strtotime($subject['exam_date'])), 1, 0, 'C');
            $pdf->Cell(20, 7, $subject['day'], 1, 0, 'C');
            $pdf->Cell(25, 7, date('H:i', strtotime($subject['start_time'])) . ' - ' . date('H:i', strtotime($subject['end_time'])), 1, 0, 'C');
            $pdf->Cell(20, 7, $subject['max_marks'], 1, 1, 'C');
        }

        $pdf->Ln(15);

        // Instructions
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 8, 'Important Instructions:', 0, 1);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->MultiCell(0, 5, "1. Bring this admit card to the examination hall.\n2. Arrive at least 30 minutes before exam time.\n3. Bring your own stationery.\n4. Mobile phones are not allowed in exam hall.\n5. Follow all exam rules and regulations.", 0, 'L');

        $pdf->Ln(20);

        // Signatures
        $pdf->Cell(60, 8, 'Principal Signature', 0, 0, 'C');
        $pdf->Cell(60, 8, 'Exam Controller', 0, 0, 'C');
        $pdf->Cell(60, 8, 'Student Signature', 0, 1, 'C');

        $pdf->Ln(15);

        $pdf->Cell(60, 8, '___________________', 0, 0, 'C');
        $pdf->Cell(60, 8, '___________________', 0, 0, 'C');
        $pdf->Cell(60, 8, '___________________', 0, 1, 'C');
    }

    private function createMarksheetPDF($exam, $student, $results) {
        $pdf = new TCPDF('P', 'mm', 'A4');
        $pdf->SetCreator('School Management System');
        $pdf->SetAuthor('School Admin');
        $pdf->SetTitle('Marksheet - ' . $student['first_name'] . ' ' . $student['last_name']);
        $pdf->SetSubject('Exam Marksheet');

        $pdf->AddPage();

        // Header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 15, 'MARK SHEET', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $exam['exam_name'], 0, 1, 'C');
        $pdf->Ln(5);

        // School details
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'School Management System', 0, 1, 'C');
        $pdf->Ln(10);

        // Student details
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 8, 'Student Name:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $student['first_name'] . ' ' . $student['last_name'], 0, 1);

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 8, 'Scholar Number:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $student['scholar_number'], 0, 1);

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 8, 'Class:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, $student['class_name'] . ' ' . $student['section'], 0, 1);

        $pdf->Ln(10);

        // Results table
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(60, 8, 'Subject', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Max Marks', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Marks Obtained', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Grade', 1, 0, 'C');
        $pdf->Cell(30, 8, 'Status', 1, 1, 'C');

        $totalMaxMarks = 0;
        $totalMarksObtained = 0;

        $pdf->SetFont('helvetica', '', 9);
        foreach ($results as $result) {
            $pdf->Cell(60, 7, $result['subject_name'], 1, 0);
            $pdf->Cell(25, 7, $result['max_marks'], 1, 0, 'C');
            $pdf->Cell(25, 7, $result['marks_obtained'] ?? '-', 1, 0, 'C');
            $pdf->Cell(20, 7, $result['grade'] ?? '-', 1, 0, 'C');

            $status = 'Pass';
            if ($result['marks_obtained'] < $result['passing_marks']) {
                $status = 'Fail';
            }
            $pdf->Cell(30, 7, $status, 1, 1, 'C');

            $totalMaxMarks += $result['max_marks'];
            $totalMarksObtained += $result['marks_obtained'] ?? 0;
        }

        // Total row
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(60, 8, 'TOTAL', 1, 0, 'R');
        $pdf->Cell(25, 8, $totalMaxMarks, 1, 0, 'C');
        $pdf->Cell(25, 8, $totalMarksObtained, 1, 0, 'C');
        $percentage = $totalMaxMarks > 0 ? round(($totalMarksObtained / $totalMaxMarks) * 100, 2) : 0;
        $pdf->Cell(20, 8, $percentage . '%', 1, 0, 'C');
        $pdf->Cell(30, 8, $percentage >= 40 ? 'PASS' : 'FAIL', 1, 1, 'C');

        $pdf->Ln(20);

        // Signatures
        $pdf->Cell(60, 8, 'Principal Signature', 0, 0, 'C');
        $pdf->Cell(60, 8, 'Class Teacher', 0, 0, 'C');
        $pdf->Cell(60, 8, 'Exam Controller', 0, 1, 'C');

        $pdf->Ln(15);

        $pdf->Cell(60, 8, '___________________', 0, 0, 'C');
        $pdf->Cell(60, 8, '___________________', 0, 0, 'C');
        $pdf->Cell(60, 8, '___________________', 0, 1, 'C');

        return $pdf;
    }

    private function createTCPDF($student, $attendance, $performance) {
        $pdf = new TCPDF('P', 'mm', 'A4');
        $pdf->SetCreator('School Management System');
        $pdf->SetAuthor('School Admin');
        $pdf->SetTitle('Transfer Certificate - ' . $student['full_name']);
        $pdf->SetSubject('Transfer Certificate');

        $pdf->AddPage();

        // Header
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 15, 'TRANSFER CERTIFICATE', 0, 1, 'C');
        $pdf->Ln(10);

        // School details
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'School Management System', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, '123 Education Street, Knowledge City, State - 123456', 0, 1, 'C');
        $pdf->Ln(15);

        // Certificate content
        $pdf->SetFont('helvetica', '', 11);
        $pdf->MultiCell(0, 8, "This is to certify that " . $student['full_name'] . " son/daughter of " .
                        ($student['father_name'] ?? 'N/A') . " and " . ($student['mother_name'] ?? 'N/A') .
                        " has studied in this school from " . date('d/m/Y', strtotime($student['admission_date'])) .
                        " to " . date('d/m/Y') . " in Class " . $student['class_name'] . " " . $student['section'] . ".", 0, 'L');

        $pdf->Ln(10);

        $pdf->MultiCell(0, 8, "During the period of study, the conduct of the student was " .
                        ($performance['average_percentage'] >= 60 ? 'Good' : 'Satisfactory') . " and attendance was " .
                        ($attendance['total_days'] > 0 ? round(($attendance['present_days'] / $attendance['total_days']) * 100) : 0) . "%.", 0, 'L');

        $pdf->Ln(10);

        $pdf->MultiCell(0, 8, "The student is leaving the school to join " .
                        ($_GET['joining_school'] ?? 'another institution') . ".", 0, 'L');

        $pdf->Ln(20);

        // Date and signatures
        $pdf->Cell(0, 8, 'Date: ' . date('d/m/Y'), 0, 1, 'R');

        $pdf->Ln(20);

        $pdf->Cell(60, 8, 'Principal Signature', 0, 0, 'C');
        $pdf->Cell(60, 8, 'Class Teacher', 0, 0, 'C');
        $pdf->Cell(60, 8, 'School Seal', 0, 1, 'C');

        $pdf->Ln(15);

        $pdf->Cell(60, 8, '___________________', 0, 0, 'C');
        $pdf->Cell(60, 8, '___________________', 0, 0, 'C');
        $pdf->Cell(60, 8, '___________________', 0, 1, 'C');

        return $pdf;
    }

    private function createFeeReceiptPDF($receiptData) {
        $pdf = new TCPDF('P', 'mm', [210, 99]); // Custom size for receipt
        $pdf->SetCreator('School Management System');
        $pdf->SetAuthor('School Admin');
        $pdf->SetTitle('Fee Receipt - ' . $receiptData['receipt_number']);

        $pdf->AddPage();

        // Header
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'FEE RECEIPT', 0, 1, 'C');
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 6, 'School Management System', 0, 1, 'C');
        $pdf->Ln(5);

        // Receipt details
        $pdf->SetFont('helvetica', '', 8);
        $pdf->Cell(30, 5, 'Receipt No:', 0, 0);
        $pdf->Cell(0, 5, $receiptData['receipt_number'], 0, 1);

        $pdf->Cell(30, 5, 'Date:', 0, 0);
        $pdf->Cell(0, 5, date('d/m/Y', strtotime($receiptData['payment_date'])), 0, 1);

        $pdf->Ln(3);

        // Student details
        $pdf->Cell(30, 5, 'Student:', 0, 0);
        $pdf->Cell(0, 5, $receiptData['student_name'], 0, 1);

        $pdf->Cell(30, 5, 'Scholar No:', 0, 0);
        $pdf->Cell(0, 5, $receiptData['scholar_number'], 0, 1);

        $pdf->Cell(30, 5, 'Class:', 0, 0);
        $pdf->Cell(0, 5, $receiptData['class_name'] . ' ' . $receiptData['section'], 0, 1);

        $pdf->Ln(3);

        // Fee details
        $pdf->Cell(30, 5, 'Fee Type:', 0, 0);
        $pdf->Cell(0, 5, $receiptData['fee_name'], 0, 1);

        $pdf->Cell(30, 5, 'Amount Paid:', 0, 0);
        $pdf->Cell(0, 5, '₹' . number_format($receiptData['amount_paid'], 2), 0, 1);

        $pdf->Cell(30, 5, 'Payment Mode:', 0, 0);
        $pdf->Cell(0, 5, ucfirst($receiptData['payment_mode']), 0, 1);

        if ($receiptData['transaction_id']) {
            $pdf->Cell(30, 5, 'Transaction ID:', 0, 0);
            $pdf->Cell(0, 5, $receiptData['transaction_id'], 0, 1);
        }

        $pdf->Ln(5);

        // Signature
        $pdf->Cell(0, 5, 'Received By: ' . ($receiptData['collected_by_name'] ?? 'Admin'), 0, 1, 'R');

        return $pdf;
    }

    private function createIdCardPDF($student) {
        $pdf = new TCPDF('L', 'mm', [85.6, 54]); // Standard ID card size
        $pdf->SetCreator('School Management System');
        $pdf->SetAuthor('School Admin');
        $pdf->SetTitle('ID Card - ' . $student['full_name']);

        $pdf->AddPage();

        // Background color
        $pdf->Rect(0, 0, 85.6, 54, 'F', array(), array(240, 248, 255));

        // Header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY(5, 5);
        $pdf->Cell(75.6, 5, 'School Management System', 0, 1, 'C');

        // Photo placeholder
        $pdf->Rect(5, 12, 20, 25);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetXY(5, 12);
        $pdf->Cell(20, 25, 'PHOTO', 0, 0, 'C');

        // Student details
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetXY(28, 12);
        $pdf->Cell(52.6, 4, $student['full_name'], 0, 1);

        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetXY(28, 17);
        $pdf->Cell(52.6, 3, 'Scholar No: ' . $student['scholar_number'], 0, 1);

        $pdf->SetXY(28, 21);
        $pdf->Cell(52.6, 3, 'Class: ' . $student['class_name'] . ' ' . $student['section'], 0, 1);

        $pdf->SetXY(28, 25);
        $pdf->Cell(52.6, 3, 'Blood Group: ' . ($student['blood_group'] ?? 'N/A'), 0, 1);

        $pdf->SetXY(28, 29);
        $pdf->Cell(52.6, 3, 'Contact: ' . ($student['mobile_number'] ?? 'N/A'), 0, 1);

        // Valid till
        $pdf->SetXY(5, 40);
        $pdf->SetFont('helvetica', 'B', 6);
        $pdf->Cell(75.6, 3, 'Valid Till: ' . date('d/m/Y', strtotime('+1 year')), 0, 1, 'C');

        // Barcode placeholder
        $pdf->Rect(5, 45, 75.6, 6);
        $pdf->SetXY(5, 45);
        $pdf->SetFont('helvetica', '', 5);
        $pdf->Cell(75.6, 6, 'BARCODE: ' . $student['scholar_number'], 0, 0, 'C');

        return $pdf;
    }

    private function outputPDF($pdf, $filename) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        echo $pdf->Output('', 'S');
        exit;
    }
}
?>