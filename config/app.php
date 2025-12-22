<?php
/**
 * Application Configuration
 */

return [
    'name' => 'School Management System',
    'version' => '1.0.0',
    'url' => getenv('APP_URL') ?: 'http://localhost',
    'timezone' => 'Asia/Kolkata',
    'locale' => 'en',
    'debug' => getenv('APP_DEBUG') ?: false,
    'log_level' => 'error',
    'maintenance_mode' => false,

    // File upload settings
    'upload' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        'path' => BASE_PATH . 'uploads/',
    ],

    // Pagination
    'pagination' => [
        'per_page' => 25,
    ],

    // Date formats
    'date_format' => 'Y-m-d',
    'datetime_format' => 'Y-m-d H:i:s',
    'display_date_format' => 'd/m/Y',
    'display_datetime_format' => 'd/m/Y H:i',

    // School settings
    'school' => [
        'name' => 'School Name',
        'address' => 'School Address',
        'phone' => 'School Phone',
        'email' => 'school@example.com',
        'website' => 'http://school.example.com',
    ],
];
?>