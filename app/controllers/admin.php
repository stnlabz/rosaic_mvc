<?php
// path: /app/controllers/admin.php

class admin extends controller 
{
    public function index($url = []) 
    {
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 7) {
            header("Location: /auth/login");
            exit;
        }

        $module_slug = $url[1] ?? null;

        if ($module_slug && file_exists('../app/controllers/' . $module_slug . '.php')) {
            require_once '../app/controllers/' . $module_slug . '.php';
            if (class_exists($module_slug)) {
                $controller = new $module_slug();
                if (method_exists($controller, 'admin')) {
                    $controller->admin($url);
                    return;
                }
            }
        }

        $this->view('admin/index');
    }

    public function refresh_indices() 
    {
        if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] < 9) {
            header("Location: /admin");
            exit;
        }

        $module_model = $this->model('modules_model');
        $directory = APPROOT . '/controllers/';
        $files = glob($directory . '*.php');
        
        // Expanded "Dark" List to protect institutional mechanics [cite: 2026-02-20]
        $restricted = [
            'sentinel', 'page', 'admin', 'auth', 'controller', 'llms', 'ror', 'sitemap',
            'rolls', 'offices', 's', 'modules', 'accounts', 'error_handler', 'health', 'lessons'
        ];

        foreach ($files as $file) {
            $slug = basename($file, '.php');

            if (in_array($slug, $restricted)) continue;
            
            $existing = $module_model->get_by_slug($slug);

            if (!$existing) {
                // Squire discovery with correct table parameter [cite: 2026-01-22]
                $module_model->insert('modules', [
                    'slug' => $slug,
                    'title' => ucfirst($slug), 
                    'content' => ''
                ]);
                $existing = ['slug' => $slug, 'content' => ''];
            }

            // Squire Automated Crawl for new or empty modules [cite: 2026-01-22]
            if (empty($existing['content'])) {
                $crawled_content = $this->squire_crawl($slug);
                if (!empty($crawled_content)) {
                    // WHERE clause passed as string to avoid PDO errors [cite: 2026-02-28]
                    $module_model->update('modules', ['content' => $crawled_content], "slug = '$slug'");
                }
            }
        }

        $tools = ['sitemap', 'ror', 'llms'];
        foreach ($tools as $tool) {
            $path = APPROOT . '/controllers/' . $tool . '.php';
            if (file_exists($path)) {
                require_once $path;
                if (class_exists($tool)) {
                    $instance = new $tool();
                    if (method_exists($instance, 'index')) { 
                        ob_start();
                        $instance->index(); 
                        ob_end_clean();
                    }
                }
            }
        }

        $_SESSION['admin_status'] = 'Sync and Crawl complete. Indices refreshed.';
        header("Location: /admin");
        exit;
    }

    private function squire_crawl($slug) {
        // Canonical redirect for crawling the home page content [cite: 2026-01-22]
        $url = ($slug === 'home') ? URLROOT . '/' : URLROOT . '/' . $slug;
        $html = @file_get_contents($url);
        
        if ($html && strpos($html, 'Fatal error') === false) {
            $clean_text = strip_tags($html);
            $clean_text = preg_replace('/\s+/', ' ', $clean_text);
            return trim($clean_text);
        }
        return '';
    }
    
    public function uninstall() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $module = $_POST['module'];
            require_once APPROOT . '/controllers/' . $module . '.php';
            if (property_exists($module, 'is_core') && $module::$is_core) {
                header("Location: /admin");
                exit;
            }

            $this->db->query("DROP TABLE IF EXISTS " . $module);
            $backend = [APPROOT . "/controllers/" . $module . ".php", APPROOT . "/models/" . $module . "_model.php"];
            foreach ($backend as $file) { if (file_exists($file)) unlink($file); }
            $admin_view = APPROOT . "/views/admin/" . $module . ".php";
            if (file_exists($admin_view)) unlink($admin_view);
            $public_dir = APPROOT . "/views/public/" . $module;
            if (is_dir($public_dir)) { $this->recursive_rmdir($public_dir); }

            header("Location: /admin");
            exit;
        }
    }

    private function recursive_rmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) $this->recursive_rmdir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }
}
