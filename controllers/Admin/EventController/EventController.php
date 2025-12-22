<?php
/**
 * Event Controller
 *
 * Handles event management operations
 */

class EventController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    public function events() {
        $events = $this->db->select(
            "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
             FROM events e
             LEFT JOIN user_profiles u ON e.created_by = u.user_id
             ORDER BY e.event_date DESC"
        );

        $upcomingEvents = $this->db->select(
            "SELECT * FROM events
             WHERE event_date >= CURDATE()
             ORDER BY event_date ASC
             LIMIT 5"
        );

        $data = [
            'events' => $events,
            'upcoming_events' => $upcomingEvents,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/events', $data);
    }

    public function add() {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validation rules
            $validationRules = [
                'title' => 'required|min:3|max:200',
                'description' => 'required|min:10|max:1000',
                'event_date' => 'required|date',
                'event_time' => 'required',
                'venue' => 'required|min:3|max:200',
                'event_type' => 'required|in:academic,cultural,sports,other'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                // Handle file upload
                $imagePath = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/events/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                    $imagePath = $uploadDir . $fileName;
                    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
                }

                $eventData = [
                    'title' => $postData['title'],
                    'description' => $postData['description'],
                    'event_date' => $postData['event_date'],
                    'event_time' => $postData['event_time'],
                    'venue' => $postData['venue'],
                    'event_type' => $postData['event_type'],
                    'image_path' => $imagePath,
                    'created_by' => Session::get('user_id'),
                    'status' => 'active'
                ];

                $eventId = $this->db->insert('events', $eventData);

                if ($eventId) {
                    $this->setFlash('success', 'Event added successfully');
                    $this->logActivity('event_added', "Added event: {$postData['title']}");
                    $this->redirect('/admin/events');
                } else {
                    $errors['db'] = 'Failed to add event';
                }
            }
        }

        $data = [
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/events/add', $data);
    }

    public function edit($eventId) {
        $event = $this->db->selectOne("SELECT * FROM events WHERE id = ?", [$eventId]);

        if (!$event) {
            $this->setFlash('error', 'Event not found');
            $this->redirect('/admin/events');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validation rules
            $validationRules = [
                'title' => 'required|min:3|max:200',
                'description' => 'required|min:10|max:1000',
                'event_date' => 'required|date',
                'event_time' => 'required',
                'venue' => 'required|min:3|max:200',
                'event_type' => 'required|in:academic,cultural,sports,other',
                'status' => 'required|in:active,cancelled,completed'
            ];

            $errors = Validator::validateData($postData, $validationRules);

            if (empty($errors)) {
                // Handle file upload
                $imagePath = $event['image_path'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = 'uploads/events/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                    $imagePath = $uploadDir . $fileName;
                    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);

                    // Delete old image if exists
                    if ($event['image_path'] && file_exists($event['image_path'])) {
                        unlink($event['image_path']);
                    }
                }

                $eventData = [
                    'title' => $postData['title'],
                    'description' => $postData['description'],
                    'event_date' => $postData['event_date'],
                    'event_time' => $postData['event_time'],
                    'venue' => $postData['venue'],
                    'event_type' => $postData['event_type'],
                    'image_path' => $imagePath,
                    'status' => $postData['status']
                ];

                if ($this->db->update('events', $eventData, 'id = ?', [$eventId])) {
                    $this->setFlash('success', 'Event updated successfully');
                    $this->logActivity('event_updated', "Updated event: {$postData['title']}");
                    $this->redirect('/admin/events');
                } else {
                    $errors['db'] = 'Failed to update event';
                }
            }
        }

        $data = [
            'event' => $event,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/events/edit', $data);
    }

    public function delete($eventId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Invalid request method'], 400);
        }

        $event = $this->db->selectOne("SELECT * FROM events WHERE id = ?", [$eventId]);

        if (!$event) {
            $this->json(['error' => 'Event not found'], 404);
        }

        if ($this->db->delete('events', 'id = ?', [$eventId])) {
            // Delete image file if exists
            if ($event['image_path'] && file_exists($event['image_path'])) {
                unlink($event['image_path']);
            }

            $this->logActivity('event_deleted', "Deleted event: {$event['title']}");
            $this->json(['success' => true, 'message' => 'Event deleted successfully']);
        } else {
            $this->json(['error' => 'Failed to delete event'], 500);
        }
    }
}
?>