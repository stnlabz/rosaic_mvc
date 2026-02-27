<?php

class ror extends controller
{
    // Designation as a Core Module prevents deletion from the site/DB
    public static $is_core = true;
    
    public function index()
    {
        $baseUrl = 'https://www.indiciainstitute.org';

        $modules = $this->model('modules_model')->get_all();
        $rolls   = $this->model('rolls_model')->get_all();

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<rss version="2.0">' . PHP_EOL;
        $xml .= '  <channel>' . PHP_EOL;
        $xml .= '    <title>Indicia Institute</title>' . PHP_EOL;
        $xml .= '    <link>' . $baseUrl . '</link>' . PHP_EOL;
        $xml .= '    <description>Institutional Resource Feed</description>' . PHP_EOL;

        foreach ($modules as $module) {
            $xml .= '    <item>' . PHP_EOL;
            $xml .= '      <title>' . htmlspecialchars($module['title']) . '</title>' . PHP_EOL;
            $xml .= '      <link>' . $baseUrl . '/' . htmlspecialchars($module['slug']) . '</link>' . PHP_EOL;
            $xml .= '    </item>' . PHP_EOL;
        }

        foreach ($rolls as $roll) {
            $xml .= '    <item>' . PHP_EOL;
            $xml .= '      <title>' . htmlspecialchars($roll['title']) . '</title>' . PHP_EOL;
            $xml .= '      <link>' . $baseUrl . '/rolls/' . htmlspecialchars($roll['slug']) . '</link>' . PHP_EOL;
            $xml .= '    </item>' . PHP_EOL;
        }

        $xml .= '  </channel>' . PHP_EOL;
        $xml .= '</rss>';

        // Use getcwd() to target the exact directory where index.php is running [cite: 2026-02-13]
        $file_path = getcwd() . '/ror.xml';
        file_put_contents($file_path, $xml);
        
        if (basename($_SERVER['PHP_SELF']) == 'index.php' && !isset($is_internal_call)) {
           return true;
        }

    }
}

