<?php
include("db.php");

// Handle form submission
if (isset($_POST['submit'])) {
    $name     = $_POST['name'];
    $size     = $_POST['size'];
    $category = $_POST['category'];
    $price    = $_POST['price'];

    // Upload directory
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // make sure uploads folder exists burat
    }

    // Create unique filename
    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        // Insert into inventory table
        $stmt = $conn->prepare("INSERT INTO inventory (name, image, category, size, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $targetFilePath, $category, $size, $price);

        if ($stmt->execute()) {
            // Redirect back to admin.php after success
             header("Location: admin.php?success=1");
            exit();
        } else {
            echo "<p style='color:red;'>❌ Database error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color:red;'>❌ Failed to upload image.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link rel="stylesheet" href="styleForAdmin.css">
</head>
<body>
    <div class="main-content2">
        <!-- Toast Container -->
        <div id="toast-container" class="toast-container"></div>
        
        <!-- Modern Product Form -->
        <div class="form-container">
            <div class="form-header">
                <h2>➕ Add New Product</h2>
                <p>Fill in the details below to add a new product to your inventory</p>
            </div>
            
            <form action="addProduct.php" method="POST" enctype="multipart/form-data" class="product-form">
                
                <!-- Product Name -->
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" name="name" id="name" placeholder="Enter product name" required>
                </div>

                <!-- Image Upload -->
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <div class="file-upload">
                        <input type="file" name="image" id="image" accept="image/*" required>
                        <label for="image" class="file-upload-label">
                            <span class="upload-icon">📷</span>
                            <span class="upload-text">Choose Image</span>
                        </label>
                    </div>
                    <div id="image-preview" class="image-preview" style="display: none;">
                        <img id="preview-img" src="" alt="Preview">
                        <span id="file-name" class="file-name"></span>
                    </div>
                </div>

                <!-- Size and Category Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="size">Size</label>
                        <select name="size" id="size" required>
                            <option value="">Select Size</option>
                            <option value="XS">XS</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select name="category" id="category" required>
                            <option value="">Select Category</option>
                            <option value="vintage">Vintage</option>
                            <option value="modern">Modern</option>
                            <option value="jackets">Jackets</option>
                            <option value="coats">Coats</option>
                            <option value="pants">Pants</option>
                        </select>
                    </div>
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label for="price">Price (₱)</label>
                    <input type="number" name="price" id="price" min="1" placeholder="Enter price" required>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" name="submit" class="btn-submit">Add Product</button>
                    <a href="admin.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toast notification system
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            
            const icon = type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️';
            toast.innerHTML = `
                <div class="toast-content">
                    <span class="toast-icon">${icon}</span>
                    <span class="toast-message">${message}</span>
                </div>
                <button class="toast-close" onclick="closeToast(this)"></button>
            `;
            
            toastContainer.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.classList.add('toast-hide');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toastContainer.removeChild(toast);
                        }
                    }, 300);
                }
            }, 5000);
        }
        
        function closeToast(button) {
            const toast = button.parentNode;
            toast.classList.add('toast-hide');
            setTimeout(() => {
                if (toast.parentNode) {
                    document.getElementById('toast-container').removeChild(toast);
                }
            }, 300);
        }

        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            const fileName = document.getElementById('file-name');
            
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    showToast('Please select a valid image file (JPEG, PNG, GIF, WebP)', 'error');
                    e.target.value = '';
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showToast('Image size must be less than 5MB', 'error');
                    e.target.value = '';
                    return;
                }
                
                // Show the preview container
                preview.style.display = 'block';
                
                // Display the filename
                fileName.textContent = file.name;
                
                // Create a preview of the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
                
                // Update the upload label to show file is selected
                const uploadText = document.querySelector('.upload-text');
                uploadText.textContent = 'Change Image';
                uploadText.style.color = '#8eb390';
                
                // Show success toast
                showToast(`Image "${file.name}" selected successfully`, 'success');
            } else {
                // Hide preview if no file selected
                preview.style.display = 'none';
                
                // Reset upload label
                const uploadText = document.querySelector('.upload-text');
                uploadText.textContent = 'Choose Image';
                uploadText.style.color = '#6e6e73';
            }
        });

        // Form validation and submission
        document.querySelector('.product-form').addEventListener('submit', function(e) {
            const formData = new FormData(this);
            const name = formData.get('name').trim();
            const price = formData.get('price');
            const image = formData.get('image');
            
            // Validate form fields
            if (!name) {
                e.preventDefault();
                showToast('Please enter a product name', 'error');
                return;
            }
            
            if (!price || price <= 0) {
                e.preventDefault();
                showToast('Please enter a valid price', 'error');
                return;
            }
            
            if (!image || image.size === 0) {
                e.preventDefault();
                showToast('Please select an image', 'error');
                return;
            }
            
            // Show loading toast
            showToast('Adding product...', 'info');
        }); 
    </script>
</body>
</html>
