<?php
include("db.php");
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user addresses
$stmtAddr = $conn->prepare("SELECT address, address2, address3 FROM users WHERE id=?");
$stmtAddr->bind_param("i", $user_id);
$stmtAddr->execute();
$userAddresses = $stmtAddr->get_result()->fetch_assoc();
$stmtAddr->close();

// Fetch cart items for this user
$stmtCart = $conn->prepare("
    SELECT c.id AS cart_id, i.id AS product_id, i.name, i.size, i.price, i.image, c.quantity
    FROM cart c
    JOIN inventory i ON c.product_id = i.id
    WHERE c.user_id = ?
");
$stmtCart->bind_param("i", $user_id);
$stmtCart->execute();
$selectedItems = $stmtCart->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtCart->close();

if (!$selectedItems) {
    header("Location: userDashboard.php");
    exit();
}

// Calculate grand total
$grandTotal = array_reduce($selectedItems, function($carry, $item) {
    return $carry + ($item['price'] * $item['quantity']);
}, 0.0);

// Handle order confirmation
if (isset($_POST['confirm_order'])) {
    $shippingAddress = $_POST['shipping_address'] ?? '';
    $conn->begin_transaction();

    try {
        // Prepare product details for JSON storage
        $productNames = [];
        $productImages = [];
        $productSizes = [];
        foreach ($selectedItems as $item) {
            $productNames[] = $item['name'];
            $productImages[] = $item['image'];
            $productSizes[] = $item['size'];
        }
        $namesJson = json_encode($productNames);
        $imagesJson = json_encode($productImages);
        $sizesJson = json_encode($productSizes);

        // Insert into orders
        $stmtOrder = $conn->prepare("
            INSERT INTO orders (user_id, total_amount, payment_method, order_date, shipping_address, product_names, product_images, product_sizes)
            VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)
        ");
        $paymentMethod = "Cash on Delivery";
        $stmtOrder->bind_param("idsssss", $user_id, $grandTotal, $paymentMethod, $shippingAddress, $namesJson, $imagesJson, $sizesJson);
        $stmtOrder->execute();
        $order_id = $stmtOrder->insert_id;
        $stmtOrder->close();

        // Insert each item into order_items
        $stmtItem = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, product_image, size, quantity, price)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($selectedItems as $item) {
            $stmtItem->bind_param(
                "iisssid",
                $order_id,
                $item['product_id'],
                $item['name'],
                $item['image'],
                $item['size'],
                $item['quantity'],
                $item['price']
            );
            $stmtItem->execute();
        }
        $stmtItem->close();

        // Delete items from cart
        $stmtDelCart = $conn->prepare("DELETE FROM cart WHERE user_id=?");
        $stmtDelCart->bind_param("i", $user_id);
        $stmtDelCart->execute();
        $stmtDelCart->close();

        $conn->commit();

        $_SESSION['order_success_id'] = $order_id;
        header("Location: order_success.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['order_error'] = "Checkout failed: " . $e->getMessage();
        header("Location: userDashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Review Checkout</title>
<link rel="stylesheet" href="checkout.css">
</head>
<body>
<!-- Keep the PHP part the same as before, just ensure the CSS classes match -->
<div class="checkout-section">
  <div class="checkout-container">
    <div class="checkout-header">
      <div class="header-content">
        <h1 class="checkout-title">Review Your Order</h1>
        <p class="checkout-subtitle">Please confirm your items and total before placing the order.</p>
      </div>
      <div class="progress-indicator">
        <div class="progress-step">
          <span class="step-number">1</span>
          <span class="step-label">Cart</span>
        </div>
        <div class="progress-step active">
          <span class="step-number">2</span>
          <span class="step-label">Review</span>
        </div>
        <div class="progress-step">
          <span class="step-number">3</span>
          <span class="step-label">Complete</span>
        </div>
      </div>
    </div>

    <div class="checkout-layout">
      <!-- Order Summary -->
      <div class="order-summary">
        <div class="summary-header">
          <h3>Order Summary</h3>
          <span class="items-count"><?= count($selectedItems) ?> item<?= count($selectedItems) !== 1 ? 's' : '' ?></span>
        </div>

        <div class="cart-items">
          <?php foreach ($selectedItems as $item): ?>
            <div class="cart-item">
              <div class="item-image">
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-thumb">
              </div>
              <div class="item-details">
                <h4 class="item-name"><?= htmlspecialchars($item['name']) ?></h4>
                <div class="item-meta">
                  <span class="item-size">Size: <?= htmlspecialchars($item['size']) ?></span>
                  
                </div>
                <div class="item-price">₱<?= number_format($item['price'], 2) ?></div>
              </div>
              <div class="item-total">
                ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="order-totals">
          <div class="total-row">
            <span>Subtotal</span>
            <span>₱<?= number_format($grandTotal, 2) ?></span>
          </div>
          <div class="total-row">
            <span>Shipping</span>
            <span>Free</span>
          </div>
          <div class="total-row final">
            <span>Total</span>
            <span class="grand-total">₱<?= number_format($grandTotal, 2) ?></span>
          </div>
        </div>
      </div>

      <!-- Checkout Form -->
      <div class="checkout-form-container">
        <form method="POST" class="checkout-form">
          <div class="form-section">
            <h4 class="section-title">Shipping Address</h4>
            <div class="select-wrapper">
             
              <select name="shipping_address" id="shipping_address" class="styled-select" required>
                <?php foreach (['address','address2','address3'] as $key): ?>
                  <?php if (!empty($userAddresses[$key])): ?>
                    <option value="<?= htmlspecialchars($userAddresses[$key]) ?>">
                      <?= htmlspecialchars($userAddresses[$key]) ?>
                    </option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-section">
            <h4 class="section-title">Payment Method</h4>
            <div class="payment-options">
              <label class="payment-option selected">
                <input type="radio" name="payment_method" value="Cash on Delivery" checked>
                <div class="payment-content">
                  <span class="payment-icon">💵</span>
                  <div class="payment-info">
                    <span class="payment-name">Cash on Delivery</span>
                    <span class="payment-desc">Pay when you receive your order</span>
                  </div>
                </div>
              </label>
            </div>
          </div>

          <div class="order-security">
            <div class="security-features">
              <div class="security-item">
                <span class="security-icon">🔒</span>
                <span>Secure checkout</span>
              </div>
              <div class="security-item">
                <span class="security-icon">🚚</span>
                <span>Free delivery</span>
              </div>
              <div class="security-item">
                <span class="security-icon">↩️</span>
                <span>Easy returns</span>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" name="confirm_order" class="btn-place-order" onclick="return confirm('Are you sure you want to place this order?');">
              <span class="btn-text">Confirm Order</span>
             
            </button>
            <a href="userDashboard.php" class="btn-back">Cancel Order</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>