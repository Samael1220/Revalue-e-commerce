<?php
include("db.php");
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle AJAX request for address update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_address'])) {
    $type = $_POST['address_type'] ?? '';
    $new_address = trim($_POST['new_address']);

    // Validate the type parameter
    $valid_types = ['main', 'home', 'work'];
    if (!in_array($type, $valid_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid address type.']);
        exit();
    }

    // Map address type to database column
    $column = match ($type) {
        'main' => 'address',
        'home' => 'address2',
        'work' => 'address3',
    };

    $stmt = $conn->prepare("UPDATE users SET $column=? WHERE id=?");
    $stmt->bind_param("si", $new_address, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Address updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update address.']);
    }
    exit();
}

// Handle AJAX request for fetching current address
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_address'])) {
    $type = $_GET['type'] ?? '';
    
    // Validate the type parameter
    $valid_types = ['main', 'home', 'work'];
    if (!in_array($type, $valid_types)) {
        echo json_encode(['success' => false, 'message' => 'Invalid address type.']);
        exit();
    }

    // Map address type to database column
    $column = match ($type) {
        'main' => 'address',
        'home' => 'address2',
        'work' => 'address3',
    };

    $result = mysqli_query($conn, "SELECT $column FROM users WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($result);
    $current_address = $user[$column] ?? "";

    echo json_encode(['success' => true, 'address' => $current_address]);
    exit();
}
?>

<?php
// If we reach here, it means no valid AJAX request was made
// Redirect back to dashboard
header("Location: userDashboard.php");
exit();
?>
