<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = ['success' => false, 'message' => ''];

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? '';
    $type = $input['type'] ?? ''; // 'manual' or 'user'
    
    if (!$id || !$type) {
        throw new Exception('Missing project ID or type');
    }
    
    if ($type === 'manual') {
        // For manual projects, we just remove from memory (they'll reload on next request)
        $response = [
            'success' => true,
            'message' => 'Manual project deleted successfully'
        ];
    } elseif ($type === 'user') {
        // For user projects, remove from gallery.json
        $galleryFile = 'gallery.json';
        $userProjects = [];
        
        if (file_exists($galleryFile)) {
            $content = file_get_contents($galleryFile);
            $userProjects = json_decode($content, true);
            if (!is_array($userProjects)) {
                $userProjects = [];
            }
            
            // Find and remove the project
            $found = false;
            foreach ($userProjects as $index => $project) {
                if ($project['id'] == $id) {
                    // Remove image file if it exists
                    if (isset($project['image']) && file_exists($project['image'])) {
                        unlink($project['image']);
                    }
                    
                    array_splice($userProjects, $index, 1);
                    $found = true;
                    break;
                }
            }
            
            if ($found) {
                // Save updated gallery data
                file_put_contents($galleryFile, json_encode($userProjects, JSON_PRETTY_PRINT));
                $response = [
                    'success' => true,
                    'message' => 'User project deleted successfully'
                ];
            } else {
                throw new Exception('Project not found');
            }
        } else {
            throw new Exception('Gallery file not found');
        }
    } else {
        throw new Exception('Invalid project type');
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>
