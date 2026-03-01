<?php
class llms extends controller {

    public static $is_core = true;
    
    public function index() {
        $module_model = $this->model('modules_model');
        $pages = $module_model->get_all(); 
        $host = "https://" . $_SERVER['HTTP_HOST'];

        $txt = "# Ars Rosaic\n\n";
        $txt .= "> This file provides a high-level map of the Ars Rosaic directory for LLM scrapers and AI assistants.\n\n";
        
        $txt .= "## Core Modules\n";
        
        // Manual skip list for internal system logic [cite: 2026-02-20]
        $restricted = ['sentinel', 'page', 'admin', 'auth', 'controller', 'llms', 'ror', 'sitemap', 'rolls', 'offices', 's', 'modules'];

        foreach ($pages as $p) {
            if (in_array($p['slug'], $restricted)) continue;

            // Canonical Root mapping [cite: 2026-02-13]
            $url_path = ($p['slug'] === 'home') ? "" : $p['slug'];
            
            // Validate content is not a Fatal Error [cite: 2026-01-22]
            if (!empty($p['content']) && strpos($p['content'], 'Fatal error') === false) {
                $txt .= "- [" . $p['title'] . "]($host/" . $url_path . "): " . substr(strip_tags($p['content']), 0, 100) . "...\n";
            }
        }

        $file_path = getcwd() . '/llms.txt';
        file_put_contents($file_path, $txt);
        
        if (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($is_internal_call)) {
            return true;
        }
    }
}
