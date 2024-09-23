<?php
    // Set the correct path to the artisan file
    define('LARAVEL_START', microtime(true));
    
    // Include Composer's autoload file
    require __DIR__.'/vendor/autoload.php';
    
    // Bootstrap the Laravel application
    $app = require_once __DIR__.'/bootstrap/app.php';
    
    if (isset($_GET['command'])) {
        $command = escapeshellcmd($_GET['command']);
        $output = shell_exec("php artisan $command");
        echo "<pre>$output</pre>";
    } else {
        echo "No command provided.";
    }
?>
