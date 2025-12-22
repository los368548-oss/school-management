# School Management System - Developer Guide

## Architecture Overview

The School Management System is built using PHP 8.1+ with a custom MVC (Model-View-Controller) framework. The system follows modern PHP development practices and includes comprehensive security features.

### Core Components

```
school-management/
├── core/           # Framework core
├── controllers/    # Request handlers
├── models/         # Data models
├── views/          # Presentation layer
├── config/         # Configuration files
├── middleware/     # Request processing
├── helpers/        # Utility functions
├── libraries/      # Third-party integrations
├── api/           # REST API endpoints
└── docs/          # Documentation
```

## Framework Structure

### MVC Architecture

#### Models
- Located in `models/` directory
- Extend `BaseModel` class
- Handle database operations and business logic
- Include data validation and relationships

#### Views
- Located in `views/` directory
- PHP templates with HTML/CSS/JS
- Receive data from controllers
- Include reusable components

#### Controllers
- Located in `controllers/` directory
- Extend `BaseController` class
- Handle HTTP requests and responses
- Coordinate between models and views

### Core Classes

#### Database Class
```php
// Singleton pattern for database connections
$db = Database::getInstance();

// Query methods
$user = $db->selectOne("SELECT * FROM users WHERE id = ?", [$id]);
$users = $db->select("SELECT * FROM users WHERE status = ?", ['active']);
$db->insert('users', $userData);
$db->update('users', $updateData, 'id = ?', [$id]);
$db->delete('users', 'id = ?', [$id]);
```

#### Router Class
```php
// Define routes in core/Router.php
'admin/dashboard' => ['controller' => 'AdminController', 'action' => 'dashboard', 'middleware' => 'Auth']

// Route parameters
'student/profile/{id}' => ['controller' => 'StudentController', 'action' => 'profile']
```

#### Security Class
```php
// CSRF protection
$token = Security::generateCSRFToken();
Security::validateCSRFToken($token);

// Password hashing
$hash = Security::hashPassword($password);
$valid = Security::verifyPassword($password, $hash);

// Session management
Security::isLoggedIn();
Security::getCurrentUser();
Security::requireRole('admin');
```

## Database Design

### Schema Overview

The system uses MySQL 8.0+ with the following key tables:

- `academic_years` - Academic year management ⭐
- `users` - User accounts and authentication
- `user_profiles` - Extended user information
- `students` - Student records (scoped by academic year)
- `classes` - Class definitions
- `subjects` - Subject catalog
- `attendance` - Daily attendance records
- `exams` - Examination definitions
- `exam_results` - Student marks and grades
- `fees` - Fee structure definitions
- `fee_payments` - Payment transactions
- `events` - School events and announcements
- `gallery` - Photo gallery management

### Key Relationships

```
users (1) ──── (M) user_profiles
users (1) ──── (M) students
classes (1) ──── (M) students
classes (1) ──── (M) class_subjects (M) ──── (1) subjects
students (1) ──── (M) attendance
students (1) ──── (M) exam_results
students (1) ──── (M) fee_payments (M) ──── (1) fees
academic_years (1) ──── (M) [most tables for scoping]
```

### Academic Year Scoping ⭐

**Critical Feature**: All data operations are scoped by academic year:

```php
// In models, always scope by academic year
class Student extends BaseModel {
    public function getForCurrentYear() {
        $academicYearId = $this->getCurrentAcademicYearId();
        return $this->where(['academic_year_id' => $academicYearId]);
    }
}
```

## API Development

### RESTful API Structure

All API endpoints follow REST conventions:

```
GET    /api/v1/students     # List students
POST   /api/v1/students     # Create student
GET    /api/v1/students/1   # Get specific student
PUT    /api/v1/students/1   # Update student
DELETE /api/v1/students/1   # Delete student
```

### API Authentication

```php
// Token-based authentication
$headers = getallheaders();
$token = $headers['Authorization'] ?? $headers['X-API-Token'] ?? null;

// Validate token
$userId = Session::get('api_token_' . $token);
```

### API Response Format

```php
// Success response
[
    'success' => true,
    'data' => [...],
    'message' => 'Operation completed'
]

// Error response
[
    'success' => false,
    'error' => 'Error message',
    'code' => 400
]
```

## Security Implementation

### Authentication & Authorization

```php
// Role-based access control
class AdminController extends BaseController {
    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }
}
```

### Input Validation

```php
// Comprehensive validation
$validator = new Validator($inputData);
$rules = [
    'email' => 'required|email',
    'password' => 'required|min:8',
    'age' => 'required|integer|min:5|max:100'
];

if ($validator->validate($rules)) {
    // Process data
} else {
    $errors = $validator->getErrors();
}
```

### CSRF Protection

```php
// Generate token
$csrfToken = Security::generateCSRFToken();

// Validate token
if (!Security::validateCSRFToken($_POST['csrf_token'])) {
    die('CSRF validation failed');
}
```

## File Upload Handling

### Image Uploads

```php
// Handle file uploads
if (!empty($_FILES['profile_image'])) {
    $file = $_FILES['profile_image'];

    // Validate file
    if (!is_image($file['name'])) {
        $errors[] = 'Invalid file type';
    }

    // Move uploaded file
    $uploadDir = BASE_PATH . 'uploads/profiles/';
    $fileName = uniqid('profile_') . '.' . get_file_extension($file['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $userData['profile_image'] = $fileName;
    }
}
```

### File Security

- File type validation
- Size limits (5MB for images, 10MB for documents)
- Secure file naming
- Directory permissions (755)
- File access restrictions via .htaccess

## Email Integration

### PHPMailer Setup

```php
// Send welcome email
$emailService = new EmailService();
$emailService->sendWelcomeEmail(
    $userEmail,
    $userName,
    $password
);

// Send fee reminder
$emailService->sendFeeReminder(
    $studentEmail,
    $studentName,
    $pendingAmount
);
```

### Email Templates

Emails use HTML templates with fallback text versions:

- Welcome emails for new users
- Password reset notifications
- Fee payment reminders
- Exam result notifications

## Print System (TCPDF)

### Document Generation

```php
// Generate marksheet
require_once 'libraries/TCPDF.php';

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf->SetCreator('School Management System');

// Add content
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Student details
$pdf->Cell(0, 10, 'Student Name: ' . $studentName, 0, 1);
$pdf->Cell(0, 10, 'Class: ' . $className, 0, 1);

// Marks table
// ... table generation code ...

$pdf->Output('marksheet_' . $studentId . '.pdf', 'D');
```

### Supported Documents

- **Admit Cards**: Exam admission cards
- **Marksheets**: Detailed result sheets
- **Transfer Certificates**: Student transfer docs
- **Fee Receipts**: Payment receipts
- **ID Cards**: Student identification cards

## Chart Generation (Chart.js)

### Dynamic Charts

```php
// Generate enrollment chart data
$chartData = ChartGenerator::getEnrollmentChartData();

// Returns structured data for Chart.js
[
    'type' => 'bar',
    'data' => [
        'labels' => ['Class 1', 'Class 2', ...],
        'datasets' => [[
            'label' => 'Students',
            'data' => [45, 42, ...],
            'backgroundColor' => 'rgba(13, 110, 253, 0.8)'
        ]]
    ]
]
```

### Available Charts

- Student enrollment by class
- Monthly fee collection trends
- Attendance statistics
- Exam performance analysis
- Revenue and expense tracking

## Helper Functions

### Common Utilities

```php
// URL and routing
base_url('admin/dashboard');
current_url();
redirect('/login');

// Form handling
old('username');
set_old_input($_POST);
csrf_token();

// Data formatting
format_currency(5000, '₹');
format_date('2024-01-15', 'd/m/Y');
calculate_age('2008-05-15');

// String utilities
slugify('School Management System');
truncate('Long text here...', 50);

// User and session
is_logged_in();
current_user();
has_role('admin');
current_academic_year();

// Business logic
generate_receipt_number();
generate_scholar_number();
get_grade(85); // Returns 'A'
calculate_percentage(425, 500); // Returns 85.00
```

## Error Handling & Logging

### Error Logging

```php
// Application errors
error_log('Database connection failed: ' . $e->getMessage(), 3, BASE_PATH . 'logs/error.log');

// Security events
Security::logActivity('login_failed', 'Invalid password for user: ' . $username);

// Audit trail
$this->logActivity('student_created', 'Student ID: ' . $studentId);
```

### Exception Handling

```php
try {
    // Database operation
    $student = $studentModel->create($studentData);
} catch (Exception $e) {
    // Log error
    error_log('Student creation failed: ' . $e->getMessage());

    // User-friendly message
    $this->setFlash('error', 'Failed to create student record');
    $this->redirect('/admin/students');
}
```

## Performance Optimization

### Database Optimization

- **Indexing**: Proper indexes on frequently queried columns
- **Prepared Statements**: All queries use parameterized statements
- **Connection Pooling**: Singleton database connection
- **Query Optimization**: Efficient JOIN operations

### Caching Strategies

```php
// File-based caching for configuration
$config = require BASE_PATH . 'config/app.php';

// Session caching for user data
$_SESSION['user_data'] = $userData;

// Database result caching (implement as needed)
```

### Code Optimization

- **Autoloading**: PSR-4 autoloading for classes
- **Minification**: Compressed CSS/JS in production
- **Lazy Loading**: Images and content loaded on demand
- **CDN Support**: External resources for better performance

## Testing & Quality Assurance

### Unit Testing Structure

```
tests/
├── Unit/
│   ├── Models/
│   │   ├── UserTest.php
│   │   ├── StudentTest.php
│   │   └── AcademicYearTest.php
│   ├── Controllers/
│   │   ├── AuthControllerTest.php
│   │   └── AdminControllerTest.php
│   └── Helpers/
│       └── FunctionsTest.php
├── Integration/
│   ├── ApiTest.php
│   └── DatabaseTest.php
└── Feature/
    ├── AuthenticationTest.php
    └── StudentManagementTest.php
```

### Running Tests

```bash
# Install PHPUnit
composer require --dev phpunit/phpunit

# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit tests/Unit/Models/

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

## Deployment

### Production Setup

1. **Server Requirements**
   - PHP 8.1+ with required extensions
   - MySQL 8.0+
   - Apache/Nginx with mod_rewrite
   - SSL certificate for HTTPS

2. **File Permissions**
   ```bash
   chmod 755 .
   chmod 755 logs/
   chmod 755 uploads/
   chmod 644 *.php
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   # Edit .env with production values
   ```

4. **Database Setup**
   ```bash
   mysql -u username -p database_name < database/schema.sql
   ```

5. **Web Server Configuration**
   ```apache
   <VirtualHost *:80>
       ServerName your-school.com
       DocumentRoot /path/to/school-management
       <Directory /path/to/school-management>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

### Security Checklist

- [ ] HTTPS enabled
- [ ] File permissions set correctly
- [ ] Default passwords changed
- [ ] Debug mode disabled
- [ ] Error reporting configured
- [ ] Database credentials secured
- [ ] Backup system in place
- [ ] Firewall configured
- [ ] Regular security updates

## Contributing

### Development Workflow

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/new-feature`)
3. **Commit** changes (`git commit -am 'Add new feature'`)
4. **Push** to branch (`git push origin feature/new-feature`)
5. **Create** Pull Request

### Code Standards

- **PSR-12** coding standards
- **Meaningful** commit messages
- **Comprehensive** documentation
- **Unit tests** for new features
- **Security** best practices

### Pull Request Guidelines

- **Descriptive** title and description
- **Related issues** referenced
- **Tests included** for new functionality
- **Documentation updated** if needed
- **Breaking changes** clearly marked

## Support & Resources

### Getting Help

- **Documentation**: Check this guide and API docs
- **Issues**: GitHub Issues for bug reports
- **Discussions**: Community forum for questions
- **Email**: developer@schoolmanagement.com

### Additional Resources

- **PHP Documentation**: php.net/manual
- **MySQL Reference**: dev.mysql.com/doc
- **Security Best Practices**: owasp.org
- **Web Standards**: w3.org

---

*Developer guide last updated: January 2024*