<?php
include("db.php");
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch cart items with product details
    $query = "
        SELECT 
            c.id as cart_id,
            c.quantity,
            i.name,
            i.price,
            i.image,
            i.size
        FROM cart c
        JOIN inventory i ON c.product_id = i.id
        WHERE c.user_id = ?
        ORDER BY c.id DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        $itemTotal = $row['price'] * $row['quantity'];
        $total += $itemTotal;
        
        $items[] = [
            'cart_id' => $row['cart_id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'quantity' => $row['quantity'],
            'image' => $row['image'],
            'size' => $row['size'],
            'total' => $itemTotal
        ];
    }
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'count' => count($items)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
