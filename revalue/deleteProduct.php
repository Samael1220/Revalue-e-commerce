<?php
include("db.php");

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin.php?deleted=error#products");
    exit();
}

$id = intval($_GET['id']);

// Delete product
$stmt = $conn->prepare("DELETE FROM inventory WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: admin.php?deleted=success#products");
    exit();
} else {
    header("Location: admin.php?deleted=error#products");
    exit();
}
?>