<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo 'error: not logged in';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['confirmation'])) {
    $orderId = (int)$_POST['order_id'];
    $confirmation = trim($_POST['confirmation']);

    if (strtoupper($confirmation) !== 'RECEIVED') {
        echo 'error: wrong confirmation';
        exit;
    }

    $proofPath = null;

    // Optional image upload
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/proofs/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = 'order_' . $orderId . '_' . time() . '_' . basename($_FILES['productImage']['name']);
        $targetFile = $uploadDir . $filename;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['productImage']['type'], $allowedTypes)) {
            if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFile)) {
                echo 'error: upload failed';
                exit;
            }
            $proofPath = $targetFile;
        } else {
            echo 'error: invalid image type';
            exit;
        }
    }

    // Update the order in DB only if it belongs to the logged-in user
    $stmt = $conn->prepare("UPDATE orders SET status = 'Completed', proof_of_delivery=? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $proofPath, $orderId, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'error: invalid request';
}
