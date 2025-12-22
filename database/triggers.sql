-- School Management System - Database Triggers
-- Version 1.0.0

DELIMITER $$

-- Trigger to automatically create user profile when user is created
CREATE TRIGGER after_user_insert
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    INSERT INTO user_profiles (user_id, first_name, last_name, created_at, updated_at)
    VALUES (NEW.id, '', '', NOW(), NOW());
END $$

-- Trigger to update user profile timestamp when user is updated
CREATE TRIGGER after_user_update
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    UPDATE user_profiles SET updated_at = NOW() WHERE user_id = NEW.id;
END $$

-- Trigger to log user login activities
CREATE TRIGGER after_user_login_update
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF OLD.last_login IS NULL AND NEW.last_login IS NOT NULL THEN
        INSERT INTO audit_logs (user_id, action, details, created_at)
        VALUES (NEW.id, 'login', 'User logged in', NOW());
    END IF;
END $$

-- Trigger to prevent deletion of students with outstanding fees
CREATE TRIGGER before_student_delete
BEFORE DELETE ON students
FOR EACH ROW
BEGIN
    DECLARE outstanding_fees DECIMAL(10,2);

    SELECT (SUM(f.amount) - COALESCE(SUM(fp.amount_paid), 0)) INTO outstanding_fees
    FROM fees f
    LEFT JOIN fee_payments fp ON f.id = fp.fee_id AND fp.student_id = OLD.id
    WHERE f.academic_year_id = OLD.academic_year_id;

    IF outstanding_fees > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot delete student with outstanding fees';
    END IF;
END $$

-- Trigger to update class capacity when student is added
CREATE TRIGGER after_student_insert
AFTER INSERT ON students
FOR EACH ROW
BEGIN
    UPDATE classes SET updated_at = NOW() WHERE id = NEW.class_id;
END $$

-- Trigger to update class capacity when student is removed
CREATE TRIGGER after_student_delete
AFTER DELETE ON students
FOR EACH ROW
BEGIN
    UPDATE classes SET updated_at = NOW() WHERE id = OLD.class_id;
END $$

-- Trigger to log attendance changes
CREATE TRIGGER after_attendance_insert
AFTER INSERT ON attendance
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (user_id, action, details, created_at)
    VALUES (
        NEW.marked_by,
        'attendance_marked',
        CONCAT('Attendance marked for student ID: ', NEW.student_id, ', Status: ', NEW.status),
        NOW()
    );
END $$

-- Trigger to log attendance updates
CREATE TRIGGER after_attendance_update
AFTER UPDATE ON attendance
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO audit_logs (user_id, action, details, created_at)
        VALUES (
            NEW.marked_by,
            'attendance_updated',
            CONCAT('Attendance updated for student ID: ', NEW.student_id, ', From: ', OLD.status, ' To: ', NEW.status),
            NOW()
        );
    END IF;
END $$

-- Trigger to log fee payment activities
CREATE TRIGGER after_fee_payment_insert
AFTER INSERT ON fee_payments
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (user_id, action, details, created_at)
    VALUES (
        NEW.collected_by,
        'fee_payment',
        CONCAT('Fee payment recorded: ', NEW.receipt_number, ', Amount: ₹', NEW.amount_paid),
        NOW()
    );
END $$

-- Trigger to log exam result entries
CREATE TRIGGER after_exam_result_insert
AFTER INSERT ON exam_results
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (user_id, action, details, created_at)
    VALUES (
        NEW.marked_by,
        'exam_result_entered',
        CONCAT('Exam result entered for student ID: ', NEW.student_id, ', Subject ID: ', NEW.subject_id, ', Marks: ', NEW.marks_obtained),
        NOW()
    );
END $$

-- Trigger to log exam result updates
CREATE TRIGGER after_exam_result_update
AFTER UPDATE ON exam_results
FOR EACH ROW
BEGIN
    IF OLD.marks_obtained != NEW.marks_obtained OR OLD.grade != NEW.grade THEN
        INSERT INTO audit_logs (user_id, action, details, created_at)
        VALUES (
            NEW.marked_by,
            'exam_result_updated',
            CONCAT('Exam result updated for student ID: ', NEW.student_id, ', Subject ID: ', NEW.subject_id, ', Marks: ', OLD.marks_obtained, ' → ', NEW.marks_obtained),
            NOW()
        );
    END IF;
END $$

-- Trigger to automatically set academic year for new records
CREATE TRIGGER before_student_insert_set_academic_year
BEFORE INSERT ON students
FOR EACH ROW
BEGIN
    -- If no academic year is specified, use the active one
    IF NEW.academic_year_id IS NULL THEN
        SET NEW.academic_year_id = (SELECT id FROM academic_years WHERE is_active = 1 LIMIT 1);
    END IF;
END $$

-- Trigger to automatically set academic year for attendance
CREATE TRIGGER before_attendance_insert_set_academic_year
BEFORE INSERT ON attendance
FOR EACH ROW
BEGIN
    -- If no academic year is specified, get it from the student's class
    IF NEW.academic_year_id IS NULL THEN
        SET NEW.academic_year_id = (
            SELECT c.academic_year_id
            FROM students s
            JOIN classes c ON s.class_id = c.id
            WHERE s.id = NEW.student_id
            LIMIT 1
        );
    END IF;
END $$

-- Trigger to automatically set academic year for fee payments
CREATE TRIGGER before_fee_payment_insert_set_academic_year
BEFORE INSERT ON fee_payments
FOR EACH ROW
BEGIN
    -- If no academic year is specified, get it from the fee
    IF NEW.academic_year_id IS NULL THEN
        SET NEW.academic_year_id = (SELECT academic_year_id FROM fees WHERE id = NEW.fee_id LIMIT 1);
    END IF;
END $$

-- Trigger to automatically set academic year for exam results
CREATE TRIGGER before_exam_result_insert_set_academic_year
BEFORE INSERT ON exam_results
FOR EACH ROW
BEGIN
    -- If no academic year is specified, get it from the exam
    IF NEW.academic_year_id IS NULL THEN
        SET NEW.academic_year_id = (SELECT academic_year_id FROM exams WHERE id = NEW.exam_id LIMIT 1);
    END IF;
END $$

-- Trigger to update academic year statistics when records are added
CREATE TRIGGER after_academic_year_record_insert
AFTER INSERT ON students
FOR EACH ROW
BEGIN
    -- This could trigger cache invalidation or statistics updates
    -- For now, just log the activity
    INSERT INTO audit_logs (action, details, created_at)
    VALUES ('academic_year_data_added', CONCAT('Student added to academic year: ', NEW.academic_year_id), NOW());
END $$

-- Trigger to prevent changes to completed exams
CREATE TRIGGER before_exam_update_prevent_completed
BEFORE UPDATE ON exams
FOR EACH ROW
BEGIN
    IF OLD.status = 'completed' AND NEW.status != 'completed' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cannot modify completed exams';
    END IF;
END $$

-- Trigger to validate exam dates
CREATE TRIGGER before_exam_subject_insert_validate_dates
BEFORE INSERT ON exam_subjects
FOR EACH ROW
BEGIN
    DECLARE exam_start DATE;
    DECLARE exam_end DATE;

    SELECT start_date, end_date INTO exam_start, exam_end
    FROM exams WHERE id = NEW.exam_id;

    IF NEW.exam_date < exam_start OR NEW.exam_date > exam_end THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Exam subject date must be within exam period';
    END IF;
END $$

-- Trigger to validate fee amounts
CREATE TRIGGER before_fee_insert_validate_amount
BEFORE INSERT ON fees
FOR EACH ROW
BEGIN
    IF NEW.amount <= 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Fee amount must be greater than zero';
    END IF;
END $$

-- Trigger to validate attendance dates
CREATE TRIGGER before_attendance_insert_validate_date
BEFORE INSERT ON attendance
FOR EACH ROW
BEGIN
    DECLARE academic_year_start DATE;
    DECLARE academic_year_end DATE;

    SELECT start_date, end_date INTO academic_year_start, academic_year_end
    FROM academic_years WHERE id = NEW.academic_year_id;

    IF NEW.attendance_date < academic_year_start OR NEW.attendance_date > academic_year_end THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Attendance date must be within academic year';
    END IF;
END $$

DELIMITER ;