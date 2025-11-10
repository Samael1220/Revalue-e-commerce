<?php
include('db.php');

if (isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    $stmt = $conn->prepare("UPDATE orders SET status = 'Delivered' WHERE id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
}
?>