<?php
include("db.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Step 1: Review selected items
if (isset($_POST['review_checkout']) || isset($_POST['confirm_checkout'])) {
    $cart_ids = isset($_POST['cart_ids']) ? $_POST['cart_ids'] : [];
    
    if (empty($cart_ids)) {
        die("No items selected for checkout.");
    }

    // Make sure $cart_ids is an array
    if (is_string($cart_ids)) {
        $cart_ids = explode(',', $cart_ids);
    }

    // Fetch selected cart items
    $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
    $types = str_repeat('i', count($cart_ids));
    $stmt = $conn->prepare("SELECT c.id as cart_id, i.name, i.price, i.quantity FROM cart c JOIN inventory i ON c.product_id=i.id WHERE c.id IN ($placeholders) AND c.user_id=?");
    $stmt->bind_param($types.'i', ...$cart_ids, $user_id);
    $stmt->execute();
    $items = $stmt->get_result();

    $grandTotal = 0;
    $cartIdsHidden = '';
    while ($item = $items->fetch_assoc()) {
        $grandTotal += $item['price'] * $item['quantity'];
        $cartIdsHidden .= '<input type="hidden" name="cart_ids[]" value="'.$item['cart_id'].'">';
    }

    // Step 2: Confirm checkout
    if (isset($_POST['confirm_checkout'])) {
        // Insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_date, status) VALUES (?, ?, NOW(), 'Pending')");
        $stmt->bind_param("id", $user_id, $grandTotal);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Insert into order_items
        foreach ($cart_ids as $cart_id) {
            $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) SELECT ?, product_id, quantity FROM cart WHERE id=?");
            $itemStmt->bind_param("ii", $order_id, $cart_id);
            $itemStmt->execute();
            $itemStmt->close();
        }

        // Remove checked items from cart
        $deleteStmt = $conn->prepare("DELETE FROM cart WHERE id IN (".implode(',', array_fill(0, count($cart_ids), '?')).") AND user_id=?");
        $deleteStmt->bind_param($types.'i', ...$cart_ids, $user_id);
        $deleteStmt->execute();

        echo "<h2>✅ Order placed successfully!</h2>";
        exit;
    }

} else {
    header("Location: userDashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout Review</title>
</head>
<body>
<h2>Review Your Order</h2>
<form method="POST">
    <table border="1" cellpadding="5">
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
        <?php
        $stmt->execute();
        $items = $stmt->get_result();
        while ($item = $items->fetch_assoc()):
        ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Grand Total: ₱<?= number_format($grandTotal, 2) ?></h3>
    <h4>Payment Method: Cash on Delivery</h4>

    <?= $cartIdsHidden ?>
    <button type="submit" name="confirm_checkout">Confirm Order</button>
</form>
</body>
</html>