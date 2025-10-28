<?php
include("db.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    // If it's an AJAX call, return JSON; else redirect
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

$user_id = $_SESSION['user_id'];

// AJAX removal (POST): expects cart_id and returns JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    header('Content-Type: application/json');

    $cart_id = intval($_POST['cart_id']);
    if ($cart_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }
    $stmt->bind_param("ii", $cart_id, $user_id);
    $ok = $stmt->execute();

    if ($ok && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Item removed from cart.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Item not found or already removed.']);
    }
    exit();
}

// Legacy removal (GET): keeps old behavior and redirects
if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']);
    if ($cart_id > 0) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
        if ($stmt) {
            $stmt->bind_param("ii", $cart_id, $user_id);
            $stmt->execute();
        }
    }
    header("Location: userDashboard.php?section=cart");
    exit();
}

// Fallback redirect
header("Location: userDashboard.php");
exit();
