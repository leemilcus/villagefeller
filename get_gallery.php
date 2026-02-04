<?php
header('Content-Type: application/json');

// Read existing gallery data from gallery.json
$galleryFile = 'gallery.json';
$galleryData = [];

if (file_exists($galleryFile)) {
    $galleryContent = file_get_contents($galleryFile);
    $galleryData = json_decode($galleryContent, true);
    if (!is_array($galleryData)) {
        $galleryData = [];
    }
}

echo json_encode($galleryData);
?>
