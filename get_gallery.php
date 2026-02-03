<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$galleryFile = 'gallery.json';
$response = ['success' => false, 'projects' => []];

try {
    if (file_exists($galleryFile)) {
        $content = file_get_contents($galleryFile);
        $gallery = json_decode($content, true);
        
        if (is_array($gallery)) {
            $response['success'] = true;
            $response['projects'] = $gallery;
        } else {
            $response['projects'] = [];
        }
    } else {
        // Create empty gallery file if it doesn't exist
        file_put_contents($galleryFile, '[]');
        $response['success'] = true;
        $response['projects'] = [];
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(500);
}

echo json_encode($response);
?>
