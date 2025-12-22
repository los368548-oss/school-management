-- School Management System - Stored Procedures
-- Version 1.0.0

DELIMITER $$

-- Procedure to get student attendance summary
CREATE PROCEDURE GetStudentAttendanceSummary(
    IN student_id_param INT,
    IN academic_year_id_param INT
)
BEGIN
    SELECT
        COUNT(*) as total_days,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_days,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_days,
        ROUND((SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
    FROM attendance
    WHERE student_id = student_id_param AND academic_year_id = academic_year_id_param;
END $$

-- Procedure to get class-wise fee collection summary
CREATE PROCEDURE GetClassFeeSummary(
    IN academic_year_id_param INT
)
BEGIN
    SELECT
        c.class_name,
        c.section,
        COUNT(DISTINCT s.id) as total_students,
        SUM(f.amount) as total_fees,
        SUM(COALESCE(fp.amount_paid, 0)) as collected_amount,
        (SUM(f.amount) - SUM(COALESCE(fp.amount_paid, 0))) as pending_amount
    FROM classes c
    JOIN students s ON c.id = s.class_id
    LEFT JOIN fees f ON (f.class_id = c.id OR f.class_id IS NULL) AND f.academic_year_id = academic_year_id_param
    LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.academic_year_id = academic_year_id_param
    WHERE c.academic_year_id = academic_year_id_param AND s.academic_year_id = academic_year_id_param
    GROUP BY c.id
    ORDER BY c.class_name, c.section;
END $$

-- Procedure to get exam results for a class
CREATE PROCEDURE GetClassExamResults(
    IN exam_id_param INT,
    IN class_id_param INT
)
BEGIN
    SELECT
        s.scholar_number,
        CONCAT(s.first_name, ' ', s.last_name) as student_name,
        s.roll_number,
        GROUP_CONCAT(
            CONCAT(sub.subject_name, ': ', er.marks_obtained, '/', es.max_marks, ' (', er.grade, ')')
            ORDER BY sub.subject_name
            SEPARATOR '; '
        ) as subject_results,
        SUM(er.marks_obtained) as total_marks,
        SUM(es.max_marks) as max_marks,
        ROUND((SUM(er.marks_obtained) / SUM(es.max_marks)) * 100, 2) as percentage,
        CASE
            WHEN ROUND((SUM(er.marks_obtained) / SUM(es.max_marks)) * 100, 2) >= 90 THEN 'A+'
            WHEN ROUND((SUM(er.marks_obtained) / SUM(es.max_marks)) * 100, 2) >= 80 THEN 'A'
            WHEN ROUND((SUM(er.marks_obtained) / SUM(es.max_marks)) * 100, 2) >= 70 THEN 'B+'
            WHEN ROUND((SUM(er.marks_obtained) / SUM(es.max_marks)) * 100, 2) >= 60 THEN 'B'
            WHEN ROUND((SUM(er.marks_obtained) / SUM(es.max_marks)) * 100, 2) >= 50 THEN 'C'
            WHEN ROUND((SUM(er.marks_obtained) / SUM(es.max_marks)) * 100, 2) >= 40 THEN 'D'
            ELSE 'F'
        END as overall_grade
    FROM students s
    LEFT JOIN exam_results er ON s.id = er.student_id
    LEFT JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
    LEFT JOIN subjects sub ON er.subject_id = sub.id
    WHERE er.exam_id = exam_id_param AND s.class_id = class_id_param
    GROUP BY s.id
    ORDER BY s.roll_number, s.scholar_number;
END $$

-- Procedure to generate student promotion report
CREATE PROCEDURE GeneratePromotionReport(
    IN from_class_id_param INT,
    IN academic_year_id_param INT
)
BEGIN
    SELECT
        s.id,
        s.scholar_number,
        CONCAT(s.first_name, ' ', s.last_name) as student_name,
        s.roll_number,
        c.class_name as current_class,
        c.section as current_section,
        AVG(CASE WHEN e.exam_type = 'final' THEN (SUM(er.marks_obtained) / SUM(es.max_marks)) * 100 END) as final_exam_percentage,
        COUNT(CASE WHEN a.status = 'present' THEN 1 END) / COUNT(a.id) * 100 as attendance_percentage,
        CASE
            WHEN AVG(CASE WHEN e.exam_type = 'final' THEN (SUM(er.marks_obtained) / SUM(es.max_marks)) * 100 END) >= 50
                 AND COUNT(CASE WHEN a.status = 'present' THEN 1 END) / COUNT(a.id) * 100 >= 75 THEN 'Promoted'
            WHEN AVG(CASE WHEN e.exam_type = 'final' THEN (SUM(er.marks_obtained) / SUM(es.max_marks)) * 100 END) < 50 THEN 'Failed'
            ELSE 'Attendance Shortage'
        END as promotion_status
    FROM students s
    JOIN classes c ON s.class_id = c.id
    LEFT JOIN exam_results er ON s.id = er.student_id
    LEFT JOIN exams e ON er.exam_id = e.id
    LEFT JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
    LEFT JOIN attendance a ON s.id = a.student_id AND a.academic_year_id = academic_year_id_param
    WHERE s.class_id = from_class_id_param AND s.academic_year_id = academic_year_id_param
    GROUP BY s.id
    ORDER BY s.roll_number;
END $$

-- Procedure to get monthly attendance report
CREATE PROCEDURE GetMonthlyAttendanceReport(
    IN class_id_param INT,
    IN academic_year_id_param INT,
    IN month_param INT,
    IN year_param INT
)
BEGIN
    SELECT
        s.roll_number,
        CONCAT(s.first_name, ' ', s.last_name) as student_name,
        COUNT(CASE WHEN DAY(a.attendance_date) = 1 AND a.status = 'present' THEN 1 END) as day_1,
        COUNT(CASE WHEN DAY(a.attendance_date) = 2 AND a.status = 'present' THEN 1 END) as day_2,
        COUNT(CASE WHEN DAY(a.attendance_date) = 3 AND a.status = 'present' THEN 1 END) as day_3,
        -- Continue for all days in month (simplified - would need dynamic SQL for full implementation)
        COUNT(CASE WHEN a.status = 'present' THEN 1 END) as total_present,
        COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as total_absent,
        ROUND(COUNT(CASE WHEN a.status = 'present' THEN 1 END) / COUNT(*) * 100, 2) as attendance_percentage
    FROM students s
    LEFT JOIN attendance a ON s.id = a.student_id
        AND MONTH(a.attendance_date) = month_param
        AND YEAR(a.attendance_date) = year_param
        AND a.academic_year_id = academic_year_id_param
    WHERE s.class_id = class_id_param AND s.academic_year_id = academic_year_id_param
    GROUP BY s.id
    ORDER BY s.roll_number;
END $$

-- Procedure to calculate fee defaulters
CREATE PROCEDURE GetFeeDefaulters(
    IN academic_year_id_param INT
)
BEGIN
    SELECT
        s.id,
        s.scholar_number,
        CONCAT(s.first_name, ' ', s.last_name) as student_name,
        c.class_name,
        c.section,
        s.roll_number,
        SUM(f.amount) as total_fees,
        COALESCE(SUM(fp.amount_paid), 0) as paid_amount,
        (SUM(f.amount) - COALESCE(SUM(fp.amount_paid), 0)) as pending_amount,
        DATEDIFF(CURDATE(), MIN(f.due_date)) as days_overdue
    FROM students s
    JOIN classes c ON s.class_id = c.id
    LEFT JOIN fees f ON (f.class_id = c.id OR f.class_id IS NULL) AND f.academic_year_id = academic_year_id_param
    LEFT JOIN fee_payments fp ON s.id = fp.student_id AND fp.academic_year_id = academic_year_id_param
    WHERE s.academic_year_id = academic_year_id_param AND s.status = 'active'
    GROUP BY s.id
    HAVING pending_amount > 0
    ORDER BY pending_amount DESC, days_overdue DESC;
END $$

-- Procedure to get dashboard statistics
CREATE PROCEDURE GetDashboardStats(
    IN academic_year_id_param INT
)
BEGIN
    -- Student count
    SELECT 'total_students' as metric, COUNT(*) as value FROM students WHERE academic_year_id = academic_year_id_param AND status = 'active'
    UNION ALL
    -- Class count
    SELECT 'total_classes' as metric, COUNT(*) as value FROM classes WHERE academic_year_id = academic_year_id_param
    UNION ALL
    -- Exam count
    SELECT 'total_exams' as metric, COUNT(*) as value FROM exams WHERE academic_year_id = academic_year_id_param
    UNION ALL
    -- Total fees
    SELECT 'total_fees' as metric, SUM(amount) as value FROM fees WHERE academic_year_id = academic_year_id_param
    UNION ALL
    -- Collected fees
    SELECT 'collected_fees' as metric, SUM(amount_paid) as value FROM fee_payments WHERE academic_year_id = academic_year_id_param
    UNION ALL
    -- Pending fees
    SELECT 'pending_fees' as metric,
           (SELECT SUM(amount) FROM fees WHERE academic_year_id = academic_year_id_param) -
           (SELECT SUM(amount_paid) FROM fee_payments WHERE academic_year_id = academic_year_id_param) as value;
END $$

DELIMITER ;