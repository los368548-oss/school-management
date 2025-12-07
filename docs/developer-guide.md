# School Management System - Developer Guide

## Architecture Overview

The School Management System follows the MVC (Model-View-Controller) architectural pattern with additional layers for security, routing, and API functionality.

### Core Components

#### MVC Structure
- **Models**: Handle database operations and business logic
- **Views**: Manage presentation and user interface
- **Controllers**: Process requests and coordinate between models and views

#### Additional Layers
- **Core**: Framework foundation (Database, Router, Session, Security, Validator)
- **Middleware**: Request processing and security
- **API**: RESTful endpoints for AJAX functionality
- **Helpers**: Utility functions and configurations

## File Structure

```
school-management/
├── index.php              # Main application entry point
├── install.php            # Installation wizard
├── core/                  # Framework core
│   ├── Database.php       # Database abstraction layer
│   ├── Router.php         # URL routing system
│   ├── Session.php        # Session management
│   ├── Security.php       # Security utilities
│   └── Validator.php      # Input validation
├── controllers/           # Request handlers
├── models/               # Data layer
├── views/                # Presentation layer
├── middleware/           # Security & routing
├── config/               # System configuration
├── assets/               # Static resources
├── database/             # Database schema & migrations
├── docs/                 # Documentation
└── logs/                 # Application logs
```

## Database Design

### Core Tables

#### Users & Authentication
- `users`: User accounts with role-based access
- `user_roles`: Role definitions (Admin, Student)
- `permissions`: Granular permissions per role

#### Academic Structure
- `classes`: Class definitions with academic year
- `subjects`: Subject catalog
- `class_subjects`: Subject assignments to classes

#### Student Management
- `students`: Complete student profiles
- `attendance`: Daily attendance records
- `exams`: Examination definitions
- `exam_results`: Student marks and grades

#### Financial Management
- `fees`: Fee structure definitions
- `fee_payments`: Payment transactions
- `expenses`: School expense tracking

#### Content Management
- `events`: School events and announcements
- `gallery`: Media files with categorization
- `news`: News articles
- `homepage_content`: Dynamic website content

#### System Management
- `settings`: System configuration
- `audit_logs`: Activity tracking
- `permissions`: Access control matrix

## API Endpoints

### Authentication Required Endpoints

#### Dashboard Statistics
```
GET /api/v1/dashboard/stats
Authorization: Admin required
Response: System statistics and metrics
```

#### Student Management
```
GET /api/v1/students
Authorization: Admin required
Query: page, per_page, search, class_id
Response: Paginated student list
```

#### Attendance Management
```
GET /api/v1/attendance
Authorization: Required
Query: date, class_id
Response: Attendance data for class/date

POST /api/v1/attendance
Authorization: Admin required
Body: student_id, date, status
Response: Attendance marking confirmation
```

#### Fee Management
```
GET /api/v1/fees
Authorization: Required
Query: student_id
Response: Fee status and payment history
```

## Security Implementation

### Authentication
- Secure password hashing using bcrypt
- Session-based authentication with timeout
- Role-based access control (RBAC)

### Authorization
- Middleware-based route protection
- Permission checking per action
- CSRF protection on all forms

### Input Validation
- Server-side validation using Validator class
- Sanitization of all user inputs
- Type checking and length validation

### Security Headers
- CSRF tokens on all forms
- XSS prevention through output escaping
- SQL injection prevention with prepared statements

## Development Workflow

### Setting Up Development Environment

1. **Prerequisites**
   - PHP 8.1+
   - MySQL 8.0+
   - Apache/Nginx web server
   - Composer (optional)

2. **Installation**
   ```bash
   # Clone repository
   git clone [repository-url]

   # Set permissions
   chmod 755 .
   chmod 755 logs/
   chmod 755 uploads/

   # Run installation
   # Access install.php in browser
   ```

3. **Database Setup**
   ```sql
   -- Create database
   CREATE DATABASE school_management;

   -- Import schema
   mysql -u username -p school_management < database/schema.sql
   ```

### Code Standards

#### PHP Standards
- PSR-12 coding standards
- Meaningful variable and function names
- Comprehensive documentation
- Error handling with try-catch blocks

#### File Organization
- One class per file
- Consistent naming conventions
- Logical folder structure
- Separation of concerns

#### Database Standards
- Foreign key constraints
- Indexed columns for performance
- Consistent naming (snake_case)
- Data type optimization

### Adding New Features

#### 1. Database Changes
```sql
-- Add new table
CREATE TABLE new_feature (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add to schema.sql
-- Update relevant models
```

#### 2. Model Creation
```php
class NewFeature extends BaseModel {
    protected $table = 'new_feature';
    protected $fillable = ['name'];

    public function customMethod() {
        // Business logic here
    }
}
```

#### 3. Controller Methods
```php
class AdminController extends BaseController {
    public function newFeature() {
        $model = new NewFeature();
        $data = $model->all();

        $this->view('admin/new-feature', [
            'data' => $data,
            'csrf_token' => $this->session->generateCsrfToken()
        ]);
    }
}
```

#### 4. Routes
```php
// Add to index.php
$router->get('/admin/new-feature', 'AdminController@newFeature', ['AuthMiddleware', 'AdminMiddleware']);
```

#### 5. Views
```php
<!-- views/admin/new-feature.php -->
<div class="container">
    <h1>New Feature</h1>
    <!-- Implementation -->
</div>
```

### Testing Guidelines

#### Unit Testing
- Test individual functions and methods
- Mock external dependencies
- Test edge cases and error conditions

#### Integration Testing
- Test complete workflows
- Verify database operations
- Test API endpoints

#### User Acceptance Testing
- Test from user perspective
- Verify business requirements
- Check cross-browser compatibility

### Debugging

#### Error Logging
```php
// Log errors
error_log('Debug message: ' . $variable);

// Check logs/error.log
tail -f logs/error.log
```

#### Debug Mode
```php
// Enable in config/app.php
'debug' => true,

// Debug variables
var_dump($variable);
print_r($array);
```

#### Database Debugging
```php
// Enable query logging
$this->db->setDebug(true);

// Check query results
$result = $this->db->query("SELECT * FROM table")->resultSet();
var_dump($result);
```

## Deployment

### Production Server Requirements

- PHP 8.1+ with required extensions
- MySQL 8.0+
- SSL certificate
- Proper file permissions
- Backup strategy

### Deployment Steps

1. **Code Deployment**
   ```bash
   # Upload files
   rsync -avz . user@server:/path/to/app/

   # Set permissions
   chown -R www-data:www-data /path/to/app/
   chmod -R 755 /path/to/app/
   ```

2. **Database Migration**
   ```bash
   # Backup existing database
   mysqldump -u user -p database > backup.sql

   # Run migrations if any
   php migrate.php
   ```

3. **Configuration**
   ```bash
   # Update config files
   cp config/database.example.php config/database.php
   # Edit database credentials

   cp config/app.example.php config/app.php
   # Set production settings
   ```

4. **Web Server Configuration**
   ```apache
   # Apache .htaccess
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteBase /
       RewriteRule ^index\.php$ - [L]
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteRule . /index.php [L]
   </IfModule>
   ```

### Performance Optimization

#### Database Optimization
- Use indexes on frequently queried columns
- Optimize queries with EXPLAIN
- Implement query caching
- Regular database maintenance

#### Code Optimization
- Enable opcode caching (OPcache)
- Minify CSS and JavaScript
- Implement file caching
- Use CDN for static assets

#### Server Optimization
- Configure PHP-FPM
- Set up reverse proxy (Nginx)
- Implement SSL/TLS
- Configure caching headers

## Contributing

### Code Contribution Process

1. **Fork Repository**
2. **Create Feature Branch**
   ```bash
   git checkout -b feature/new-feature
   ```
3. **Make Changes**
   - Follow coding standards
   - Add tests
   - Update documentation
4. **Commit Changes**
   ```bash
   git commit -m "Add new feature"
   ```
5. **Push and Create Pull Request**
   ```bash
   git push origin feature/new-feature
   ```

### Documentation Updates

- Update this guide for new features
- Add inline code comments
- Update API documentation
- Maintain changelog

### Code Review Checklist

- [ ] Code follows PSR-12 standards
- [ ] Unit tests added/updated
- [ ] Documentation updated
- [ ] Security considerations addressed
- [ ] Performance impact assessed
- [ ] Cross-browser compatibility checked

## Troubleshooting

### Common Development Issues

#### Database Connection Issues
```php
// Check database config
$config = require 'config/database.php';
print_r($config);

// Test connection
try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['database']}",
                   $config['username'], $config['password']);
    echo "Connection successful";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
```

#### Permission Issues
```bash
# Check file permissions
ls -la /path/to/app/

# Fix permissions
chmod -R 755 /path/to/app/
chown -R www-data:www-data /path/to/app/
```

#### Routing Issues
```php
// Debug routing
echo "Current URI: " . $_SERVER['REQUEST_URI'];
echo "Request Method: " . $_SERVER['REQUEST_METHOD'];

// Check routes in index.php
```

### Getting Help

1. **Documentation**: Check this guide and inline comments
2. **Logs**: Review error logs in `logs/` directory
3. **Community**: Check forums and issue trackers
4. **Professional Support**: Contact development team

---

**Version 1.0.0**
*School Management System Developer Guide*