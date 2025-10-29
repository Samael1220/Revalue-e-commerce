<?php
include("db.php");
session_start();

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'] ?? 'Customer';
$order_id = $_SESSION['order_success_id'] ?? null;

// Handle inventory deletion when user clicks buttons
if (isset($_GET['delete_inventory']) && $order_id) {
    // Fetch items from this order
    $stmtItems = $conn->prepare("SELECT product_id FROM order_items WHERE order_id=?");
    $stmtItems->bind_param("i", $order_id);
    $stmtItems->execute();
    $items = $stmtItems->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmtItems->close();

    // Delete from inventory
    $stmtDel = $conn->prepare("DELETE FROM inventory WHERE id=?");
    foreach ($items as $item) {
        $stmtDel->bind_param("i", $item['product_id']);
        $stmtDel->execute();
    }
    $stmtDel->close();

    // Clear session order id so deletion doesn't repeat
    unset($_SESSION['order_success_id']);

    // Redirect to desired page
    $redirect = $_GET['redirect'] ?? 'userDashboard.php';
    header("Location: $redirect");
    exit();
}

// Fetch latest order for this user
if (!$order_id) {
    $stmtOrder = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY order_date DESC LIMIT 1");
    $stmtOrder->bind_param("i", $user_id);
} else {
    $stmtOrder = $conn->prepare("SELECT * FROM orders WHERE id=? AND user_id=?");
    $stmtOrder->bind_param("ii", $order_id, $user_id);
}
$stmtOrder->execute();
$orderResult = $stmtOrder->get_result();
$order = $orderResult->fetch_assoc();
$stmtOrder->close();

if (!$order) {
    echo "<h2>No recent order found.</h2>";
    exit();
}

$order_id = $order['id'];

// Fetch order items
$stmtItems = $conn->prepare("SELECT product_name, product_image, size, quantity, price FROM order_items WHERE order_id=?");
$stmtItems->bind_param("i", $order_id);
$stmtItems->execute();
$orderItems = $stmtItems->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtItems->close();

// Shipping address and total
$shippingAddress = htmlspecialchars($order['shipping_address'] ?? 'N/A');
$grandTotal = $order['total_amount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success - Re-Value.PH</title>
<link rel="stylesheet" href="user.css">
<link rel="stylesheet" href="order.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="success-header">
    <div class="success-container">
        <div class="success-icon"><i class="fas fa-check-circle"></i></div>
        <h1 class="success-title">Order Placed Successfully!</h1>
        <p class="success-subtitle">
            Thank you for your purchase, <strong><?= htmlspecialchars($full_name) ?></strong>. Your order is being processed.
        </p>
    </div>
</div>

<div class="order-success-container">
    <div class="order-content">

        <div class="order-summary-card">
            <div class="card-header">
                <h2><i class="fas fa-receipt"></i> Order Summary</h2>
                <div class="order-number">Order #<?= $order_id ?></div>
            </div>
            <div class="order-details">
                <div class="order-info-grid">
                    <div class="info-item">
                        <div class="info-label">Order Date</div>
                        <div class="info-value"><?= date("M d, Y", strtotime($order['order_date'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Shipping Address</div>
                        <div class="info-value"><?= $shippingAddress ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Amount</div>
                        <div class="info-value total-amount">₱<?= number_format($grandTotal, 2) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="products-section">
            <h3><i class="fas fa-shopping-bag"></i> Items Ordered</h3>
            <div class="products-list">
                <?php if (!empty($orderItems)): ?>
                    <?php foreach ($orderItems as $item): ?>
                        <div class="product-item">
                            <div class="product-image">
                                <img src="<?= htmlspecialchars($item['product_image'] ?: 'uploads/default.jpg') ?>" 
                                     alt="<?= htmlspecialchars($item['product_name']) ?>">
                            </div>
                            <div class="product-details">
                                <h4 class="product-name"><?= htmlspecialchars($item['product_name']) ?></h4>
                                <div class="product-meta">
                                    <span class="product-size">Size: <?= htmlspecialchars($item['size'] ?: 'N/A') ?></span>
                                    <span class="product-quantity">Qty: <?= htmlspecialchars($item['quantity']) ?></span>
                                </div>
                                <div class="product-price">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div>No products found for this order.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="next-steps-card">
            <h3><i class="fas fa-clock"></i> What's Next?</h3>
            <div class="steps-list">
                <div class="step-item completed">
                    <div class="step-icon"><i class="fas fa-check"></i></div>
                    <div class="step-content">
                        <h4>Order Pending</h4>
                        <p>Your order has been successfully placed and pending.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-icon"><i class="fas fa-box"></i></div>
                    <div class="step-content">
                        <h4>Processing</h4>
                        <p>We're preparing your items for delivery.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-icon"><i class="fas fa-truck"></i></div>
                    <div class="step-content">
                        <h4>Delivery</h4>
                        <p>Your order will be delivered within 3-5 business days.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="order_success.php?delete_inventory=1&redirect=userDashboard.php" 
               class="btn btn-primary f"><i class="fas fa-tachometer-alt"></i> Back to Dashboard</a>
            <a href="order_success.php?delete_inventory=1&redirect=index.php" 
               class="btn btn-secondary f"><i class="fas fa-shopping-cart"></i> Continue Shopping</a>
        </div>

    </div>
</div>

<script>
const cards = document.querySelectorAll('.order-summary-card, .products-section, .next-steps-card');
cards.forEach(card => {
    card.addEventListener('mouseenter', () => card.style.transform = 'translateY(-2px)');
    card.addEventListener('mouseleave', () => card.style.transform = 'translateY(0)');
});
</script>
</body>
</html>
