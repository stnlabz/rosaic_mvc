<?php

class sitemap extends controller
{
    public function index()
    {
        $model = $this->model('modules_model');

        $modules = $model->get_all(); // assumes active/public filtering inside model

        $base_url = URLROOT;

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        // Home
        $xml .= "  <url>\n";
        $xml .= "    <loc>{$base_url}/</loc>\n";
        $xml .= "    <changefreq>weekly</changefreq>\n";
        $xml .= "    <priority>1.0</priority>\n";
        $xml .= "  </url>\n";

        // Modules / Pages
        if (!empty($modules)) {
            foreach ($modules as $module) {

                if (!empty($module['slug'])) {

                    $loc = $base_url . '/' . $module['slug'];

                    $xml .= "  <url>\n";
                    $xml .= "    <loc>{$loc}</loc>\n";
                    $xml .= "    <changefreq>monthly</changefreq>\n";
                    $xml .= "    <priority>0.8</priority>\n";
                    $xml .= "  </url>\n";
                }
            }
        }

        $xml .= "</urlset>";

        // Write to public directory
        $path = PUBROOT . '/sitemap.xml';
        file_put_contents($path, $xml);

        // If called from admin refresh, return status
        if (isset($_GET['refresh'])) {
            return true;
        }

        // Otherwise output directly
       if (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($is_internal_call)) {
           return true;
        }
    }
}

