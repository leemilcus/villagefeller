<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Increase file upload limits
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_execution_time', 300);

// Configuration
$uploadDir = 'img/';
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Create upload directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$response = ['success' => false, 'message' => '', 'imageUrl' => ''];

try {
    // Check if file was uploaded
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error occurred.');
    }

    $file = $_FILES['image'];
    
    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
    }
    
    // Validate file size
    if ($file['size'] > $maxFileSize) {
        throw new Exception('File size exceeds 5MB limit.');
    }
    
    // Get title and description from POST data
    $title = isset($_POST['title']) ? trim($_POST['title']) : 'New Project';
    $description = isset($_POST['description']) ? trim($_POST['description']) : 'Project description';
    
    // Generate unique filename
    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', $originalName);
    $uniqueName = $safeName . '_' . time() . '_' . uniqid() . '.' . $extension;
    $uploadPath = $uploadDir . $uniqueName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Create project data
        $projectData = [
            'id' => 'project_' . time() . '_' . uniqid(),
            'title' => $title,
            'description' => $description,
            'image' => $uploadPath,
            'filename' => $uniqueName,
            'uploadDate' => date('Y-m-d H:i:s'),
            'isManual' => false
        ];
        
        // Update gallery.json
        $galleryFile = 'gallery.json';
        $gallery = [];
        
        if (file_exists($galleryFile)) {
            $gallery = json_decode(file_get_contents($galleryFile), true);
            if (!is_array($gallery)) {
                $gallery = [];
            }
        }
        
        // Add new project
        $gallery[] = $projectData;
        
        // Save gallery data
        if (file_put_contents($galleryFile, json_encode($gallery, JSON_PRETTY_PRINT))) {
            $response['success'] = true;
            $response['message'] = 'Image uploaded successfully!';
            $response['imageUrl'] = $uploadPath;
            $response['project'] = $projectData;
        } else {
            throw new Exception('Failed to save project data.');
        }
    } else {
        throw new Exception('Failed to move uploaded file.');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
?>
