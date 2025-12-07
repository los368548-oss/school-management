# A.s.higher secondary school Management System

A comprehensive, modern, and feature-rich School Management System built with PHP, designed to streamline educational institution operations and enhance the learning experience for students and administrators.

## 🎯 Features

### 👥 Multi-Role Support
- **Admin Dashboard**: Complete administrative control
- **Student Portal**: Personal academic dashboard and records
- **Public Website**: Dynamic school website with news and events

### 📚 Academic Management
- Student enrollment and profile management
- Class and subject organization
- Attendance tracking with detailed reporting
- Examination management with result processing
- Fee collection and financial tracking

### 🌐 Content Management
- Dynamic public website with customizable content
- Event management with calendar integration
- Photo gallery with categorization
- News and announcements system

### 📊 Reporting & Analytics
- Comprehensive reports (students, attendance, fees, exams)
- PDF and CSV export capabilities
- Real-time statistics and dashboards

## 🚀 Quick Start

### Prerequisites
- PHP 8.1 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (optional, for dependency management)

### Installation

1. **Upload Files**
   ```
   Upload all files to your web server directory
   ```

2. **Set Permissions**
   ```bash
   chmod 755 .
   chmod 755 logs/
   chmod 755 uploads/
   ```

3. **Run Installation**
   - Open `http://your-domain.com/install.php` in your browser
   - Follow the 3-step installation wizard:
     - System requirements check
     - Database configuration
     - Administrator account setup

4. **Access System**
   - Admin login: `http://your-domain.com/login`
   - Default admin credentials:
     - Username: `admin`
     - Password: `admin123`

## 📁 Project Structure

```
school-management/
├── 🎯 Core Application
│   ├── index.php              # Main application entry point
│   ├── install.php            # Installation wizard
│   └── composer.json          # PHP dependencies
├── 🔧 Application Core (MVC)
│   ├── controllers/           # Request handlers
│   ├── core/                  # Framework foundation
│   ├── middleware/            # Security & routing
│   ├── models/                # Data layer
│   └── config/                # System configuration
├── 🌐 Interfaces
│   ├── admin/                 # Admin panel views
│   ├── student/               # Student portal views
│   └── public/                # Public website views
├── 📱 Assets
│   ├── assets/css/            # Bootstrap CSS files
│   ├── assets/js/             # Bootstrap JS files
│   └── assets/logos/schoollogs/ # School logo directory
├── 🗄️ Database
│   └── database/schema.sql    # Complete database structure
└── 📚 Documentation
    ├── docs/                  # Documentation files
    └── README.md             # This file
```

## 👤 User Roles & Permissions

### Administrator
- Full system access
- User management
- Student management
- Academic structure setup
- Financial management
- Content management
- System configuration

### Student
- View personal profile
- Check attendance records
- View exam results
- Monitor fee status
- Access school events
- Update contact information

## 📖 User Guide

### For Administrators

#### Dashboard
- View system statistics and quick actions
- Monitor recent activities
- Access all major modules

#### Student Management
1. Navigate to "Students" in the sidebar
2. Click "Add Student" to register new students
3. Use filters to search and manage student records
4. Import/export student data using CSV files

#### Attendance Management
1. Go to "Attendance" section
2. Select class and date
3. Mark attendance for individual students or bulk operations
4. Generate attendance reports

#### Fee Management
1. Access "Fees" module
2. Set up fee structures for different classes
3. Record payments and generate receipts
4. Monitor outstanding fees and send reminders

### For Students

#### Dashboard
- View attendance percentage
- Check recent exam results
- Monitor fee status
- See upcoming events

#### Profile Management
- Update contact information
- Change password
- View academic records

## 🛠️ Developer Guide

### Architecture
The system follows MVC (Model-View-Controller) architecture:

- **Models**: Handle database operations and business logic
- **Views**: Manage presentation and user interface
- **Controllers**: Process requests and coordinate between models and views

### Key Components

#### Database Layer
```php
// Example model usage
$userModel = new User();
$users = $userModel->all();
$user = $userModel->find(1);
```

#### Controller Structure
```php
class ExampleController extends BaseController {
    public function index() {
        $data = $this->model->all();
        $this->view('example/index', ['data' => $data]);
    }
}
```

#### Routing
Routes are defined in `index.php`:
```php
$router->get('/users', 'UserController@index');
$router->post('/users', 'UserController@store');
```

### Security Features
- CSRF protection on all forms
- Input validation and sanitization
- Role-based access control
- Secure session management
- Password hashing with bcrypt

### AJAX Implementation
```javascript
fetch('/api/students', {
    method: 'GET',
    headers: {
        'X-CSRF-Token': csrfToken
    }
})
.then(response => response.json())
.then(data => {
    // Handle response
});
```

## 🔧 Configuration

### Database Configuration (`config/database.php`)
```php
return [
    'host' => 'localhost',
    'database' => 'school_management',
    'username' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4'
];
```

### Application Settings (`config/app.php`)
```php
return [
    'name' => 'A.s.higher secondary school Management System',
    'debug' => false,
    'timezone' => 'Asia/Kolkata',
    // ... other settings
];
```

## 🔒 Security Best Practices

1. **Change Default Passwords**: Update admin credentials immediately after installation
2. **Use HTTPS**: Enable SSL/TLS for all connections
3. **Regular Backups**: Implement automated database backups
4. **File Permissions**: Set appropriate permissions for sensitive directories
5. **Input Validation**: Never trust user input, always validate and sanitize

## 🚀 Deployment

### Production Server Requirements
- PHP 8.1+
- MySQL 8.0+
- 500MB+ disk space
- SSL certificate recommended

### Web Server Configuration

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 🐛 Troubleshooting

### Common Issues

#### Database Connection Failed
- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check user permissions

#### File Upload Errors
- Check `uploads/` directory permissions (755)
- Verify PHP upload limits in `php.ini`
- Ensure sufficient disk space

#### Permission Errors
- Set proper file permissions:
  - Directories: 755
  - Files: 644
- Ensure web server user owns the files

## 📞 Support

### Getting Help
1. Check this documentation first
2. Review error logs in `logs/` directory
3. Check PHP and MySQL error logs
4. Contact system administrator

### Contributing
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📋 Version History

### Version 1.0.0
- Initial release
- Complete school management system
- Multi-role support (Admin/Student)
- Responsive Bootstrap interface
- Comprehensive reporting system

## 📄 License

This project is proprietary software. All rights reserved.

---

**A.s.higher secondary school Management System v1.0.0**
*Built with ❤️ for educational excellence*