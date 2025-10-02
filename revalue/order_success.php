<?php
include("db.php");
session_start();

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get latest order for this user
$user_id = $_SESSION['user_id'];

$orderStmt = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY order_date DESC LIMIT 1");
$orderStmt->bind_param("i", $user_id);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
$order = $orderResult->fetch_assoc();
$orderStmt->close();

if (!$order) {
    echo "No recent order found.";
    exit();
}

// Get order items
$itemsStmt = $conn->prepare("
    SELECT i.name, i.size, i.price, oi.quantity
    FROM order_items oi
    JOIN inventory i ON oi.product_id = i.id
    WHERE oi.order_id=?
");
$itemsStmt->bind_param("i", $order['id']);
$itemsStmt->execute();
$itemsResult = $itemsStmt->get_result();
$items = [];
while ($row = $itemsResult->fetch_assoc()) {
    $items[] = $row;
}
$itemsStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success</title>
<link rel="stylesheet" href="user.css">
</head>
<body>
<div class="container">
    <h1>✅ Order Placed Successfully!</h1>
    <p>Thank you for your purchase, <?= htmlspecialchars($_SESSION['full_name']) ?>.</p>

    <h2>Order Summary</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= htmlspecialchars($item['size']) ?></td>
                <td>₱<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align:right;">Grand Total:</th>
                <th>₱<?= number_format($order['total_amount'], 2) ?></th>
            </tr>
            <tr>
                <th colspan="4" style="text-align:right;">Payment Method:</th>
                <th><?= htmlspecialchars($order['payment_method'] ?? 'Cash on Delivery') ?></th>
            </tr>
            <tr>
                <th colspan="4" style="text-align:right;">Order Date:</th>
                <th><?= date("M d, Y H:i", strtotime($order['order_date'])) ?></th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top:20px; text-align:center;">
        <a href="userDashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</div>
</body>
</html>
