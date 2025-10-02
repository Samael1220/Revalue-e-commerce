<?php
include("db.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
}

header("Location: userDashboard.php?section=cart");
exit();
