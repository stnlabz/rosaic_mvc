<?php
// path: /app/core/controller.php
class controller {
    /**
     * Renders a view file and injects data
     */
    public function view($view, $data = []) {
        $file = APPROOT . '/views/' . $view . '.php';

        if (file_exists($file)) {
            // Force browser to render HTML, not plain text 
            header('Content-Type: text/html; charset=utf-8');
            
            // Makes $data available in the view 
            extract($data);
            
            // Execute the PHP within the file 
            require $file; 
        } else {
            die("View Error: $view not found at $file");
        }
    }

    /**
     * Loads a model
     */
    public function model($model) {
        if (file_exists(APPROOT . '/models/' . $model . '.php')) {
            require_once APPROOT . '/models/' . $model . '.php';
            return new $model();
        }
        die("Model $model not found.");
    }
}
