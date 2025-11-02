<?php
include('db.php');
session_start();

header('Content-Type: application/json');

try {
    // Find admin id by email
    $adminEmail = 'admin@revalue.com';
    $stmtA = $conn->prepare("SELECT id FROM users WHERE E_mail=? LIMIT 1");
    $stmtA->bind_param('s', $adminEmail);
    $stmtA->execute();
    $adminRes = $stmtA->get_result()->fetch_assoc();
    $stmtA->close();
    if (!$adminRes) {
        echo json_encode(['success' => false, 'error' => 'admin_missing', 'users' => []]);
        exit;
    }
    $adminId = (int)$adminRes['id'];

    // Partners are distinct users who have messaged with admin
    $sql = "
      SELECT u.id,
             COALESCE(NULLIF(u.Full_name,''), u.E_mail) AS name,
             u.E_mail AS email,
             MAX(m.created_at) AS last_ts
      FROM messages m
      JOIN users u ON u.id = CASE WHEN m.sender_id = ? THEN m.receiver_id WHEN m.receiver_id = ? THEN m.sender_id END
      WHERE (m.sender_id = ? OR m.receiver_id = ?)
      GROUP BY u.id
      ORDER BY last_ts DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $adminId, $adminId, $adminId, $adminId);
    $stmt->execute();
    $res = $stmt->get_result();
    $users = [];
    while ($row = $res->fetch_assoc()) {
        $users[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'email' => $row['email']
        ];
    }
    $stmt->close();

    echo json_encode(['success' => true, 'users' => $users]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'server_error', 'users' => []]);
}
?>


