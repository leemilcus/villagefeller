<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

$response = ['success' => false, 'message' => ''];

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id']) || empty($input['id'])) {
        throw new Exception('Project ID is required.');
    }
    
    $projectId = $input['id'];
    $galleryFile = 'gallery.json';
    
    if (!file_exists($galleryFile)) {
        throw new Exception('Gallery file not found.');
    }
    
    $gallery = json_decode(file_get_contents($galleryFile), true);
    
    if (!is_array($gallery)) {
        $gallery = [];
    }
    
    $found = false;
    $newGallery = [];
    
    foreach ($gallery as $project) {
        if (isset($project['id']) && $project['id'] === $projectId) {
            $found = true;
            // Delete the image file
            if (isset($project['image']) && file_exists($project['image'])) {
                unlink($project['image']);
            }
            continue; // Skip this project (delete it)
        }
        $newGallery[] = $project;
    }
    
    if (!$found) {
        throw new Exception('Project not found.');
    }
    
    // Save updated gallery
    if (file_put_contents($galleryFile, json_encode($newGallery, JSON_PRETTY_PRINT))) {
        $response['success'] = true;
        $response['message'] = 'Project deleted successfully!';
    } else {
        throw new Exception('Failed to update gallery.');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>
