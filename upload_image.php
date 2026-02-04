<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Create img directory if it doesn't exist
$imgDir = 'img/';
if (!file_exists($imgDir)) {
    mkdir($imgDir, 0777, true);
}

// Check if image was uploaded
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $title = $_POST['title'] ?? 'Untitled Project';
    $description = $_POST['description'] ?? '';
    
    // Generate unique filename
    $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = 'project_' . uniqid() . '_' . time() . '.' . $fileExtension;
    $filePath = $imgDir . $fileName;
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = $_FILES['image']['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.']);
        exit;
    }
    
    // Validate file size (5MB max)
    $maxSize = 5 * 1024 * 1024;
    if ($_FILES['image']['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'File size too large. Maximum size is 5MB.']);
        exit;
    }
    
    // Move uploaded file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        // Generate unique ID for the project
        $projectId = uniqid('proj_', true);
        
        // Prepare response
        echo json_encode([
            'success' => true,
            'message' => 'Image uploaded successfully!',
            'id' => $projectId,
            'fileName' => $fileName,
            'imageUrl' => 'img/' . $fileName,
            'title' => $title,
            'description' => $description
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save image.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No image uploaded or upload error.']);
}
?>
