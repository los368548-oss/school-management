# School Management System - Administrator Manual

## Table of Contents
1. [Getting Started](#getting-started)
2. [System Login](#system-login)
3. [Academic Year Management](#academic-year-management)
4. [Dashboard Overview](#dashboard-overview)
5. [Student Management](#student-management)
6. [Class & Subject Management](#class--subject-management)
7. [Attendance Management](#attendance-management)
8. [Examination Management](#examination-management)
9. [Fee Management](#fee-management)
10. [Content Management](#content-management)
11. [Reports & Analytics](#reports--analytics)
12. [Settings & Configuration](#settings--configuration)
13. [Troubleshooting](#troubleshooting)

## Getting Started

### System Requirements
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 8.1 or higher
- **Database**: MySQL 8.0 or higher
- **Browser**: Modern web browser (Chrome, Firefox, Safari, Edge)

### Installation
1. Upload all files to your web server
2. Set proper file permissions (755 for directories, 644 for files)
3. Access `http://your-domain.com/install.php`
4. Follow the installation wizard:
   - Check system requirements
   - Configure database connection
   - Create administrator account
   - Set up academic year
5. Access the admin panel at `http://your-domain.com`

## System Login

### Accessing the Admin Panel
1. Navigate to your school's website
2. Click "Admin Login" or go to `/admin`
3. Enter your administrator credentials
4. **Important**: After login, select your current academic year

### Academic Year Selection
- **Required**: You must select an academic year before accessing other features
- **Purpose**: All data operations are scoped to the selected academic year
- **Changing Years**: Use the "Change Year" button in the top navigation

## Academic Year Management

### Creating Academic Years
1. Go to Settings → Academic Years
2. Click "Add New Academic Year"
3. Enter:
   - Year name (e.g., "2024-2025")
   - Start date
   - End date
4. Set as active if this is the current year

### Switching Between Years
- Use the dropdown in the top navigation
- All data will be filtered by the selected year
- Historical data remains accessible by changing years

## Dashboard Overview

### Key Metrics
- **Total Students**: Current enrollment count
- **Total Classes**: Number of active classes
- **Total Exams**: Examinations this year
- **Pending Fees**: Outstanding payments

### Quick Actions
- Add new students, classes, or events
- Mark attendance or collect fees
- Generate reports

### Recent Activity
- View system activity log
- Monitor user actions
- Track recent changes

## Student Management

### Adding New Students
1. Navigate to Students → Add Student
2. Fill in required information:
   - **Personal Details**: Name, date of birth, gender, contact info
   - **Academic Info**: Scholar number, admission details, class assignment
   - **Guardian Details**: Parent/guardian information
   - **Documents**: Aadhaar, caste certificate, etc.
3. Upload student photo (optional)
4. Save the record

### Managing Student Records
- **Search & Filter**: Find students by name, class, or scholar number
- **Edit Information**: Update student details as needed
- **Transfer/Promote**: Move students between classes
- **Generate Documents**: Print ID cards, transfer certificates

### Bulk Operations
- **Import Students**: Upload CSV files for bulk enrollment
- **Export Data**: Download student lists in Excel/CSV format
- **Bulk Updates**: Change class assignments or status

## Class & Subject Management

### Creating Classes
1. Go to Classes → Add Class
2. Enter:
   - Class name (e.g., "Class 1", "Grade 10")
   - Section (A, B, C, etc.)
   - Capacity
   - Class teacher assignment

### Managing Subjects
1. Navigate to Classes → Subjects
2. Add subjects with:
   - Subject name and code
   - Description
   - Status (active/inactive)

### Subject Allocation
- Assign subjects to specific classes
- Set subject teachers
- Configure subject-wise assessments

## Attendance Management

### Daily Attendance Marking
1. Go to Attendance → Mark Attendance
2. Select class and date
3. Mark each student as:
   - **Present**: Student attended
   - **Absent**: Student was absent
   - **Late**: Student arrived late
4. Add remarks if needed
5. Save attendance

### Bulk Attendance
- Upload CSV files for multiple days
- Import attendance from external systems
- Automated attendance marking for special cases

### Attendance Reports
- **Class-wise Reports**: Attendance by class and date range
- **Student-wise Reports**: Individual student attendance history
- **Monthly Reports**: Consolidated monthly attendance
- **Defaulters List**: Students with low attendance

## Examination Management

### Setting Up Examinations
1. Navigate to Exams → Add Exam
2. Configure:
   - Exam name and type (mid-term, final, unit test)
   - Class and academic year
   - Start and end dates
   - Status (upcoming, ongoing, completed)

### Subject Scheduling
1. For each exam, add subjects with:
   - Subject selection
   - Exam date and time
   - Maximum marks
   - Passing marks

### Result Entry
1. Go to Exams → Enter Results
2. Select exam and student
3. Enter marks for each subject
4. System calculates grades and percentages
5. Generate marksheets automatically

### Document Generation
- **Admit Cards**: Print admit cards for students
- **Marksheets**: Generate detailed result sheets
- **Report Cards**: Comprehensive academic reports

## Fee Management

### Setting Up Fee Structure
1. Go to Fees → Fee Structure
2. Add fee types:
   - Tuition fees, transport fees, exam fees
   - One-time or recurring payments
   - Class-specific or general fees
3. Set amounts and due dates

### Fee Collection
1. Navigate to Fees → Collect Payment
2. Select student and fee type
3. Enter payment details:
   - Amount paid
   - Payment mode (cash, online, cheque)
   - Transaction/reference number
4. Generate receipt automatically

### Fee Reports
- **Collection Reports**: Payment history and trends
- **Outstanding Fees**: Pending payments by student/class
- **Monthly Reports**: Revenue analysis
- **Defaulters**: Students with overdue payments

## Content Management

### Managing Events
1. Go to Events → Add Event
2. Create events with:
   - Title, description, date, and venue
   - Event type (academic, sports, cultural)
   - Featured images
3. Set visibility (public or internal)

### Photo Gallery
1. Navigate to Gallery → Upload Photos
2. Upload multiple images
3. Organize by:
   - Categories (academic, sports, events)
   - Events
   - Academic years
4. Set featured images

### Public Website Content
- Manage homepage content
- Update school information
- Post announcements
- Control public visibility

## Reports & Analytics

### Available Reports
- **Student Reports**: Enrollment statistics, demographics
- **Attendance Reports**: Daily, monthly, and yearly attendance
- **Fee Reports**: Collection analysis, outstanding payments
- **Exam Reports**: Performance analysis, grade distribution
- **Custom Reports**: Filter by date, class, or other criteria

### Export Options
- **PDF Reports**: Professional formatted documents
- **Excel/CSV**: Data for further analysis
- **Print**: Direct printing capabilities

### Analytics Dashboard
- Interactive charts and graphs
- Real-time statistics
- Trend analysis
- Performance indicators

## Settings & Configuration

### School Information
- Update school name, address, contact details
- Configure logo and branding
- Set academic year preferences

### User Management
- Create additional admin accounts
- Manage user roles and permissions
- Reset passwords
- View user activity logs

### System Configuration
- Email settings for notifications
- File upload limits and types
- Security settings
- Backup configurations

## Troubleshooting

### Common Issues

#### Login Problems
- **Issue**: Cannot log in
- **Solution**: Check username/password, ensure account is active
- **Issue**: "Academic year not selected"
- **Solution**: Select current academic year after login

#### Data Not Showing
- **Issue**: No student/fee data visible
- **Solution**: Check selected academic year, verify data exists for that year
- **Issue**: Reports show no data
- **Solution**: Ensure date ranges and filters are correct

#### File Upload Issues
- **Issue**: Cannot upload photos/documents
- **Solution**: Check file permissions, verify file types and sizes
- **Issue**: Images not displaying
- **Solution**: Check uploads directory permissions

#### Performance Issues
- **Issue**: System running slow
- **Solution**: Clear browser cache, check database connections
- **Issue**: Large reports taking time
- **Solution**: Use filters to limit data, export during off-peak hours

### Getting Help

#### Support Resources
- **User Manual**: This document
- **Online Help**: Check tooltips and help icons
- **Error Logs**: Check `logs/error.log` for technical issues
- **System Status**: Monitor dashboard for system health

#### Contact Support
- **Technical Issues**: Check error logs and configuration
- **Data Issues**: Verify input data and database integrity
- **Feature Requests**: Document requirements for future updates

---

## Quick Reference

### Keyboard Shortcuts
- `Ctrl+S`: Save current form
- `Ctrl+F`: Search in tables
- `Esc`: Close modals

### Important URLs
- **Admin Dashboard**: `/admin/dashboard`
- **Student Management**: `/admin/students`
- **Fee Collection**: `/admin/fees`
- **Reports**: `/admin/reports`

### File Limits
- **Images**: 5MB maximum
- **Documents**: 10MB maximum
- **Bulk Uploads**: 100 records maximum

---

*This manual is updated regularly. Check for updates and additional resources.*