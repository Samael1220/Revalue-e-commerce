<?php
session_start();
include("db.php");

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ===================== STEP 1: CHECK IF ITEMS SELECTED =====================
if (!isset($_POST['cart_ids']) && !isset($_POST['confirm_checkout'])) {
    header("Location: userDashboard.php#cart");
    exit();
}

// Convert cart IDs to array
$cart_ids = isset($_POST['cart_ids']) ? $_POST['cart_ids'] : [];
if (is_string($cart_ids)) {
    $cart_ids = explode(',', $cart_ids);
}

if (empty($cart_ids)) {
    die("⚠️ No items selected for checkout.");
}

// ===================== STEP 2: FETCH ITEMS =====================
$cart_ids_escaped = implode(',', array_map('intval', $cart_ids)); // prevents SQL injection
$sql = "
    SELECT 
        c.id AS cart_id, 
        i.id AS product_id, 
        i.name, 
        i.price, 
        c.quantity 
    FROM cart c
    JOIN inventory i ON c.product_id = i.id
    WHERE c.id IN ($cart_ids_escaped) AND c.user_id = $user_id
";
$result = mysqli_query($conn, $sql);

$orderItems = [];
$grandTotal = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $subtotal = $row['price'] * $row['quantity'];
    $grandTotal += $subtotal;
    $orderItems[] = $row;
}

if (empty($orderItems)) {
    die("⚠️ No matching items found in your cart.");
}

// ===================== STEP 3: CONFIRM CHECKOUT =====================
if (isset($_POST['confirm_checkout'])) {
    // Insert order
    $order = $conn->prepare("
        INSERT INTO orders (user_id, total_amount, order_date, status, payment_method)
        VALUES (?, ?, NOW(), 'Pending', 'Cash on Delivery')
    ");
    $order->bind_param("id", $user_id, $grandTotal);
    $order->execute();
    $order_id = $order->insert_id;
    $order->close();

    // Insert into order_items
    foreach ($orderItems as $item) {
        $oi = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $oi->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $oi->execute();
        $oi->close();
    }

    // ✅ Delete purchased items from cart (using direct query to avoid binding issues)
    $delete_sql = "DELETE FROM cart WHERE id IN ($cart_ids_escaped) AND user_id = $user_id";
    mysqli_query($conn, $delete_sql);

    // ✅ Delete purchased items from inventory (optional: if each product is unique)
    foreach ($orderItems as $item) {
        $product_id = (int)$item['product_id'];
        mysqli_query($conn, "DELETE FROM inventory WHERE id = $product_id");
    }

    // ✅ Redirect after checkout success
    header("Location: userDashboard.php#orders");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Review</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9fafb;
            margin: 0;
            padding: 30px;
        }
        .checkout-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 800px;
            margin: auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f1f1f1;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        button {
            background: #10b981;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background: #059669;
        }
        .cancel {
            background: #ef4444;
            margin-left: 10px;
        }
        .cancel:hover {
            background: #dc2626;
        }
    </style>
</head>
<body>

<div class="checkout-container">
    <h2>🛒 Review Your Order</h2>
    <p>Please review your selected items before confirming checkout.</p>

    <form method="POST">
        <table>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
            <?php foreach ($orderItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₱<?= number_format($item['price'], 2) ?></td>
                    <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="total">Grand Total: ₱<?= number_format($grandTotal, 2) ?></div>
        <div class="total">Payment Method: <strong>Cash on Delivery</strong></div>

        <?php foreach ($cart_ids as $cid): ?>
            <input type="hidden" name="cart_ids[]" value="<?= htmlspecialchars($cid) ?>">
        <?php endforeach; ?>

        <button type="submit" name="confirm_checkout">✅ Confirm Order</button>
        <button type="button" class="cancel" onclick="window.location.href='userDashboard.php#cart'">❌ Cancel</button>
    </form>
</div>

</body>
</html>
