<?php
/**
 * Public Controller
 *
 * Handles public-facing pages
 */

class PublicController extends BaseController {

    public function index() {
        $this->homepage();
    }

    public function homepage() {
        // Load homepage content from database
        $homepageModel = $this->loadModel('Admin/Homepage/Homepage');

        $heroContent = $homepageModel->getContentBySection('hero');
        $aboutContent = $homepageModel->getContentBySection('about');
        $coursesContent = $homepageModel->getContentBySection('courses');
        $eventsContent = $homepageModel->getContentBySection('events');
        $galleryContent = $homepageModel->getContentBySection('gallery');
        $testimonialsContent = $homepageModel->getContentBySection('testimonials');

        $data = [
            'hero_content' => $heroContent,
            'about_content' => $aboutContent,
            'courses_content' => $coursesContent,
            'events_content' => $eventsContent,
            'gallery_content' => $galleryContent,
            'testimonials_content' => $testimonialsContent
        ];

        $this->view('public/homepage', $data);
    }

    public function about() {
        $this->view('public/about');
    }

    public function courses() {
        $this->view('public/courses');
    }

    public function events() {
        $this->view('public/events');
    }

    public function gallery() {
        $this->view('public/gallery');
    }

    public function contact() {
        $this->view('public/contact');
    }

    public function admission() {
        $this->view('public/admission');
    }
}
?>