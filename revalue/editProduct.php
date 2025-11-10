<?php
include("db.php");

// Check if ID is given
if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = intval($_GET['id']);

// Get product from DB
$stmt = $conn->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $size = $_POST['size'];
    $category = $_POST['category'];
    $price = $_POST['price'];

    // Handle image upload (optional)
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);

        $stmt = $conn->prepare("UPDATE inventory SET name=?, size=?, category=?, price=?, image=? WHERE id=?");
        $stmt->bind_param("sssisi", $name, $size, $category, $price, $targetFile, $id);
    } else {
        $stmt = $conn->prepare("UPDATE inventory SET name=?, size=?, category=?, price=? WHERE id=?");
        $stmt->bind_param("sssii", $name, $size, $category, $price, $id);
    }

    if ($stmt->execute()) {
        header("Location: admin.php#products");
        exit();
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
    <link rel="stylesheet" href="styleForAdmin.css">
</head>
<body>
    <div class="main-content2">
        <!-- Modern Edit Form -->
        <div class="form-container">
            <div class="form-header">
                <h2>✏️ Edit Product</h2>
                <p>Update the product details below</p>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="product-form">
                
                <!-- Product Name -->
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" placeholder="Enter product name" required>
                </div>

                <!-- Current Image Display -->
                <div class="form-group">
                <label>Current Image</label>
                <div class="current-image">
                    <img id="preview-image" src="<?= $product['image'] ?>" alt="Current product image" style="max-width:200px; border-radius:8px;">
                    <span class="image-label">Current Image</span>
                </div>
                </div>

                <!-- New Image Upload -->
                <div class="form-group">
                <label for="image">Update New Image</label>
                <div class="file-upload">
                    <input type="file" name="image" id="image" accept="image/*">
                    <label for="image" class="file-upload-label">
                    <span class="upload-icon">📷</span>
                    <span class="upload-text">Choose New Image</span>
                    </label>
                </div>
                </div>

                <!-- Preview Script -->
                <script>
                document.getElementById('image').addEventListener('change', function (event) {
                    const file = event.target.files[0];
                    const preview = document.getElementById('preview-image');

                    if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result; // Update image preview
                    };
                    reader.readAsDataURL(file);
                    }
                });
                </script>

                <!-- Size and Category Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="size">Size</label>
                        <select name="size" id="size" required>
                            <?php
                            $sizes = ["XS","S","M","L","XL","XXL"];
                            foreach ($sizes as $s) {
                                $selected = ($product['size'] == $s) ? "selected" : "";
                                echo "<option value='$s' $selected>$s</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select name="category" id="category" required>
                            <?php
                            $categories = ["vintage","modern","jackets","coats","pants"];
                            foreach ($categories as $c) {
                                $selected = ($product['category'] == $c) ? "selected" : "";
                                echo "<option value='$c' $selected>" . ucfirst($c) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Price -->
                <div class="form-group">
                    <label for="price">Price (₱)</label>
                    <input type="number" name="price" id="price" value="<?= $product['price'] ?>" min="1" placeholder="Enter price" required>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Update Product</button>
                    <a href="admin.php#products" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
