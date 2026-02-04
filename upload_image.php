<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

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
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.');
    }
    
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
    $userProjects = [];
    
    if (file_exists($galleryFile)) {
        $content = file_get_contents($galleryFile);
        $userProjects = json_decode($content, true);
        if (!is_array($userProjects)) {
            $userProjects = [];
        }
    }
    
    // Add new project
    $userProjects[] = $project;
    
    // Save updated gallery data
    file_put_contents($galleryFile, json_encode($userProjects, JSON_PRETTY_PRINT));
    
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
