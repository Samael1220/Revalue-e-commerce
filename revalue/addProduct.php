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
            header("Location: admin.php");
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

<!-- Product Form -->
<div class="content-section" id="products">
  <h2>Add Product</h2>
  <form action="addProduct.php" method="POST" enctype="multipart/form-data">
    
    <!-- Product Name -->
    <label for="name">Product Name:</label>
    <input type="text" name="name" id="name" required><br><br>

    <!-- Image Upload -->
    <label for="image">Product Image:</label>
    <input type="file" name="image" id="image" accept="image/*" required><br><br>

    <!-- Size -->
    <label for="size">Size:</label>
    <select name="size" id="size" required>
      <option value="">-- Select Size --</option>
      <option value="XS">XS</option>
      <option value="S">S</option>
      <option value="M">M</option>
      <option value="L">L</option>
      <option value="XL">XL</option>
      <option value="XXL">XXL</option>
    </select><br><br>

    <!-- Category -->
    <label for="category">Category:</label>
    <select name="category" id="category" required>
      <option value="">-- Select Category --</option>
      <option value="vintage">Vintage</option>
      <option value="modern">Modern</option>
      <option value="jackets">Jackets</option>
      <option value="coats">Coats</option>
      <option value="pants">Pants</option>
    </select><br><br>

    <!-- Price -->
    <label for="price">Price (₱):</label>
    <input type="number" name="price" id="price" min="1" required><br><br>

    <!-- Submit -->
    <button type="submit" name="submit">Add Product</button>
  </form>

  <br>
  <a href="admin.php">⬅ Back to Products</a>
</div>
