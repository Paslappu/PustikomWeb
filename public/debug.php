<?php
// Debug Document Root Info
echo "<h2>Server Configuration</h2>";
echo "<pre>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . __FILE__ . "\n";
echo "Request URI: " . $_REQUEST['REQUEST_URI'] . "\n";
echo "</pre>";

// Check if CSS exists
$cssPath = $_SERVER['DOCUMENT_ROOT'] . '/css/login.css';
echo "<h2>CSS File Check</h2>";
echo "Looking for: " . $cssPath . "<br>";
if (file_exists($cssPath)) {
    echo "✅ CSS File FOUND<br>";
} else {
    echo "❌ CSS File NOT FOUND<br>";
}

// Check public folder
echo "<h2>Directory Listing</h2>";
echo "Contents of " . $_SERVER['DOCUMENT_ROOT'] . ":<br>";
$files = scandir($_SERVER['DOCUMENT_ROOT']);
echo "<pre>";
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo $file . "\n";
    }
}
echo "</pre>";
?>
