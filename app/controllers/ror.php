<?php

class ror extends controller
{
    public static $is_core = true;
    
    public function index()
    {
        $baseUrl = 'https://www.arsrosaic.org';
        $modules = $this->model('modules_model')->get_all();

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<rss version="2.0">' . PHP_EOL;
        $xml .= '  <channel>' . PHP_EOL;
        $xml .= '    <title>Ars Rosaic</title>' . PHP_EOL;
        $xml .= '    <link>' . $baseUrl . '</link>' . PHP_EOL;
        $xml .= '    <description>Organization Resource Feed</description>' . PHP_EOL;

        // Expanded Restricted List
        $restricted = [
            'sentinel', 'page', 'admin', 'auth', 'controller', 'llms', 'ror', 'sitemap',
            'error_handler', 'health', 'rolls', 'offices', 's', 'modules', 'accounts', 'lessons'
        ];

        foreach ($modules as $module) {
            // Filter restricted system modules
            if (in_array($module['slug'], $restricted)) continue;

            // Canonical mapping: home becomes root
            $url_path = ($module['slug'] === 'home') ? "" : htmlspecialchars($module['slug']);

            $xml .= '    <item>' . PHP_EOL;
            $xml .= '      <title>' . htmlspecialchars($module['title']) . '</title>' . PHP_EOL;
            $xml .= '      <link>' . $baseUrl . '/' . $url_path . '</link>' . PHP_EOL;
            $xml .= '    </item>' . PHP_EOL;
        }

        $xml .= '  </channel>' . PHP_EOL;
        $xml .= '</rss>';

        $file_path = getcwd() . '/ror.xml';
        file_put_contents($file_path, $xml);
        
        if (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($is_internal_call)) {
           return true;
        }
    }
}
