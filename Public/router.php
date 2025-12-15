<?php


// Router script for PHP built-in server
// Allows serving static files from parent directory

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if requesting a static file
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i', $path)) {
    // Try to find the file in parent directory
    $file = __DIR__ . '/..' . $path;

    if (file_exists($file) && is_file($file)) {
        // MIME type mapping
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mime = $mime_types[$ext] ?? 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        // File not found
        http_response_code(404);
        echo "404 - File not found: $path";
        exit;
    }
}

// For all other requests, route to index.php
require __DIR__ . '/index.php';