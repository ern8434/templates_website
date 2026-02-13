<?php

require_once 'App/Config.php';
require_once 'App/EnvatoClient.php';
require_once 'App/Controller.php';

use App\Controller;

// Simple Autoloader (if needed, but manual requires are fine for this size)
// spl_autoload_register(function ($class) {
//     $file = str_replace('\\', '/', $class) . '.php';
//     if (file_exists($file)) {
//         require $file;
//     }
// });

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$controller = new Controller();

// Basic Routing
// Automatically detect the base path relative to index.php
$basePath = dirname($_SERVER['SCRIPT_NAME']);
// Ensure no backslashes (Windows safety) and consistent /
$basePath = str_replace('\\', '/', $basePath);
// If root, basepath might be empty or /
if ($basePath === '/') echo ""; // Just to execute logic? No.
// Actually dirname('/') is '/' usually.

// Proper extraction:
if (strpos($uri, $basePath) === 0 && $basePath !== '/') {
    $uri = substr($uri, strlen($basePath));
}

// Remove trailing slash
$uri = rtrim($uri, '/');
if ($uri === '' || $uri === '/index.php') {
    $uri = '/home';
}
if (empty($uri)) {
    $uri = '/home';
}

switch ($uri) {
    case '/home':
        $controller->index();
        break;
    case '/search':
        $controller->search();
        break;
    case '/item':
        // Get ID from query string
        $controller->item();
        break;
    default:
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        break;
}
