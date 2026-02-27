<?php
// path: /app/controllers/lessons.php

class lessons extends controller
{

    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = true;
    
    public function admin()
    {
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 1) {
            header("Location: /auth/login");
            exit;
        }

        $model = $this->model('lessons_model');
        $offices_model = $this->model('offices_model');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                $model->create([
                    'title' => trim($_POST['title']),
                    'slug' => trim($_POST['slug']),
                    'content' => trim($_POST['content']),
                    'office_id' => (int)$_POST['office_id']
                ]);
            }

            if ($action === 'update') {
                $model->update_lesson([
                    'id' => (int)$_POST['id'],
                    'title' => trim($_POST['title']),
                    'slug' => trim($_POST['slug']),
                    'content' => trim($_POST['content']),
                    'office_id' => (int)$_POST['office_id']
                ]);
            }

            if ($action === 'toggle_archive') {
                $model->toggle_archive((int)$_POST['id']);
            }

            if ($action === 'delete') {
                $model->delete((int)$_POST['id']);
            }

            header("Location: /admin/lessons");
            exit;
        }

        $data['lessons'] = $model->get_all_admin();
        $data['offices'] = $offices_model->get_all();
        $this->view('admin/lessons', $data);
    }
}
