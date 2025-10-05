<?php
include("db.php");
session_start();

// Set content type to JSON
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to cart']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);

    // Get product name for success message
    $productQuery = $conn->prepare("SELECT name FROM inventory WHERE id = ?");
    $productQuery->bind_param("i", $product_id);
    $productQuery->execute();
    $productResult = $productQuery->get_result();
    $productName = $productResult->fetch_assoc()['name'] ?? 'Product';

    // Check if item already exists in cart
    $check = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // If already exists, just increase quantity
        $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id=? AND product_id=?");
        $update->bind_param("ii", $user_id, $product_id);
        if ($update->execute()) {
            echo json_encode(['success' => true, 'message' => "Added to cart successfully"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to cart']);
        }
    } else {
        // Insert new
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $product_id);
        if ($insert->execute()) {
            echo json_encode(['success' => true, 'message' => "Added to cart successfully"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add to cart']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
exit();