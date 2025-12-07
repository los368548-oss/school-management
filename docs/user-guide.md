# School Management System - User Guide

## Overview

The School Management System is a comprehensive web-based application designed to streamline educational institution operations. It provides separate interfaces for administrators and students with role-based access control.

## Getting Started

### Installation

1. Upload all files to your web server
2. Set proper permissions (755 for directories, 644 for files)
3. Access `install.php` in your browser
4. Follow the installation wizard:
   - System requirements check
   - Database configuration
   - Administrator account setup

### Default Login Credentials

- **Admin**: username: `admin`, password: `admin123`
- **Student**: username: `student1`, password: `student123`

## Admin Panel

### Dashboard

The admin dashboard provides:
- System statistics (total students, classes, teachers)
- Recent activities log
- Quick action buttons
- Today's attendance overview
- Pending fees summary

### Student Management

#### Adding Students
1. Navigate to Students → Add Student
2. Fill in all required fields:
   - Scholar Number (unique)
   - Admission Number (unique)
   - Personal details (name, DOB, gender, etc.)
   - Contact information
   - Class and section assignment
3. Upload student photo (optional)
4. Save the record

#### Managing Students
- View all students with search and filter options
- Edit student information
- View detailed student profiles
- Delete students (only if no associated records)

### Class Management

#### Creating Classes
1. Go to Classes & Subjects
2. Click "Add Class"
3. Enter class details:
   - Name (e.g., "10th")
   - Section (e.g., "A")
   - Academic Year
   - Class Teacher (optional)

#### Managing Subjects
- Add subjects with code and description
- Assign subjects to classes
- Remove subjects from classes

### Attendance Management

#### Marking Attendance
1. Go to Attendance section
2. Select class and date
3. Mark attendance for each student:
   - Present
   - Absent
   - Late
4. Save changes

#### Viewing Reports
- Generate attendance reports by class
- View attendance percentages
- Export reports to PDF/Excel

### Fee Management

#### Setting Up Fees
1. Navigate to Fees
2. Create fee structures per class
3. Set amounts and due dates

#### Fee Collection
1. Select student
2. Enter payment details:
   - Amount paid
   - Payment mode (Cash/Online/Cheque/UPI)
   - Transaction details
3. Generate receipt

### Event Management

#### Creating Events
1. Go to Events → Add Event
2. Fill in event details:
   - Title and description
   - Date and location
   - Upload event image
3. Publish the event

### Gallery Management

#### Uploading Images
1. Navigate to Gallery
2. Click "Upload Images"
3. Select multiple images
4. Add titles, descriptions, and categories
5. Save uploads

### Settings

#### General Settings
- Application name
- Timezone
- Default language
- Items per page

#### School Information
- School name and address
- Contact details
- Academic year
- Description

#### Security Settings
- Session timeout
- Password requirements
- Login attempt limits
- Two-factor authentication

## Student Portal

### Dashboard

The student dashboard shows:
- Personal attendance percentage
- Recent exam results
- Fee payment status
- Upcoming events
- Quick action buttons

### Profile Management

#### Updating Profile
1. Go to My Profile
2. Update contact information
3. Change contact details
4. Save changes

### Attendance View

- View monthly attendance calendar
- See attendance statistics
- Check present/absent/late counts

### Exam Results

- View all exam results
- See subject-wise marks
- Check grades and percentages

### Fee Information

- View total fees and payments
- Check pending amounts
- View payment history
- Download receipts

### Events

- Browse upcoming school events
- View event details and locations
- See past events

## Public Website

### Homepage Features

- Dynamic carousel images
- About section
- Course information
- Upcoming events
- Photo gallery
- Contact form

### Navigation

- Home
- About Us
- Courses
- Events
- Gallery
- Contact

## Security Features

### Password Security
- Minimum 8 characters required
- Secure password hashing
- Regular password change prompts

### Session Management
- Automatic logout on inactivity
- Secure session handling
- CSRF protection on all forms

### Access Control
- Role-based permissions
- Admin and Student roles
- Protected routes and actions

## Troubleshooting

### Common Issues

#### Login Problems
- Check username and password
- Ensure account is active
- Contact administrator if locked out

#### File Upload Issues
- Check file size limits
- Ensure correct file formats
- Verify upload directory permissions

#### Database Connection
- Verify database credentials
- Check MySQL service status
- Ensure proper user permissions

### Getting Help

1. Check this documentation first
2. Review error logs in `logs/` directory
3. Contact system administrator
4. Check community forums

## Best Practices

### Data Management
- Regular backups of database
- Export important reports
- Keep student data updated

### Security
- Change default passwords immediately
- Use strong passwords
- Log out when not using the system
- Report suspicious activities

### Performance
- Clear browser cache regularly
- Use supported browsers
- Report slow loading issues

---

**Version 1.0.0**
*School Management System User Guide*