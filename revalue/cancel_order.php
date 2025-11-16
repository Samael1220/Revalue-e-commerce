<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];

    // Check if order exists and is not already cancelled
    $checkStmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
    $checkStmt->bind_param("i", $order_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "error: Order not found";
        $checkStmt->close();
        exit;
    }
    
    $order = $result->fetch_assoc();
    $checkStmt->close();
    
    // Check if already cancelled
    if (strtolower($order['status']) === 'cancelled' || strtolower($order['status']) === 'canceled') {
        echo "error: Order is already cancelled";
        exit;
    }

    // Update order status to Cancelled
    $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "error: Invalid request";
}
?>


