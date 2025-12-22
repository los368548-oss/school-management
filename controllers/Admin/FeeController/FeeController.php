<?php
/**
 * Fee Controller
 *
 * Handles fee management operations
 */

class FeeController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    public function fees() {
        $feeModel = new Fee();

        // Get fee collection summary
        $feeSummary = $feeModel->getCollectionSummary();

        $data = [
            'fee_summary' => $feeSummary,
            'academic_year' => $this->getAcademicYearInfo($this->getCurrentAcademicYear()),
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/fees', $data);
    }

    public function add() {
        $errors = [];
        $students = $this->db->select(
            "SELECT s.id, s.first_name, s.last_name, s.roll_number,
                    c.class_name, c.section
             FROM students s
             JOIN classes c ON s.class_id = c.id
             WHERE s.academic_year_id = ? AND s.status = 'active'
             ORDER BY c.class_name, c.section, s.roll_number",
            [$this->getCurrentAcademicYear()]
        );

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validation rules
            $validationRules = [
                'student_id' => 'required|integer',
                'fee_type' => 'required|in:tuition,transport,exam,other',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
                'description' => 'max:500'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                $feeData = [
                    'student_id' => $postData['student_id'],
                    'fee_type' => $postData['fee_type'],
                    'amount' => $postData['amount'],
                    'due_date' => $postData['due_date'],
                    'description' => $postData['description'] ?? null,
                    'academic_year_id' => $this->getCurrentAcademicYear(),
                    'status' => 'pending'
                ];

                $feeId = $this->db->insert('fees', $feeData);

                if ($feeId) {
                    $this->setFlash('success', 'Fee added successfully');
                    $this->logActivity('fee_added', "Added fee for student ID: {$postData['student_id']}");
                    $this->redirect('/admin/fees');
                } else {
                    $errors['db'] = 'Failed to add fee';
                }
            }
        }

        $data = [
            'students' => $students,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/fees/add', $data);
    }

    public function edit($feeId) {
        $fee = $this->db->selectOne(
            "SELECT f.*, s.first_name, s.last_name, s.roll_number,
                    c.class_name, c.section
             FROM fees f
             JOIN students s ON f.student_id = s.id
             JOIN classes c ON s.class_id = c.id
             WHERE f.id = ?",
            [$feeId]
        );

        if (!$fee) {
            $this->setFlash('error', 'Fee not found');
            $this->redirect('/admin/fees');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validation rules
            $validationRules = [
                'fee_type' => 'required|in:tuition,transport,exam,other',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
                'description' => 'max:500',
                'status' => 'required|in:pending,paid,overdue,waived'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                $feeData = [
                    'fee_type' => $postData['fee_type'],
                    'amount' => $postData['amount'],
                    'due_date' => $postData['due_date'],
                    'description' => $postData['description'] ?? null,
                    'status' => $postData['status']
                ];

                if ($this->db->update('fees', $feeData, 'id = ?', [$feeId])) {
                    $this->setFlash('success', 'Fee updated successfully');
                    $this->logActivity('fee_updated', "Updated fee ID: {$feeId}");
                    $this->redirect('/admin/fees');
                } else {
                    $errors['db'] = 'Failed to update fee';
                }
            }
        }

        $data = [
            'fee' => $fee,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/fees/edit', $data);
    }

    public function delete($feeId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $fee = $this->db->selectOne("SELECT * FROM fees WHERE id = ?", [$feeId]);

        if (!$fee) {
            $this->json(['error' => 'Fee not found'], 404);
        }

        // Check if fee has payments
        $payment = $this->db->selectOne(
            "SELECT id FROM fee_payments WHERE fee_id = ?",
            [$feeId]
        );

        if ($payment) {
            $this->json(['error' => 'Cannot delete fee that has payments'], 400);
        }

        if ($this->db->delete('fees', 'id = ?', [$feeId])) {
            $this->logActivity('fee_deleted', "Deleted fee ID: {$feeId}");
            $this->json(['success' => true, 'message' => 'Fee deleted successfully']);
        } else {
            $this->json(['error' => 'Failed to delete fee'], 500);
        }
    }

    private function getAcademicYearInfo($academicYearId) {
        return $this->db->selectOne("SELECT * FROM academic_years WHERE id = ?", [$academicYearId]);
    }
}
?>