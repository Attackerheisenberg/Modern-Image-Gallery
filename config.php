<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'image_gallery');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create images table if not exists
$sql = "CREATE TABLE IF NOT EXISTS images (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    description TEXT,
    category VARCHAR(100),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    views INT DEFAULT 0
)";

if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

// Create thumbnails directory if not exists
if (!file_exists('thumbs')) {
    mkdir('thumbs', 0777, true);
}

// Create images directory if not exists
if (!file_exists('images')) {
    mkdir('images', 0777, true);
}
?>