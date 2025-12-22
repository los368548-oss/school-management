<?php
// Admin Marksheet Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - <?php echo htmlspecialchars($exam['exam_name']); ?></title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <style>
        .marksheet {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .school-logo {
            max-width: 100px;
            height: auto;
        }
        .student-photo {
            max-width: 100px;
            height: 120px;
            border: 1px solid #000;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .marks-table th, .marks-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        .marks-table th {
            background-color: #f8f9fa;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .result-summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        @media print {
            body { margin: 0; }
            .marksheet { border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row no-print">
            <div class="col-12 text-center mb-4">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Marksheet
                </button>
                <a href="/admin/exams" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Exams
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="marksheet">
                    <div class="header">
                        <div class="row">
                            <div class="col-3">
                                <img src="/assets/images/school-logo.png" alt="School Logo" class="school-logo">
                            </div>
                            <div class="col-9">
                                <h2><?php echo htmlspecialchars($school_name ?? 'School Name'); ?></h2>
                                <h4>Marksheet</h4>
                                <h5><?php echo htmlspecialchars($exam['exam_name']); ?></h5>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Student Name:</strong></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Roll Number:</strong></td>
                                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Class:</strong></td>
                                    <td><?php echo htmlspecialchars($student['class_name'] . ' ' . $student['section']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Exam Date:</strong></td>
                                    <td><?php echo htmlspecialchars($exam['start_date']); ?> to <?php echo htmlspecialchars($exam['end_date']); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-4 text-center">
                            <img src="<?php echo htmlspecialchars($student['photo_path'] ?? '/assets/images/default-student.png'); ?>"
                                 alt="Student Photo" class="student-photo">
                        </div>
                    </div>

                    <table class="marks-table">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Max Marks</th>
                                <th>Marks Obtained</th>
                                <th>Grade</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalMarks = 0;
                            $totalObtained = 0;
                            foreach ($results as $result):
                                $totalMarks += 100; // Assuming 100 is max marks per subject
                                $totalObtained += $result['marks_obtained'];
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['subject_code']); ?></td>
                                <td><?php echo htmlspecialchars($result['subject_name']); ?></td>
                                <td>100</td>
                                <td><?php echo htmlspecialchars($result['marks_obtained']); ?></td>
                                <td><?php echo htmlspecialchars($result['grade'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($result['remarks'] ?? '-'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="result-summary">
                        <div class="row">
                            <div class="col-6">
                                <strong>Total Marks: <?php echo $totalMarks; ?></strong>
                            </div>
                            <div class="col-6 text-right">
                                <strong>Marks Obtained: <?php echo $totalObtained; ?></strong>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <strong>Percentage: <?php echo number_format(($totalObtained / $totalMarks) * 100, 2); ?>%</strong>
                            </div>
                            <div class="col-6 text-right">
                                <strong>Grade: <?php echo htmlspecialchars($overall_grade ?? '-'); ?></strong>
                            </div>
                        </div>
                    </div>

                    <div class="signature-section">
                        <div class="signature-box">
                            <strong>Principal</strong>
                        </div>
                        <div class="signature-box">
                            <strong>Class Teacher</strong>
                        </div>
                        <div class="signature-box">
                            <strong>Exam Controller</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>