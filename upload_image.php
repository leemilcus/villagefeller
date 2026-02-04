<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No image uploaded or upload error');
    }

    $title = $_POST['title'] ?? 'Untitled Project';
    $description = $_POST['description'] ?? '';
    
    // Create uploads directory if it doesn't exist
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('project_') . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        throw new Exception('Failed to save uploaded file');
    }
    
    // Prepare project data
    $project = [
        'id' => uniqid(),
        'title' => $title,
        'description' => $description,
        'image' => $filePath,
        'fileName' => $fileName,
        'uploaded' => date('Y-m-d H:i:s'),
        'isManual' => false
    ];
    
    // Read existing gallery data
    $galleryFile = 'gallery.json';
    $galleryData = [];
    
    if (file_exists($galleryFile)) {
        $galleryContent = file_get_contents($galleryFile);
        $galleryData = json_decode($galleryContent, true);
        if (!is_array($galleryData)) {
            $galleryData = [];
        }
    }
    
    // Add new project
    $galleryData[] = $project;
    
    // Save updated gallery data
    file_put_contents($galleryFile, json_encode($galleryData, JSON_PRETTY_PRINT));
    
    $response = [
        'success' => true,
        'message' => 'Project uploaded successfully',
        'id' => $project['id'],
        'fileName' => $fileName,
        'imageUrl' => $filePath
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
