<?php
class llms extends controller {

    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = true;
    
    public function index() {
        $module_model = $this->model('modules_model');
        $rolls_model = $this->model('rolls_model');

        $pages = $module_model->get_all(); // Fetches from 'modules' table [cite: 2026-01-22]
        $rolls = $rolls_model->get_all();  // Fetches from 'rolls' table [cite: 2026-01-22]

        $host = "https://" . $_SERVER['HTTP_HOST'];

        // Formatting for LLM Consumption [cite: 2026-02-13]
        $txt = "# Indicia Institute\n\n";
        $txt .= "> This file provides a high-level map of the Indicia Institute directory for LLM scrapers and AI assistants.\n\n";
        
        $txt .= "## Core Modules\n";
        foreach ($pages as $p) {
            $txt .= "- [" . $p['title'] . "]($host/" . $p['slug'] . "): " . substr(strip_tags($p['content']), 0, 100) . "...\n";
        }

        $txt .= "\n## Institutional Rolls\n";
        $txt .= "The following directory contains specific personnel and institutional roles.\n\n";
        foreach ($rolls as $r) {
            $txt .= "- [" . $r['title'] . "]($host/rolls/" . $r['slug'] . ")\n";
        }

        // Save to public root [cite: 2026-02-13]
        $file_path = getcwd() . '/llms.txt';
        file_put_contents($file_path, $txt);
        
        if (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($is_internal_call)) {
            return true;
        }
    }
}
