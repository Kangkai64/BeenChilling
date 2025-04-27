<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'])) {
    // Directory where to save the image
    $uploadDir = '../images/webcam/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Get the base64 encoded image data
    $imageData = $_POST['image'];
    
    // Get filename
    $filename = isset($_POST['filename']) ? $_POST['filename'] : 'webcam_' . time() . '.png';
    
    // Full path
    $filePath = $uploadDir . $filename;
    
    // Decode and save the image
    $decodedImage = base64_decode($imageData);
    
    if (file_put_contents($filePath, $decodedImage)) {
        echo json_encode([
            'success' => true,
            'message' => 'Image saved successfully',
            'path' => $filePath
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save image'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>