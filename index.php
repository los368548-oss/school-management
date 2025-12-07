<?php
/**
 * School Management System - Main Entry Point
 */

// Start output buffering
ob_start();

// Include autoloader or manual includes
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/Session.php';
require_once __DIR__ . '/core/Security.php';
require_once __DIR__ . '/core/Validator.php';
require_once __DIR__ . '/controllers/BaseController.php';
require_once __DIR__ . '/models/BaseModel.php';

// Initialize core components
$session = Session::getInstance();
$security = Security::getInstance();

// Check session timeout
if (!$session->checkTimeout()) {
    $session->destroy();
}

// Load configuration
$config = require __DIR__ . '/config/app.php';

// Set timezone
date_default_timezone_set($config['timezone']);

// Initialize router
$router = new Router();

// Define routes

// Public routes
$router->get('/', 'PublicController@index');
$router->get('/about', 'PublicController@about');
$router->get('/courses', 'PublicController@courses');
$router->get('/events', 'PublicController@events');
$router->get('/gallery', 'PublicController@gallery');
$router->get('/contact', 'PublicController@contact');
$router->post('/contact', 'PublicController@submitContact');

// Authentication routes
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotPassword');
$router->post('/forgot-password', 'AuthController@resetPassword');

// Admin routes (protected)
$router->get('/admin', 'AdminController@dashboard', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/dashboard', 'AdminController@dashboard', ['AuthMiddleware', 'AdminMiddleware']);

// Admin - Students
$router->get('/admin/students', 'AdminController@students', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/students/create', 'AdminController@createStudent', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/students', 'AdminController@storeStudent', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/students/{id}', 'AdminController@showStudent', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/students/{id}/edit', 'AdminController@editStudent', ['AuthMiddleware', 'AdminMiddleware']);
$router->put('/admin/students/{id}', 'AdminController@updateStudent', ['AuthMiddleware', 'AdminMiddleware']);
$router->delete('/admin/students/{id}', 'AdminController@deleteStudent', ['AuthMiddleware', 'AdminMiddleware']);

// Admin - Classes
$router->get('/admin/classes', 'AdminController@classes', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/classes/create', 'AdminController@createClass', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/classes', 'AdminController@storeClass', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/classes/{id}/edit', 'AdminController@editClass', ['AuthMiddleware', 'AdminMiddleware']);
$router->put('/admin/classes/{id}', 'AdminController@updateClass', ['AuthMiddleware', 'AdminMiddleware']);
$router->delete('/admin/classes/{id}', 'AdminController@deleteClass', ['AuthMiddleware', 'AdminMiddleware']);

// Admin - Attendance
$router->get('/admin/attendance', 'AdminController@attendance', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/attendance/mark', 'AdminController@markAttendance', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/attendance/data', 'AdminController@getAttendanceData', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/attendance/report', 'AdminController@attendanceReport', ['AuthMiddleware', 'AdminMiddleware']);

// Admin - Exams
$router->get('/admin/exams', 'AdminController@exams', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/exams/create', 'AdminController@createExam', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/exams', 'AdminController@storeExam', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/exams/{id}', 'AdminController@showExam', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/exams/{id}/edit', 'AdminController@editExam', ['AuthMiddleware', 'AdminMiddleware']);
$router->put('/admin/exams/{id}', 'AdminController@updateExam', ['AuthMiddleware', 'AdminMiddleware']);
$router->delete('/admin/exams/{id}', 'AdminController@deleteExam', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/exams/{id}/results', 'AdminController@enterResults', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/exams/results', 'AdminController@saveResults', ['AuthMiddleware', 'AdminMiddleware']);

// Admin - Fees
$router->get('/admin/fees', 'AdminController@fees', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/fees/collect', 'AdminController@collectFee', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/fees/collect', 'AdminController@processFeeCollection', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/fees/receipt/{id}', 'AdminController@feeReceipt', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/fees/details', 'AdminController@getStudentFeeDetails', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/fees/report', 'AdminController@feeReport', ['AuthMiddleware', 'AdminMiddleware']);

// Admin - Events
$router->get('/admin/events', 'AdminController@events', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/events/create', 'AdminController@createEvent', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/events', 'AdminController@storeEvent', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/events/{id}', 'AdminController@showEvent', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/events/{id}/edit', 'AdminController@editEvent', ['AuthMiddleware', 'AdminMiddleware']);
$router->put('/admin/events/{id}', 'AdminController@updateEvent', ['AuthMiddleware', 'AdminMiddleware']);
$router->delete('/admin/events/{id}', 'AdminController@deleteEvent', ['AuthMiddleware', 'AdminMiddleware']);

// Admin - Gallery
$router->get('/admin/gallery', 'AdminController@gallery', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/gallery/upload', 'AdminController@uploadGallery', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/gallery/upload', 'AdminController@processGalleryUpload', ['AuthMiddleware', 'AdminMiddleware']);
$router->delete('/admin/gallery/{id}', 'AdminController@deleteGallery', ['AuthMiddleware', 'AdminMiddleware']);

// Admin - Reports
$router->get('/admin/reports', 'AdminController@reports', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/reports/students', 'AdminController@studentReport', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/reports/attendance', 'AdminController@attendanceReport', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/reports/fees', 'AdminController@feeReport', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/reports/exam/{id}', 'AdminController@examReport', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/reports/export/csv', 'AdminController@exportCSV', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/admin/reports/export/pdf', 'AdminController@exportPDF', ['AuthMiddleware', 'AdminMiddleware']);

// Admin - Settings
$router->get('/admin/settings', 'AdminController@settings', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/settings/general', 'AdminController@updateGeneralSettings', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/settings/school', 'AdminController@updateSchoolSettings', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/settings/security', 'AdminController@updateSecuritySettings', ['AuthMiddleware', 'AdminMiddleware']);
$router->post('/admin/settings/backup/create', 'AdminController@createBackup', ['AuthMiddleware', 'AdminMiddleware']);

// Student routes (protected)
$router->get('/student', 'StudentController@dashboard', ['AuthMiddleware', 'StudentMiddleware']);
$router->get('/student/dashboard', 'StudentController@dashboard', ['AuthMiddleware', 'StudentMiddleware']);
$router->get('/student/profile', 'StudentController@profile', ['AuthMiddleware', 'StudentMiddleware']);
$router->post('/student/profile', 'StudentController@updateProfile', ['AuthMiddleware', 'StudentMiddleware']);
$router->get('/student/attendance', 'StudentController@attendance', ['AuthMiddleware', 'StudentMiddleware']);
$router->get('/student/results', 'StudentController@results', ['AuthMiddleware', 'StudentMiddleware']);
$router->get('/student/fees', 'StudentController@fees', ['AuthMiddleware', 'StudentMiddleware']);
$router->get('/student/events', 'StudentController@events', ['AuthMiddleware', 'StudentMiddleware']);

// API routes
$router->get('/api/v1/students', 'ApiController@getStudents', ['AuthMiddleware', 'AdminMiddleware']);
$router->get('/api/v1/attendance', 'ApiController@getAttendance', ['AuthMiddleware']);
$router->get('/api/v1/fees', 'ApiController@getFees', ['AuthMiddleware']);
$router->post('/api/v1/attendance', 'ApiController@markAttendance', ['AuthMiddleware', 'AdminMiddleware']);

// Dispatch the request
try {
    $router->dispatch();
} catch (Exception $e) {
    // Log the error
    error_log('Application Error: ' . $e->getMessage());

    // Show error page
    if ($config['debug']) {
        echo '<h1>Error</h1><pre>' . $e->getMessage() . '</pre><pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>Internal Server Error</h1><p>Something went wrong. Please try again later.</p>';
    }
}

// Flush output buffer
ob_end_flush();
?>