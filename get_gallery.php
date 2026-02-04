<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$jsonFile = 'gallery_data.json';
$galleryData = [];

// Load existing gallery data
if (file_exists($jsonFile)) {
    $galleryData = json_decode(file_get_contents($jsonFile), true) ?? [];
} else {
    // If no JSON file exists, check img directory
    $imgDir = 'img/';
    if (file_exists($imgDir)) {
        $files = scandir($imgDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                $galleryData[] = [
                    'id' => uniqid('proj_', true),
                    'title' => 'Project ' . pathinfo($file, PATHINFO_FILENAME),
                    'description' => 'Uploaded project',
                    'image' => 'img/' . $file,
                    'isManual' => false,
                    'fileName' => $file,
                    'uploaded' => date('Y-m-d H:i:s', filemtime($imgDir . $file))
                ];
            }
        }
        
        // Save to JSON file
        file_put_contents($jsonFile, json_encode($galleryData, JSON_PRETTY_PRINT));
    }
}

echo json_encode($galleryData);
?>
