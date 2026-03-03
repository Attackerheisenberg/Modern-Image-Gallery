<?php
require_once 'config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['filename'])) {
    $id = intval($_POST['id']);
    $filename = $_POST['filename'];
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM images WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Delete image file
        $image_path = 'images/' . $filename;
        $thumb_path = 'thumbs/' . $filename;
        
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }
        
        $response['success'] = true;
        $response['message'] = 'Image deleted successfully!';
    } else {
        $response['message'] = 'Database error: ' . $stmt->error;
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>