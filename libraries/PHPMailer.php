<?php
/**
 * PHPMailer Library Wrapper
 *
 * Wrapper class for PHPMailer library to send emails
 * This is a placeholder - in production, install actual PHPMailer via Composer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;

    public function __construct() {
        // In production, this would be:
        // require_once 'vendor/autoload.php';
        // $this->mailer = new PHPMailer(true);

        // For now, create a mock implementation
        $this->mailer = new stdClass();
        $this->mailer->isSMTP = function() { return true; };
        $this->mailer->Host = '';
        $this->mailer->SMTPAuth = false;
        $this->mailer->Username = '';
        $this->mailer->Password = '';
        $this->mailer->SMTPSecure = '';
        $this->mailer->Port = 587;
        $this->mailer->setFrom = function($email, $name) {
            $this->from = ['email' => $email, 'name' => $name];
        };
        $this->mailer->addAddress = function($email, $name = '') {
            if (!isset($this->recipients)) $this->recipients = [];
            $this->recipients[] = ['email' => $email, 'name' => $name];
        };
        $this->mailer->isHTML = function($bool) {
            $this->isHtml = $bool;
        };
        $this->mailer->Subject = '';
        $this->mailer->Body = '';
        $this->mailer->AltBody = '';
        $this->mailer->send = function() {
            // Mock send - in production this would actually send email
            // Log the email for debugging
            $logData = [
                'to' => $this->recipients ?? [],
                'subject' => $this->Subject,
                'body' => $this->Body,
                'sent_at' => date('Y-m-d H:i:s')
            ];

            $logFile = BASE_PATH . 'logs/email.log';
            $logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($logData) . "\n";
            file_put_contents($logFile, $logEntry, FILE_APPEND);

            return true; // Mock success
        };
    }

    public function sendWelcomeEmail($userEmail, $userName, $password) {
        $this->setFrom('admin@school.com', 'School Management System');

        $this->addAddress($userEmail, $userName);

        $this->isHTML(true);

        $this->Subject = 'Welcome to School Management System';

        $this->Body = "
            <html>
            <head>
                <title>Welcome to School Management System</title>
            </head>
            <body>
                <h2>Welcome {$userName}!</h2>
                <p>Your account has been created successfully.</p>
                <p><strong>Login Details:</strong></p>
                <ul>
                    <li>Email: {$userEmail}</li>
                    <li>Password: {$password}</li>
                </ul>
                <p>Please change your password after first login.</p>
                <p><a href='" . BASE_URL . "'>Click here to login</a></p>
                <br>
                <p>Best regards,<br>School Administration</p>
            </body>
            </html>
        ";

        $this->AltBody = "Welcome {$userName}!\n\nYour account has been created.\nEmail: {$userEmail}\nPassword: {$password}\n\nPlease change your password after first login.\n\nLogin at: " . BASE_URL;

        return $this->send();
    }

    public function sendPasswordReset($userEmail, $resetToken) {
        $this->setFrom('admin@school.com', 'School Management System');

        $this->addAddress($userEmail);

        $this->isHTML(true);

        $this->Subject = 'Password Reset Request';

        $resetLink = BASE_URL . "reset-password?token=" . $resetToken;

        $this->Body = "
            <html>
            <head>
                <title>Password Reset</title>
            </head>
            <body>
                <h2>Password Reset Request</h2>
                <p>You have requested to reset your password.</p>
                <p><a href='{$resetLink}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                <p>If you didn't request this, please ignore this email.</p>
                <p>This link will expire in 1 hour.</p>
                <br>
                <p>Best regards,<br>School Administration</p>
            </body>
            </html>
        ";

        $this->AltBody = "Password Reset Request\n\nYou have requested to reset your password.\n\nReset your password at: {$resetLink}\n\nIf you didn't request this, please ignore this email.\nThis link will expire in 1 hour.";

        return $this->send();
    }

    public function sendFeeReminder($studentEmail, $studentName, $pendingAmount) {
        $this->setFrom('admin@school.com', 'School Management System');

        $this->addAddress($studentEmail, $studentName);

        $this->isHTML(true);

        $this->Subject = 'Fee Payment Reminder';

        $this->Body = "
            <html>
            <head>
                <title>Fee Payment Reminder</title>
            </head>
            <body>
                <h2>Fee Payment Reminder</h2>
                <p>Dear {$studentName},</p>
                <p>This is a reminder that you have outstanding fees amounting to <strong>₹{$pendingAmount}</strong>.</p>
                <p>Please make the payment at the earliest to avoid any inconvenience.</p>
                <p><a href='" . BASE_URL . "student/fees' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Fee Details</a></p>
                <br>
                <p>Best regards,<br>School Administration</p>
            </body>
            </html>
        ";

        $this->AltBody = "Fee Payment Reminder\n\nDear {$studentName},\n\nThis is a reminder that you have outstanding fees amounting to ₹{$pendingAmount}.\n\nPlease make the payment at the earliest.\n\nView details at: " . BASE_URL . "student/fees";

        return $this->send();
    }

    public function sendExamResult($studentEmail, $studentName, $examName, $results) {
        $this->setFrom('admin@school.com', 'School Management System');

        $this->addAddress($studentEmail, $studentName);

        $this->isHTML(true);

        $this->Subject = "Exam Results: {$examName}";

        $resultRows = '';
        foreach ($results as $result) {
            $resultRows .= "<tr>
                <td>{$result['subject_name']}</td>
                <td>{$result['marks_obtained']}</td>
                <td>{$result['max_marks']}</td>
                <td>{$result['grade']}</td>
            </tr>";
        }

        $this->Body = "
            <html>
            <head>
                <title>Exam Results</title>
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <h2>Exam Results: {$examName}</h2>
                <p>Dear {$studentName},</p>
                <p>Your exam results are now available:</p>

                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Marks Obtained</th>
                            <th>Max Marks</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$resultRows}
                    </tbody>
                </table>

                <p><a href='" . BASE_URL . "student/results' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Full Results</a></p>

                <br>
                <p>Best regards,<br>School Administration</p>
            </body>
            </html>
        ";

        $this->AltBody = "Exam Results: {$examName}\n\nDear {$studentName},\n\nYour exam results are now available.\n\nView full results at: " . BASE_URL . "student/results";

        return $this->send();
    }

    // Magic methods to make it work like PHPMailer
    public function __call($method, $args) {
        if (is_callable($this->mailer->$method)) {
            return call_user_func_array($this->mailer->$method, $args);
        }
        return $this->mailer->$method = $args[0] ?? null;
    }

    public function __set($property, $value) {
        $this->mailer->$property = $value;
    }

    public function __get($property) {
        return $this->mailer->$property ?? null;
    }
}
?>