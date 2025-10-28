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

// Parse product names and images from JSON
$productNames = json_decode($order['product_names'] ?? '[]', true);
$productImages = json_decode($order['product_images'] ?? '[]', true);
$productPrices = json_decode($order['product_prices'] ?? '[]', true);
$productQuantities = json_decode($order['product_quantities'] ?? '[]', true);
$productSizes = json_decode($order['product_sizes'] ?? '[]', true);
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
<!-- Success Header -->
<div class="success-header">
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="success-title">Order Placed Successfully!</h1>
        <p class="success-subtitle">Thank you for your purchase, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong>. Your order is being processed.</p>
    </div>
</div>

<!-- Main Content -->
<div class="order-success-container">
    <div class="order-content">
        <!-- Order Summary Card -->
        <div class="order-summary-card">
            <div class="card-header">
                <h2><i class="fas fa-receipt"></i> Order Summary</h2>
                <div class="order-number">Order #<?= $order['id'] ?></div>
            </div>
            
            <div class="order-details">
                <div class="order-info-grid">
                    <div class="info-item">
                        <div class="info-label">Order Date</div>
                        <div class="info-value"><?= date("M d, Y", strtotime($order['order_date'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value"><?= htmlspecialchars($order['payment_method'] ?? 'Cash on Delivery') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Total Amount</div>
                        <div class="info-value total-amount">₱<?= number_format($order['total_amount'], 2) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products List -->
        <div class="products-section">
            <h3><i class="fas fa-shopping-bag"></i> Items Ordered</h3>
            <div class="products-list">
                <?php for ($i = 0; $i < count($productNames); $i++): ?>
                <div class="product-item">
                    <div class="product-image">
                        <img src="<?= htmlspecialchars($productImages[$i] ?? 'uploads/default.jpg') ?>" alt="<?= htmlspecialchars($productNames[$i]) ?>">
                    </div>
                    <div class="product-details">
                        <h4 class="product-name"><?= htmlspecialchars($productNames[$i]) ?></h4>
                        <div class="product-meta">
                            <span class="product-size">Size: <?= htmlspecialchars($productSizes[$i] ?? 'N/A') ?></span>
                          
                        </div>
                        <div class="product-price">₱<?= number_format($productPrices[$i] ?? 0, 2) ?></div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="next-steps-card">
            <h3><i class="fas fa-clock"></i> What's Next?</h3>
            <div class="steps-list">
                <div class="step-item completed">
                    <div class="step-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-content">
                        <h4>Order Pending</h4>
                        <p>Your order has been successfully placed and pending.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="step-content">
                        <h4>Processing</h4>
                        <p>We're preparing your items for delivery.</p>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <div class="step-content">
                        <h4>Delivery</h4>
                        <p>Your order will be delivered within 3-5 business days.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="userDashboard.php" class="btn btn-primary f">
                <i class="fas fa-tachometer-alt"></i>
                Back to Dashboard
            </a>
            <a href="index.php" class="btn btn-secondary f">
                <i class="fas fa-shopping-cart"></i>
                Continue Shopping
            </a>
        </div>
    </div>
</div>



<script>    
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.order-summary-card, .products-section, .next-steps-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
</body>
</html>
