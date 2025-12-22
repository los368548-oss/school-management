<?php
/**
 * Homepage Controller
 *
 * Handles admin homepage content management
 */

class HomepageController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireAuth();
        $this->requireRole('admin');
    }

    public function index() {
        $this->homepage();
    }

    public function homepage() {
        $homepageModel = $this->loadModel('Admin/Homepage/Homepage');
        $content = $homepageModel->getAllContent();
        $sections = $homepageModel->getSections();

        $data = [
            'content' => $content,
            'sections' => $sections,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/homepage/homepage', $data);
    }

    public function add() {
        $errors = [];
        $homepageModel = $this->loadModel('Admin/Homepage/Homepage');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validate CSRF
            if (!Security::validateCSRFToken($postData['csrf_token'] ?? '')) {
                $errors['csrf'] = 'Invalid security token';
            }

            // Validate required fields
            if (empty($postData['section'])) {
                $errors['section'] = 'Section is required';
            }
            if (empty($postData['title'])) {
                $errors['title'] = 'Title is required';
            }

            if (empty($errors)) {
                $data = [
                    'section' => $postData['section'],
                    'title' => $postData['title'],
                    'content' => $postData['content'] ?? '',
                    'image_path' => $postData['image_path'] ?? '',
                    'link_url' => $postData['link_url'] ?? '',
                    'link_text' => $postData['link_text'] ?? '',
                    'display_order' => (int)($postData['display_order'] ?? 0),
                    'is_active' => isset($postData['is_active']) ? 1 : 0,
                    'updated_by' => Session::get('user_id')
                ];

                if ($homepageModel->createContent($data)) {
                    $this->logActivity('homepage_content_added', "Added homepage content: {$data['title']}");
                    $this->redirect('/admin/homepage');
                } else {
                    $errors['general'] = 'Failed to add homepage content';
                }
            }
        }

        $sections = $homepageModel->getSections();

        $data = [
            'sections' => $sections,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/homepage/add', $data);
    }

    public function edit($id) {
        $errors = [];
        $homepageModel = $this->loadModel('Admin/Homepage/Homepage');

        $content = $homepageModel->find($id);
        if (!$content) {
            $this->redirect('/admin/homepage');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            // Validate CSRF
            if (!Security::validateCSRFToken($postData['csrf_token'] ?? '')) {
                $errors['csrf'] = 'Invalid security token';
            }

            // Validate required fields
            if (empty($postData['section'])) {
                $errors['section'] = 'Section is required';
            }
            if (empty($postData['title'])) {
                $errors['title'] = 'Title is required';
            }

            if (empty($errors)) {
                $data = [
                    'section' => $postData['section'],
                    'title' => $postData['title'],
                    'content' => $postData['content'] ?? '',
                    'image_path' => $postData['image_path'] ?? '',
                    'link_url' => $postData['link_url'] ?? '',
                    'link_text' => $postData['link_text'] ?? '',
                    'display_order' => (int)($postData['display_order'] ?? 0),
                    'is_active' => isset($postData['is_active']) ? 1 : 0,
                    'updated_by' => Session::get('user_id')
                ];

                if ($homepageModel->updateContent($id, $data)) {
                    $this->logActivity('homepage_content_updated', "Updated homepage content: {$data['title']}");
                    $this->redirect('/admin/homepage');
                } else {
                    $errors['general'] = 'Failed to update homepage content';
                }
            }
        }

        $sections = $homepageModel->getSections();

        $data = [
            'content' => $content,
            'sections' => $sections,
            'errors' => $errors,
            'csrf_token' => Security::generateCSRFToken()
        ];

        $this->view('admin/homepage/edit', $data);
    }

    public function delete($id) {
        $homepageModel = $this->loadModel('Admin/Homepage/Homepage');

        $content = $homepageModel->find($id);
        if ($content) {
            if ($homepageModel->deleteContent($id)) {
                $this->logActivity('homepage_content_deleted', "Deleted homepage content: {$content['title']}");
            }
        }

        $this->redirect('/admin/homepage');
    }

    public function reorder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $postData = $this->getPostData();

            if (!Security::validateCSRFToken($postData['csrf_token'] ?? '')) {
                echo json_encode(['success' => false, 'message' => 'Invalid security token']);
                return;
            }

            $homepageModel = $this->loadModel('Admin/Homepage/Homepage');
            $section = $postData['section'] ?? '';
            $orderData = $postData['order'] ?? [];

            if ($homepageModel->reorderContent($section, $orderData)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reorder content']);
            }
        }
    }
}
?>