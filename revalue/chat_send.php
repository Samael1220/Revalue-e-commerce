<?php
include('db.php');
session_start();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'method_not_allowed']);
        exit;
    }

    $senderId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    $body = trim($_POST['message'] ?? '');
    if ($body === '') {
        echo json_encode(['success' => false, 'error' => 'empty']);
        exit;
    }

    // Find admin id
    $adminEmail = 'admin@revalue.com';
    $stmtA = $conn->prepare("SELECT id FROM users WHERE E_mail=? LIMIT 1");
    $stmtA->bind_param('s', $adminEmail);
    $stmtA->execute();
    $adminRes = $stmtA->get_result()->fetch_assoc();
    $stmtA->close();
    if (!$adminRes) {
        echo json_encode(['success' => false, 'error' => 'admin_missing']);
        exit;
    }
    $adminId = (int)$adminRes['id'];

    // Determine receiver
    // If sender is admin, expect 'to' parameter; else, receiver is admin
    if ($senderId === $adminId || $senderId === 0) {
        $to = isset($_POST['to']) ? (int)$_POST['to'] : 0;
        if ($to <= 0) {
            echo json_encode(['success' => false, 'error' => 'no_receiver']);
            exit;
        }
        $receiverId = $to;
        if ($senderId === 0) { $senderId = $adminId; }
    } else {
        $receiverId = $adminId;
    }

    // Resolve display names (fallback to email)
    $nameSql = "SELECT id, COALESCE(NULLIF(Full_name,''), E_mail) AS display_name FROM users WHERE id IN (?, ?) ORDER BY id";
    $ns = $conn->prepare($nameSql);
    $ns->bind_param('ii', $senderId, $receiverId);
    $ns->execute();
    $nr = $ns->get_result();
    $senderName = '';
    $receiverName = '';
    while ($row = $nr->fetch_assoc()) {
        if ((int)$row['id'] === $senderId) $senderName = $row['display_name'];
        if ((int)$row['id'] === $receiverId) $receiverName = $row['display_name'];
    }
    $ns->close();

    // Check if denormalized name columns exist; insert accordingly
    $hasSenderName = false; $hasReceiverName = false;
    if ($resCols = $conn->query("SHOW COLUMNS FROM messages LIKE 'sender_name'")) {
        $hasSenderName = $resCols->num_rows > 0; $resCols->close();
    }
    if ($resColr = $conn->query("SHOW COLUMNS FROM messages LIKE 'receiver_name'")) {
        $hasReceiverName = $resColr->num_rows > 0; $resColr->close();
    }

    if ($hasSenderName && $hasReceiverName) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_name, receiver_id, receiver_name, body, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param('isiss', $senderId, $senderName, $receiverId, $receiverName, $body);
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, body, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param('iis', $senderId, $receiverId, $body);
    }
    $stmt->execute();
    $insertedId = $stmt->insert_id;
    $stmt->close();

    echo json_encode(['success' => true, 'id' => (int)$insertedId]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'server_error']);
}
?>

