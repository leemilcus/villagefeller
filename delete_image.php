<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$imageId = $input['id'] ?? '';
$imageUrl = $input['imageUrl'] ?? '';

if (empty($imageUrl)) {
    echo json_encode(['success' => false, 'message' => 'No image URL provided.']);
    exit;
}

$jsonFile = 'gallery_data.json';
$galleryData = [];

if (file_exists($jsonFile)) {
    $galleryData = json_decode(file_get_contents($jsonFile), true) ?? [];
    
    // Find and remove the image from JSON
    $found = false;
    foreach ($galleryData as $key => $project) {
        if ($project['id'] === $imageId || $project['image'] === $imageUrl) {
            // Try to delete the file
            $fileName = basename($imageUrl);
            if (file_exists('img/' . $fileName)) {
                unlink('img/' . $fileName);
            }
            unset($galleryData[$key]);
            $found = true;
            break;
        }
    }
    
    if ($found) {
        // Re-index array and save
        $galleryData = array_values($galleryData);
        file_put_contents($jsonFile, json_encode($galleryData, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true, 'message' => 'Image deleted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Image not found in gallery data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Gallery data file not found.']);
}
?>
