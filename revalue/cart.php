<?php
include("db.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);

    // Check if item already exists in cart
    $check = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // If already exists, just increase quantity
        $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id=? AND product_id=?");
        $update->bind_param("ii", $user_id, $product_id);
        $update->execute();
    } else {
        // Insert new
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();
    }
}

header("Location: index.php");
exit();