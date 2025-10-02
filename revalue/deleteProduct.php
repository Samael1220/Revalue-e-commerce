<?php
include("db.php");

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = intval($_GET['id']);

// Delete product
$stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin.php#products");
    exit();
} else {
    echo "Error deleting product: " . $conn->error;
}
