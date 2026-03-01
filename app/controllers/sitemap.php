<?php

class sitemap extends controller
{
    public function index()
    {
        $model = $this->model('modules_model');
        $modules = $model->get_all(); 
        $base_url = URLROOT;

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        // Home - Manual Root Entry
        $xml .= "  <url>\n";
        $xml .= "    <loc>{$base_url}/</loc>\n";
        $xml .= "    <changefreq>weekly</changefreq>\n";
        $xml .= "    <priority>1.0</priority>\n";
        $xml .= "  </url>\n";

        // Expanded Restricted List
        $restricted = [
            'sentinel', 'page', 'admin', 'auth', 'controller', 'llms', 'ror', 'sitemap',
            'error_handler', 'health', 'rolls', 'offices', 's', 'modules', 'accounts', 'lessons'
        ];

        if (!empty($modules)) {
            foreach ($modules as $module) {
                // Skip restricted modules and 'home' to prevent duplication
                if (empty($module['slug']) || $module['slug'] === 'home' || in_array($module['slug'], $restricted)) continue;

                $loc = $base_url . '/' . $module['slug'];

                $xml .= "  <url>\n";
                $xml .= "    <loc>{$loc}</loc>\n";
                $xml .= "    <changefreq>monthly</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                $xml .= "  </url>\n";
            }
        }

        $xml .= "</urlset>";

        $path = PUBROOT . '/sitemap.xml';
        file_put_contents($path, $xml);

        if (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($is_internal_call)) {
           return true;
        }
    }
}
