# School Management System API Documentation

## Overview

The School Management System provides a comprehensive RESTful API for programmatic access to all school data and operations. The API enables mobile applications, external integrations, and automated workflows.

**Base URL**: `https://your-school-domain.com/api/v1/`

**Authentication**: Token-based authentication required for all endpoints except login

---

## Authentication

### POST /api/v1/auth/login

Authenticate user and receive API token.

**Request Body:**
```json
{
  "username": "admin",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "user": {
    "id": 1,
    "username": "admin",
    "role": "admin",
    "name": "Administrator"
  }
}
```

**Headers for Authenticated Requests:**
```
Authorization: Bearer {token}
X-API-Token: {token}
```

---

## Students API

### GET /api/v1/students

Retrieve students list for current academic year.

**Query Parameters:**
- `class_id` (optional): Filter by class
- `search` (optional): Search by name or scholar number

**Response:**
```json
{
  "success": true,
  "students": [
    {
      "id": 1,
      "scholar_number": "2024001",
      "admission_number": "ADM001",
      "name": "John Doe",
      "class": "Class 10 A",
      "roll_number": 1,
      "status": "active"
    }
  ]
}
```

### POST /api/v1/students

Create new student (Admin only).

**Request Body:**
```json
{
  "scholar_number": "2024001",
  "admission_number": "ADM001",
  "first_name": "John",
  "last_name": "Doe",
  "date_of_birth": "2008-05-15",
  "gender": "male",
  "class_id": 1,
  "father_name": "Robert Doe",
  "mother_name": "Jane Doe",
  "mobile_number": "9876543210",
  "email": "john@example.com"
}
```

**Response:**
```json
{
  "success": true,
  "student": {
    "id": 1,
    "scholar_number": "2024001",
    "name": "John Doe"
  }
}
```

---

## Fees API

### GET /api/v1/fees

Get fee structures.

**Response:**
```json
{
  "success": true,
  "fees": [
    {
      "id": 1,
      "fee_name": "Tuition Fee",
      "fee_type": "tuition",
      "amount": 5000.00,
      "frequency": "monthly",
      "class_name": "Class 10"
    }
  ]
}
```

### POST /api/v1/fees

Record fee payment (Admin only).

**Request Body:**
```json
{
  "student_id": 1,
  "fee_id": 1,
  "amount_paid": 5000.00,
  "payment_date": "2024-01-15",
  "payment_mode": "cash",
  "transaction_id": null,
  "remarks": "Monthly tuition fee"
}
```

**Response:**
```json
{
  "success": true,
  "payment": {
    "id": 1,
    "receipt_number": "20240115001",
    "amount_paid": 5000.00
  }
}
```

---

## Exams API

### GET /api/v1/exams

Get examinations list.

**Response:**
```json
{
  "success": true,
  "exams": [
    {
      "id": 1,
      "exam_name": "Mid Term Exam 2024",
      "exam_type": "mid_term",
      "class": "Class 10 A",
      "start_date": "2024-02-01",
      "end_date": "2024-02-05",
      "status": "upcoming",
      "subject_count": 6
    }
  ]
}
```

---

## Events API

### GET /api/v1/events

Get school events.

**Response:**
```json
{
  "success": true,
  "events": [
    {
      "id": 1,
      "title": "Annual Sports Day",
      "description": "Inter-house sports competition",
      "event_date": "2024-02-15",
      "event_time": "09:00:00",
      "venue": "School Ground",
      "event_type": "sports",
      "status": "upcoming",
      "is_public": true
    }
  ]
}
```

### POST /api/v1/events

Create new event (Admin only).

**Request Body:**
```json
{
  "title": "Parent Teacher Meeting",
  "description": "Monthly PTM for Class 10",
  "event_date": "2024-01-20",
  "event_time": "10:00:00",
  "venue": "School Auditorium",
  "event_type": "academic",
  "is_public": false
}
```

---

## Gallery API

### GET /api/v1/gallery

Get gallery images.

**Query Parameters:**
- `event_id` (optional): Filter by event
- `category` (optional): Filter by category

**Response:**
```json
{
  "success": true,
  "gallery": [
    {
      "id": 1,
      "title": "Sports Day Winners",
      "image_path": "gallery/sports_day_2024_001.jpg",
      "category": "sports",
      "event_title": "Annual Sports Day",
      "is_featured": true,
      "uploaded_by_name": "Administrator"
    }
  ]
}
```

### POST /api/v1/gallery

Upload images (Admin only).

**Request Body (Form Data):**
- `images[]`: Multiple image files
- `category`: Image category
- `event_id` (optional): Associated event
- `is_featured`: Whether to feature the image

**Response:**
```json
{
  "success": true,
  "uploaded": [
    {
      "id": 1,
      "title": "Image 1",
      "filename": "gallery_20240115_001.jpg"
    }
  ],
  "count": 1
}
```

### DELETE /api/v1/gallery?id={image_id}

Delete image (Admin only).

**Response:**
```json
{
  "success": true,
  "message": "Image deleted successfully"
}
```

---

## Reports API

### GET /api/v1/reports/students

Get student enrollment report.

**Query Parameters:**
- `class_id` (optional): Filter by class

**Response:**
```json
{
  "success": true,
  "report": [
    {
      "id": 1,
      "scholar_number": "2024001",
      "name": "John Doe",
      "class": "Class 10 A",
      "roll_number": 1,
      "status": "active"
    }
  ]
}
```

### GET /api/v1/reports/attendance

Get attendance report.

**Query Parameters:**
- `class_id` (optional): Filter by class
- `from_date`: Start date (required)
- `to_date`: End date (required)

**Response:**
```json
{
  "success": true,
  "report": [
    {
      "class_name": "Class 10 A",
      "total_students": 45,
      "present_count": 42,
      "absent_count": 3,
      "percentage": 93.33
    }
  ]
}
```

### GET /api/v1/reports/fees

Get fee collection report.

**Query Parameters:**
- `from_date`: Start date (required)
- `to_date`: End date (required)

**Response:**
```json
{
  "success": true,
  "report": [
    {
      "class_name": "Class 10 A",
      "total_students": 45,
      "total_fees": 225000.00,
      "collected_amount": 200000.00,
      "pending_amount": 25000.00
    }
  ]
}
```

---

## Error Handling

All API endpoints return standardized error responses:

**Authentication Error:**
```json
{
  "error": "Invalid API token",
  "code": 401
}
```

**Validation Error:**
```json
{
  "error": "Field 'email' is required",
  "code": 400
}
```

**Server Error:**
```json
{
  "error": "Internal server error",
  "code": 500
}
```

---

## Rate Limiting

- **Authenticated Requests**: 100 requests per 15 minutes
- **Unauthenticated Requests**: 10 requests per 15 minutes
- **Headers**: Check `X-RateLimit-*` headers for current limits

---

## Data Formats

### Date Format
All dates use ISO 8601 format: `YYYY-MM-DD`

### Time Format
Times use 24-hour format: `HH:MM:SS`

### Currency
All monetary values are in the smallest currency unit (e.g., paise for INR)

---

## SDKs and Libraries

### PHP SDK
```php
$api = new SchoolApi('your-api-token');

$students = $api->getStudents();
$api->createStudent($studentData);
```

### JavaScript SDK
```javascript
const api = new SchoolAPI('your-api-token');

api.getStudents().then(students => {
  console.log(students);
});
```

### Mobile SDKs
- **Android**: Available on Maven Central
- **iOS**: Available on CocoaPods

---

## Webhooks

Configure webhooks for real-time notifications:

### Available Events
- `student.created`
- `student.updated`
- `fee.payment_received`
- `exam.result_published`
- `event.created`

### Webhook Payload
```json
{
  "event": "student.created",
  "timestamp": "2024-01-15T10:30:00Z",
  "data": {
    "student_id": 1,
    "scholar_number": "2024001",
    "name": "John Doe"
  }
}
```

---

## Best Practices

### Authentication
- Store tokens securely
- Refresh tokens before expiration
- Use HTTPS for all requests

### Error Handling
- Implement proper error handling
- Check response status codes
- Handle rate limiting gracefully

### Performance
- Use appropriate filters to limit data
- Cache frequently accessed data
- Implement pagination for large datasets

### Security
- Validate all input data
- Use prepared statements
- Implement proper access controls

---

## Support

### Getting Help
- **API Status**: Check `/api/v1/status` for system health
- **Documentation**: This document is updated regularly
- **Support**: Contact development team for technical assistance

### Versioning
- Current API version: v1
- Breaking changes will be communicated in advance
- New features are backward compatible when possible

---

*API documentation last updated: January 2024*