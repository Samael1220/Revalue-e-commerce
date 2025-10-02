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
<html>
<head>
    <title>Edit Product</title>
</head>
<body>
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>

        <label>Image:</label><br>
        <img src="<?= $product['image'] ?>" width="100"><br>
        <input type="file" name="image"><br><br>

        <label>Size:</label>
        <select name="size" required>
            <?php
            $sizes = ["XS","S","M","L","XL","XXL"];
            foreach ($sizes as $s) {
                echo "<option value='$s' " . ($product['size'] == $s ? "selected" : "") . ">$s</option>";
            }
            ?>
        </select><br><br>

        <label>Category:</label>
        <select name="category" required>
            <?php
            $categories = ["vintage","modern","jackets","coats","pants"];
            foreach ($categories as $c) {
                echo "<option value='$c' " . ($product['category'] == $c ? "selected" : "") . ">$c</option>";
            }
            ?>
        </select><br><br>

        <label>Price (₱):</label>
        <input type="number" name="price" value="<?= $product['price'] ?>" required><br><br>

        <button type="submit">Update Product</button>
    </form>
</body>
</html>
