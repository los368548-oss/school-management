<?php
/**
 * Application Configuration
 */

return [
    'name' => 'A.s.higher secondary school Management System',
    'version' => '1.0.0',
    'debug' => getenv('APP_DEBUG') ?: false,
    'log_level' => getenv('LOG_LEVEL') ?: 'error',
    'timezone' => 'Asia/Kolkata',
    'locale' => 'en',

    // URLs
    'base_url' => getenv('APP_URL') ?: 'http://localhost',

    // File uploads
    'max_file_size' => 5 * 1024 * 1024, // 5MB
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],

    // Pagination
    'per_page' => 25,

    // Security
    'csrf_token_lifetime' => 3600, // 1 hour

    // Email settings (placeholder)
    'email_from' => 'noreply@school.com',
    'email_from_name' => 'School Management System',

    // Cache settings
    'cache_enabled' => true,
    'cache_lifetime' => 3600, // 1 hour

    // API settings
    'api_rate_limit' => 60, // requests per minute
    'api_version' => 'v1',
];
?>