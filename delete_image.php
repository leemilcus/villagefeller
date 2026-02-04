<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;
    $imageUrl = $input['imageUrl'] ?? '';
    
    if (!$id) {
        throw new Exception('No project ID provided');
    }
    
    // Read existing gallery data
    $galleryFile = 'gallery.json';
    if (!file_exists($galleryFile)) {
        throw new Exception('Gallery data not found');
    }
    
    $galleryContent = file_get_contents($galleryFile);
    $galleryData = json_decode($galleryContent, true);
    
    if (!is_array($galleryData)) {
        $galleryData = [];
    }
    
    // Find and remove the project
    $found = false;
    foreach ($galleryData as $index => $project) {
        if ($project['id'] == $id || (isset($project['image']) && $project['image'] == $imageUrl)) {
            // Delete the image file if it exists
            if (isset($project['image']) && file_exists($project['image'])) {
                unlink($project['image']);
            }
            
            // Remove from array
            array_splice($galleryData, $index, 1);
            $found = true;
            break;
        }
    }
    
    if ($found) {
        // Save updated gallery data
        file_put_contents($galleryFile, json_encode($galleryData, JSON_PRETTY_PRINT));
        $response['success'] = true;
        $response['message'] = 'Project deleted successfully';
    } else {
        $response['message'] = 'Project not found';
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
