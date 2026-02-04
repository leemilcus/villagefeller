<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = [];

try {
    // Manual projects (pre-loaded)
    $manualProjects = [
        [
            'id' => 'manual-1',
            'title' => 'Project 1',
            'description' => 'Tree removal project',
            'image' => './img/5.jpeg',
            'isManual' => true
        ],
        [
            'id' => 'manual-2',
            'title' => 'Project 2',
            'description' => 'Stump grinding work',
            'image' => './img/6.jpeg',
            'isManual' => true
        ],
        [
            'id' => 'manual-3',
            'title' => 'Project 3',
            'description' => 'Tree trimming service',
            'image' => './img/7.jpeg',
            'isManual' => true
        ],
        [
            'id' => 'manual-4',
            'title' => 'Project 4',
            'description' => 'Site clearing project',
            'image' => './img/8.jpeg',
            'isManual' => true
        ],
        [
            'id' => 'manual-5',
            'title' => 'Project 5',
            'description' => 'Garden cleanup work',
            'image' => './img/9.jpeg',
            'isManual' => true
        ],
        [
            'id' => 'manual-6',
            'title' => 'Project 6',
            'description' => 'Palm tree maintenance',
            'image' => './img/10.jpeg',
            'isManual' => true
        ]
    ];

    // User projects from gallery.json
    $userProjects = [];
    $galleryFile = 'gallery.json';
    
    if (file_exists($galleryFile)) {
        $content = file_get_contents($galleryFile);
        $userProjects = json_decode($content, true);
        if (!is_array($userProjects)) {
            $userProjects = [];
        }
    }

    $response = [
        'success' => true,
        'manualProjects' => $manualProjects,
        'userProjects' => $userProjects,
        'totalProjects' => count($manualProjects) + count($userProjects)
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'manualProjects' => [],
        'userProjects' => []
    ];
}

echo json_encode($response);
?>
