<?php
include("db.php");
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// If no items selected, redirect back to cart
if (!isset($_POST['cart_ids']) || empty($_POST['cart_ids'])) {
    header("Location: userDashboard.php");
    exit();
}

$cart_ids = $_POST['cart_ids'];

// Fetch selected cart items
$placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
$types = str_repeat('i', count($cart_ids));

$sql = "SELECT c.id AS cart_id, i.name, i.size, i.price, i.image, c.quantity, c.product_id
        FROM cart c
        JOIN inventory i ON c.product_id = i.id
        WHERE c.id IN ($placeholders) AND c.user_id=?";
$stmt = $conn->prepare($sql);

// Bind params dynamically
$params = array_merge($cart_ids, [$user_id]);
$stmt->bind_param($types . 'i', ...$params);
$stmt->execute();
$result = $stmt->get_result();

$selectedItems = [];
$grandTotal = 0;
while ($row = $result->fetch_assoc()) {
    $selectedItems[] = $row;
    $grandTotal += $row['price'] * $row['quantity'];
}

// Handle order confirmation
if (isset($_POST['confirm_order'])) {
    // Insert order
    $orderStmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, order_date) VALUES (?, ?, ?, NOW())");
    $paymentMethod = "Cash on Delivery";
    $orderStmt->bind_param("ids", $user_id, $grandTotal, $paymentMethod);
    $orderStmt->execute();
    $order_id = $orderStmt->insert_id;

    // Insert order items
    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
    foreach ($selectedItems as $item) {
        $itemStmt->bind_param("iii", $order_id, $item['product_id'], $item['quantity']);
        $itemStmt->execute();
    }

    // Remove items from cart
    $deleteStmt = $conn->prepare("DELETE FROM cart WHERE id IN ($placeholders) AND user_id=?");
    $deleteStmt->bind_param($types . 'i', ...$params);
    $deleteStmt->execute();

    $_SESSION['order_success'] = "✅ Your order has been placed successfully!";
    header("Location: order_success.php"); // Create a success page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Review Checkout</title>
<link rel="stylesheet" href="user.css">
</head>
<body>
<h1>Review Your Order</h1>

<form method="POST" action="">
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($selectedItems as $item): ?>
            <tr>
                <td>
                    <img src="<?= htmlspecialchars($item['image']) ?>" style="width:50px;height:50px;object-fit:cover;">
                    <?= htmlspecialchars($item['name']) ?>
                </td>
                <td><?= htmlspecialchars($item['size']) ?></td>
                <td>₱<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
            <input type="hidden" name="cart_ids[]" value="<?= $item['cart_id'] ?>">
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right;"><strong>Grand Total:</strong></td>
                <td><strong>₱<?= number_format($grandTotal, 2) ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <h3>Payment Method: Cash on Delivery</h3>

    <div style="margin-top:20px;">
        <button type="submit" name="confirm_order" class="btn">Confirm Order</button>
        <a href="userDashboard.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>
</body>
</html>
