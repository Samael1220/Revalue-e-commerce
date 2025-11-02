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
<div class="review-container">
  <div class="review-card">
    <div class="review-header">
      <div>
        <h1>Review Your Order</h1>
        <p class="review-subtitle">Please confirm your items and total before placing the order.</p>
      </div>
      <span class="review-badge">Cash on Delivery</span>
    </div>

    <form method="POST">
      <div class="review-table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Size</th>
              <th>Price</th>
              <th>Select Shipping Address</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($selectedItems as $item): ?>
            <tr>
              <td>
                <div class="product-cell">
                  <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-thumb">
                  <div><strong><?= htmlspecialchars($item['name']) ?></strong></div>
                </div>
              </td>
              <td><?= htmlspecialchars($item['size']) ?></td>
              <td>₱<?= number_format($item['price'],2) ?></td>
              <td>
                <div class="select-wrapper">
                  <label for="shipping_address" class="sr-only">Shipping address</label>
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
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="review-footer">
        <div class="review-total">
          <span class="label">Grand Total:</span>
          <span>₱<?= number_format($grandTotal,2) ?></span>
        </div>
        <div class="review-actions">
          <button type="submit" name="confirm_order" class="btn btn-primary" onclick="return confirm('Are you sure you want to place this order?');">Confirm Order</button>
          <a href="userDashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
</body>
</html>