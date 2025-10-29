<?php
include("db.php");
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
header('Content-Type: application/json'); // Ensure JSON responses

$valid_types = ['main', 'home', 'work'];
$columns_map = [
    'main' => 'address',
    'home' => 'address2',
    'work' => 'address3'
];

// ---------- HANDLE ADDRESS UPDATE ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_address'])) {
    $type = $_POST['address_type'] ?? '';
    $new_address = trim($_POST['new_address']);

    if (!in_array($type, $valid_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid address type.']);
        exit();
    }

    $column = $columns_map[$type];
    if ($new_address === '') $new_address = ' '; // Avoid NOT NULL errors

    // Use prepared statement to update DB
    $stmt = $conn->prepare("UPDATE users SET `$column`=? WHERE id=?");
    $stmt->bind_param("si", $new_address, $user_id);

    if ($stmt->execute()) {
    // ✅ Read back directly from DB to confirm
    $verify = $conn->prepare("SELECT `$column` AS addr FROM users WHERE id=?");
    $verify->bind_param("i", $user_id);
    $verify->execute();
    $verified = $verify->get_result()->fetch_assoc();
    $db_address = $verified['addr'] ?? '';

    // Update session immediately
    $_SESSION[$column] = $db_address;

    echo json_encode([
        'success' => true,
        'message' => 'Address updated successfully!',
        'new_address' => $db_address
    ]);
}
    exit();
}

// ---------- HANDLE ADDRESS FETCH ----------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_address'])) {
    $type = $_GET['type'] ?? '';

    if (!in_array($type, $valid_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid address type.']);
        exit();
    }

    $column = $columns_map[$type];

    $stmt = $conn->prepare("SELECT `$column` AS addr FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $current_address = $row['addr'] ?? "";

    echo json_encode(['success' => true, 'address' => $current_address]);
    exit();
}

// Invalid request fallback
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
exit();
?>
