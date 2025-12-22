<?php
/**
 * Email Configuration
 */

return [
    // SMTP Configuration
    'smtp' => [
        'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'port' => getenv('SMTP_PORT') ?: 587,
        'encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls', // tls, ssl, or null
        'username' => getenv('SMTP_USERNAME') ?: '',
        'password' => getenv('SMTP_PASSWORD') ?: '',
        'from_email' => getenv('FROM_EMAIL') ?: 'noreply@schoolmanagement.com',
        'from_name' => getenv('FROM_NAME') ?: 'School Management System',
    ],

    // Email Templates
    'templates' => [
        'welcome' => [
            'subject' => 'Welcome to School Management System',
            'template' => 'welcome.html'
        ],
        'password_reset' => [
            'subject' => 'Password Reset Request',
            'template' => 'password_reset.html'
        ],
        'fee_reminder' => [
            'subject' => 'Fee Payment Reminder',
            'template' => 'fee_reminder.html'
        ],
        'exam_schedule' => [
            'subject' => 'Exam Schedule Notification',
            'template' => 'exam_schedule.html'
        ],
        'result_published' => [
            'subject' => 'Exam Results Published',
            'template' => 'result_published.html'
        ]
    ],

    // Email Settings
    'settings' => [
        'debug' => getenv('EMAIL_DEBUG') ?: false,
        'charset' => 'UTF-8',
        'wordwrap' => 78,
        'timeout' => 30,
        'keep_alive' => false,
    ],

    // Queue Settings (for bulk emails)
    'queue' => [
        'enabled' => false,
        'batch_size' => 50,
        'delay_between_batches' => 5, // seconds
        'max_retries' => 3,
    ],

    // Notification Settings
    'notifications' => [
        'admin_new_registration' => true,
        'student_fee_due' => true,
        'exam_results_published' => true,
        'attendance_alert' => true,
    ],

    // BCC Settings
    'bcc' => [
        'enabled' => false,
        'email' => '',
    ],
];
?>