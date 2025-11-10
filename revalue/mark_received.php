<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['confirmation'])) {
    $orderId = (int)$_POST['order_id'];
    $confirmation = trim($_POST['confirmation']);

    if (strtoupper($confirmation) !== 'RECEIVED') {
        echo 'error';
        exit;
    }

    $stmt = $conn->prepare("UPDATE orders SET status = 'Completed' WHERE id = ?");
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        // Log actual DB error if needed
        // error_log($stmt->error);
        echo 'error';
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'error';
}
