<?php
/**
 * Security Configuration
 */

return [
    'encryption_key' => getenv('ENCRYPTION_KEY') ?: 'your-32-character-encryption-key-here',
    'csrf_token_lifetime' => 3600, // 1 hour
    'session_lifetime' => 7200, // 2 hours
    'max_login_attempts' => 5,
    'lockout_duration' => 900, // 15 minutes
    'password_policy' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => false,
    ],
    'rate_limiting' => [
        'enabled' => true,
        'max_requests' => 100,
        'window_minutes' => 15,
    ],
    'allowed_origins' => [
        'http://localhost',
        'https://localhost',
        // Add your domain here
    ],
];
?>