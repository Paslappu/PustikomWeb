<?php
// router.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$fullPath = __DIR__ . '/public' . $path;

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// Check if the requested file exists
if (file_exists($fullPath) && !is_dir($fullPath)) {
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    
    // ONLY manually handle 3D models if they are causing trouble
    if ($extension === 'glb') {
        header("Content-Type: model/gltf-binary");
        readfile($fullPath);
        return true;
    } elseif ($extension === 'gltf') {
        header("Content-Type: model/gltf+json");
        readfile($fullPath);
        return true;
    }

    // For CSS, JS, and Images, return false so PHP handles them normally
    return false;
}

// Let the request fall through to index.php if no file is found
return false;