<?php
/**
 * Public Controller
 * Handles public website pages
 */

class PublicController extends BaseController {
    private $eventModel;
    private $galleryModel;

    public function __construct() {
        parent::__construct();
        $this->eventModel = new Event();
        $this->galleryModel = new Gallery();
    }

    /**
     * Homepage
     */
    public function index() {
        // Get dynamic content from database
        $carousel_images = $this->getHomepageContent('carousel');
        $about_content = $this->getHomepageContent('about');
        $courses_content = $this->getHomepageContent('courses');
        $upcoming_events = $this->eventModel->getUpcomingEvents(3);
        $gallery_images = $this->galleryModel->getImagesByCategory(null, 6);
        $testimonials = $this->getHomepageContent('testimonials');

        $this->view('public/homepage', [
            'title' => 'A.s.higher secondary school - Excellence in Education',
            'carousel_images' => $carousel_images,
            'about_content' => $about_content,
            'courses_content' => $courses_content,
            'upcoming_events' => $upcoming_events,
            'gallery_images' => $gallery_images,
            'testimonials' => $testimonials,
            'show_header' => true,
            'show_footer' => true,
            'layout' => 'public'
        ]);
    }

    /**
     * About page
     */
    public function about() {
        $about_content = $this->getHomepageContent('about');

        $this->view('public/about', [
            'title' => 'About Us - A.s.higher secondary school',
            'about_content' => $about_content,
            'show_header' => true,
            'show_footer' => true,
            'layout' => 'public'
        ]);
    }

    /**
     * Courses page
     */
    public function courses() {
        $courses_content = $this->getHomepageContent('courses');

        $this->view('public/courses', [
            'title' => 'Courses - A.s.higher secondary school',
            'courses_content' => $courses_content,
            'show_header' => true,
            'show_footer' => true,
            'layout' => 'public'
        ]);
    }

    /**
     * Events page
     */
    public function events() {
        $upcoming_events = $this->eventModel->getUpcomingEvents(20);
        $past_events = $this->getPastEvents(10);

        $this->view('public/events', [
            'title' => 'Events - A.s.higher secondary school',
            'upcoming_events' => $upcoming_events,
            'past_events' => $past_events,
            'show_header' => true,
            'show_footer' => true,
            'layout' => 'public'
        ]);
    }

    /**
     * Gallery page
     */
    public function gallery() {
        $categories = $this->galleryModel->getCategories();
        $gallery_images = $this->galleryModel->allWithUploaders();

        $this->view('public/gallery', [
            'title' => 'Gallery - A.s.higher secondary school',
            'categories' => $categories,
            'gallery_images' => $gallery_images,
            'show_header' => true,
            'show_footer' => true,
            'layout' => 'public'
        ]);
    }

    /**
     * Contact page
     */
    public function contact() {
        $contact_content = $this->getHomepageContent('contact');

        $this->view('public/contact', [
            'title' => 'Contact Us - A.s.higher secondary school',
            'contact_content' => $contact_content,
            'show_header' => true,
            'show_footer' => true,
            'layout' => 'public'
        ]);
    }

    /**
     * Submit contact form
     */
    public function submitContact() {
        $this->validateCsrf();

        $data = $this->getPostData();

        // Validate input
        $this->validator->setData($data);
        $this->validator->setRules([
            'name' => 'required|max:100',
            'email' => 'required|email',
            'subject' => 'required|max:200',
            'message' => 'required|max:1000'
        ]);

        if (!$this->validator->validate()) {
            $this->session->setFlash('message', 'Please correct the errors below.');
            $this->session->setFlash('message_type', 'danger');
            $this->session->setFlash('errors', $this->validator->getErrors());
            $this->session->setFlash('old_input', $data);
            $this->redirect('/contact');
        }

        // In a real implementation, you would send an email or save to database
        // For now, just log it
        $this->logAction('contact_form_submitted', [
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject']
        ]);

        $this->session->setFlash('message', 'Thank you for your message. We will get back to you soon!');
        $this->session->setFlash('message_type', 'success');
        $this->redirect('/contact');
    }

    /**
     * Get homepage content by section
     */
    private function getHomepageContent($section) {
        $result = $this->db->query("
            SELECT * FROM homepage_content
            WHERE section = ? AND is_active = 1
            ORDER BY order_position ASC
        ")->bind(1, $section)->resultSet();

        return $result;
    }

    /**
     * Get past events
     */
    private function getPastEvents($limit = 10) {
        $today = date('Y-m-d');

        $results = $this->db->query("
            SELECT e.*, u.username as created_by_name
            FROM events e
            LEFT JOIN users u ON e.created_by = u.id
            WHERE e.event_date < ? AND e.is_active = 1
            ORDER BY e.event_date DESC
            LIMIT ?
        ")->bind(1, $today)->bind(2, $limit)->resultSet();

        return array_map([$this->eventModel, 'processResult'], $results);
    }
}
?>