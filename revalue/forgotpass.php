<?php
include("db.php");
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (!$email || !$new_password || !$confirm_password) {
        $message = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE E_mail=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $message = "No account found with that email.";
        } else {
            $user = $result->fetch_assoc();
            $hashed_pass = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE users SET Pass=? WHERE id=?");
            $update->bind_param("si", $hashed_pass, $user['id']);

            if ($update->execute()) {
                $message = "Password successfully updated. You can now <a href='index.php'>login</a>.";
            } else {
                $message = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password - Re-Value.PH</title>
<link rel="stylesheet" href="user.css">
<style>
.forgot-password-container {
    max-width: 400px;
    margin: 50px auto;
    padding: 25px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
}
.forgot-password-container h1 {
    text-align: center;
    margin-bottom: 20px;
}
.forgot-password-container form {
    display: flex;
    flex-direction: column;
}
.forgot-password-container label {
    margin: 10px 0 5px;
}
.forgot-password-container input {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.forgot-password-container button {
    margin-top: 20px;
    padding: 10px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.forgot-password-container .message {
    margin-bottom: 15px;
    padding: 10px;
    background: #ffefc2;
    border: 1px solid #ffd966;
    border-radius: 4px;
}
.password-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.password-wrapper input {
    flex: 1;
    padding-right: 60px;
}
.password-wrapper .toggle-password {
    position: absolute;
    right: 5px;
    background: #ddd;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
    border-radius: 4px;
}
</style>
</head>
<body>

<div class="forgot-password-container">
    <h1>Reset Your Password</h1>
    <p>Enter your email and new password below.</p>

    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="new_password">New Password:</label>
        <div class="password-wrapper">
            <input type="password" name="new_password" id="new_password" required>
            <button type="button" class="toggle-password" onclick="togglePassword('new_password')">Show</button>
        </div>

        <label for="confirm_password">Confirm Password:</label>
        <div class="password-wrapper">
            <input type="password" name="confirm_password" id="confirm_password" required>
            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">Show</button>
        </div>

        <button type="submit">Reset Password</button>
    </form>

    <p style="text-align:center; margin-top:15px;">
        <a href="index.php">Back to login</a>
    </p>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const button = input.nextElementSibling;
    if (input.type === "password") {
        input.type = "text";
        button.textContent = "Hide";
    } else {
        input.type = "password";
        button.textContent = "Show";
    }
}
</script>

</body>
</html>
