<?php
include('db.php');
session_start();

header('Content-Type: application/json');

try {
    $currentUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    $since = isset($_GET['since']) ? (int)$_GET['since'] : 0;
    $partnerId = isset($_GET['partner']) ? (int)$_GET['partner'] : 0;

    // Resolve admin id
    $adminEmail = 'admin@revalue.com';
    $stmtA = $conn->prepare("SELECT id FROM users WHERE E_mail=? LIMIT 1");
    $stmtA->bind_param('s', $adminEmail);
    $stmtA->execute();
    $adminRes = $stmtA->get_result()->fetch_assoc();
    $stmtA->close();
    $adminId = $adminRes ? (int)$adminRes['id'] : 0;

    // Determine conversation partner
    if ($currentUserId === $adminId && $adminId) {
        if ($partnerId <= 0) {
            echo json_encode(['success' => true, 'messages' => []]);
            exit;
        }
        $userA = $adminId;
        $userB = $partnerId;
    } else {
        // If no session, treat requester as admin for admin dashboard use
        if ($currentUserId === 0 && $adminId) {
            $currentUserId = $adminId;
        }
        $userA = $currentUserId;
        $userB = $adminId ?: $partnerId;
    }

    // Fetch messages with names resolved from users (fallback to email if name empty)
    $sql = "
        SELECT m.id,
               m.sender_id,
               COALESCE(NULLIF(us.Full_name, ''), us.E_mail) AS sender_name,
               m.receiver_id,
               COALESCE(NULLIF(ur.Full_name, ''), ur.E_mail) AS receiver_name,
               m.body,
               m.created_at
        FROM messages m
        JOIN users us ON us.id = m.sender_id
        JOIN users ur ON ur.id = m.receiver_id
        WHERE m.id > ? AND ((m.sender_id=? AND m.receiver_id=?) OR (m.sender_id=? AND m.receiver_id=?))
        ORDER BY m.id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiiii', $since, $userA, $userB, $userB, $userA);
    $stmt->execute();
    $res = $stmt->get_result();
    $messages = [];
    while ($m = $res->fetch_assoc()) {
        $messages[] = [
            'id' => (int)$m['id'],
            'sender_id' => (int)$m['sender_id'],
            'sender_name' => $m['sender_name'],
            'receiver_id' => (int)$m['receiver_id'],
            'receiver_name' => $m['receiver_name'],
            'body' => $m['body'],
            'created_at' => $m['created_at']
        ];
    }
    $stmt->close();

    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'server_error']);
}
?>

