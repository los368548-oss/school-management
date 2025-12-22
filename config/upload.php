<?php
/**
 * File Upload Configuration
 */

return [
    // Upload Settings
    'settings' => [
        'max_file_size' => 5 * 1024 * 1024, // 5MB in bytes
        'max_files_count' => 10, // Maximum number of files per upload
        'upload_path' => BASE_PATH . 'uploads/',
        'temp_path' => sys_get_temp_dir() . '/school_uploads/',
        'allowed_extensions' => [
            // Images
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',
            // Documents
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt',
            // Archives
            'zip', 'rar', '7z',
            // Other
            'csv'
        ],
        'image_types' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'],
        'document_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'],
    ],

    // File Type Categories
    'categories' => [
        'profile_images' => [
            'path' => 'profiles/',
            'max_size' => 2 * 1024 * 1024, // 2MB
            'allowed_types' => ['jpg', 'jpeg', 'png'],
            'resize' => ['width' => 300, 'height' => 300],
            'thumbnail' => ['width' => 150, 'height' => 150],
        ],
        'documents' => [
            'path' => 'documents/',
            'max_size' => 10 * 1024 * 1024, // 10MB
            'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
        ],
        'gallery' => [
            'path' => 'gallery/',
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif'],
            'resize' => ['width' => 1200, 'height' => 800],
            'thumbnail' => ['width' => 300, 'height' => 200],
        ],
        'certificates' => [
            'path' => 'certificates/',
            'max_size' => 2 * 1024 * 1024, // 2MB
            'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png'],
        ],
        'reports' => [
            'path' => 'reports/',
            'max_size' => 5 * 1024 * 1024, // 5MB
            'allowed_types' => ['pdf', 'xls', 'xlsx', 'csv'],
        ],
    ],

    // Image Processing
    'image_processing' => [
        'enabled' => true,
        'library' => 'gd', // gd or imagick
        'quality' => 85, // JPEG quality (1-100)
        'auto_orient' => true,
        'strip_metadata' => true,
    ],

    // Security Settings
    'security' => [
        'scan_for_viruses' => false, // Enable if you have antivirus
        'check_mime_type' => true,
        'validate_file_content' => true,
        'randomize_filename' => true,
        'overwrite_existing' => false,
        'create_backups' => true,
    ],

    // Storage Settings
    'storage' => [
        'driver' => 'local', // local, s3, ftp, etc.
        'permissions' => [
            'file' => 0644,
            'directory' => 0755,
        ],
        'cleanup' => [
            'enabled' => true,
            'max_age_days' => 30, // Delete temp files older than 30 days
        ],
    ],

    // CDN Settings (if using external storage)
    'cdn' => [
        'enabled' => false,
        'url' => '',
        'key' => '',
        'secret' => '',
        'bucket' => '',
        'region' => '',
    ],

    // Upload Limits by User Role
    'limits' => [
        'admin' => [
            'max_files' => 50,
            'max_size_per_file' => 20 * 1024 * 1024, // 20MB
            'total_size_per_day' => 500 * 1024 * 1024, // 500MB
        ],
        'student' => [
            'max_files' => 10,
            'max_size_per_file' => 5 * 1024 * 1024, // 5MB
            'total_size_per_day' => 50 * 1024 * 1024, // 50MB
        ],
        'teacher' => [
            'max_files' => 25,
            'max_size_per_file' => 10 * 1024 * 1024, // 10MB
            'total_size_per_day' => 200 * 1024 * 1024, // 200MB
        ],
    ],

    // File Naming
    'naming' => [
        'strategy' => 'timestamp', // timestamp, random, original
        'prefix' => '',
        'suffix' => '',
        'lowercase' => true,
        'replace_spaces' => '_',
        'remove_special_chars' => true,
    ],

    // Error Messages
    'messages' => [
        'file_too_large' => 'File size exceeds the maximum allowed limit.',
        'invalid_type' => 'File type not allowed.',
        'upload_failed' => 'File upload failed. Please try again.',
        'security_error' => 'File failed security check.',
        'disk_full' => 'Insufficient disk space.',
        'permission_denied' => 'Permission denied to upload files.',
    ],
];
?>