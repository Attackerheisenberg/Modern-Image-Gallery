<?php
session_start();
require_once 'config.php';

// Handle search and filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'all';

// Build query
$query = "SELECT * FROM images WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR description LIKE ? OR category LIKE ?)";
    $search_term = "%$search%";
    $params = array_merge($params, [$search_term, $search_term, $search_term]);
    $types .= "sss";
}

if ($category !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$query .= " ORDER BY uploaded_at DESC";

// Prepare and execute
$stmt = $conn->prepare($query);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get all categories for filter
$categories_result = $conn->query("SELECT DISTINCT category FROM images WHERE category IS NOT NULL AND category != ''");
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Get total images count
$total_result = $conn->query("SELECT COUNT(*) as total FROM images");
$total_images = $total_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Image Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1><i class="fas fa-images"></i> Modern Image Gallery</h1>
            <p class="subtitle">Upload, organize and share your images</p>
        </header>

        <!-- Upload Section -->
        <div class="upload-section">
            <h2><i class="fas fa-cloud-upload-alt"></i> Upload New Image</h2>
            <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="form-group">
                    <div class="file-upload">
                        <input type="file" name="image" id="image" accept="image/*" required>
                        <label for="image" class="file-label">
                            <i class="fas fa-file-image"></i>
                            <span>Choose an image</span>
                        </label>
                        <div class="file-info" id="fileInfo">No file selected</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <input type="text" name="title" placeholder="Image Title (optional)" class="form-input">
                </div>
                
                <div class="form-group">
                    <textarea name="description" placeholder="Image Description (optional)" class="form-textarea"></textarea>
                </div>
                
                <div class="form-group">
                    <input type="text" name="category" placeholder="Category (e.g., Nature, Travel, Art)" class="form-input">
                </div>
                
                <button type="submit" class="upload-btn">
                    <i class="fas fa-upload"></i> Upload Image
                </button>
                
                <div class="upload-progress" id="uploadProgress">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
            </form>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="stat-card">
                <i class="fas fa-images"></i>
                <h3><?php echo $total_images; ?></h3>
                <p>Total Images</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-folder"></i>
                <h3><?php echo count($categories); ?></h3>
                <p>Categories</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-eye"></i>
                <h3><?php 
                    $views_result = $conn->query("SELECT SUM(views) as total_views FROM images");
                    echo $views_result->fetch_assoc()['total_views'] ?? 0;
                ?></h3>
                <p>Total Views</p>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="filter-section">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search images..." value="<?php echo htmlspecialchars($search); ?>">
                <button id="searchBtn" class="search-btn">Search</button>
            </div>
            
            <div class="category-filter">
                <select id="categoryFilter" class="category-select">
                    <option value="all" <?php echo $category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button id="resetFilter" class="reset-btn">
                <i class="fas fa-redo"></i> Reset Filters
            </button>
        </div>

        <!-- Gallery -->
        <div class="gallery-section">
            <h2><i class="fas fa-th"></i> Image Gallery</h2>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="gallery-grid" id="galleryGrid">
                    <?php while($row = $result->fetch_assoc()): 
                        $image_path = 'images/' . $row['filename'];
                        $thumb_path = 'thumbs/' . $row['filename'];
                        
                        // Create thumbnail if it doesn't exist
                        if (!file_exists($thumb_path) && file_exists($image_path)) {
                            createThumbnail($image_path, $thumb_path, 300, 200);
                        }
                    ?>
                        <div class="gallery-item" data-category="<?php echo htmlspecialchars($row['category'] ?? 'Uncategorized'); ?>">
                            <div class="image-container">
                                <a href="<?php echo $image_path; ?>" data-lightbox="gallery" data-title="<?php echo htmlspecialchars($row['title'] ?? 'Untitled'); ?>">
                                    <img src="<?php echo file_exists($thumb_path) ? $thumb_path : $image_path; ?>" 
                                         alt="<?php echo htmlspecialchars($row['title'] ?? 'Image'); ?>"
                                         loading="lazy">
                                    <div class="image-overlay">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </a>
                                <div class="image-actions">
                                    <button class="view-btn" onclick="incrementViews(<?php echo $row['id']; ?>)">
                                        <i class="fas fa-eye"></i> <?php echo $row['views']; ?>
                                    </button>
                                    <button class="delete-btn" onclick="deleteImage(<?php echo $row['id']; ?>, '<?php echo $row['filename']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="image-info">
                                <h3 class="image-title"><?php echo htmlspecialchars($row['title'] ?? 'Untitled'); ?></h3>
                                <?php if (!empty($row['description'])): ?>
                                    <p class="image-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                                <?php endif; ?>
                                <div class="image-meta">
                                    <span class="image-category"><?php echo htmlspecialchars($row['category'] ?? 'Uncategorized'); ?></span>
                                    <span class="image-date"><?php echo date('M d, Y', strtotime($row['uploaded_at'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-image"></i>
                    <h3>No images found</h3>
                    <p>Upload your first image to get started!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>Modern Image Gallery &copy; <?php echo date('Y'); ?> | Built with PHP & MySQL</p>
            <p class="footer-links">
                <a href="#"><i class="fab fa-github"></i> GitHub</a>
                <a href="#"><i class="fas fa-question-circle"></i> Help</a>
                <a href="#"><i class="fas fa-envelope"></i> Contact</a>
            </p>
        </footer>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this image? This action cannot be undone.</p>
            <div class="modal-actions">
                <button id="confirmDelete" class="btn-danger">Delete</button>
                <button id="cancelDelete" class="btn-secondary">Cancel</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    <script src="script.js"></script>
</body>
</html>

<?php
// Thumbnail creation function
function createThumbnail($source, $destination, $width, $height) {
    $info = getimagesize($source);
    $mime = $info['mime'];
    
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            return false;
    }
    
    $src_w = imagesx($image);
    $src_h = imagesy($image);
    
    // Calculate aspect ratio
    $src_ratio = $src_w / $src_h;
    $dst_ratio = $width / $height;
    
    if ($dst_ratio > $src_ratio) {
        $dst_h = $height;
        $dst_w = $height * $src_ratio;
    } else {
        $dst_w = $width;
        $dst_h = $width / $src_ratio;
    }
    
    $dst_x = ($width - $dst_w) / 2;
    $dst_y = ($height - $dst_h) / 2;
    
    $thumbnail = imagecreatetruecolor($width, $height);
    
    // Add white background for transparent images
    $white = imagecolorallocate($thumbnail, 255, 255, 255);
    imagefill($thumbnail, 0, 0, $white);
    
    imagecopyresampled($thumbnail, $image, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
    
    // Save thumbnail
    switch ($mime) {
        case 'image/jpeg':
            imagejpeg($thumbnail, $destination, 85);
            break;
        case 'image/png':
            imagepng($thumbnail, $destination, 8);
            break;
        case 'image/gif':
            imagegif($thumbnail, $destination);
            break;
    }
    
    imagedestroy($image);
    imagedestroy($thumbnail);
    
    return true;
}
?>