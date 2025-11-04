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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="app.css">
<link rel="stylesheet" href="specificity.css">
<link rel="stylesheet" href="user.css">
<link rel="stylesheet" href="forgotpass.css">
</head>
<body>

<div class="fp-page">
  <div class="fp-card">
   
    <div class="fp-body">
      <h2 class="fp-title">Reset Password</h2>
      <div class="fp-subtitle">Enter your email and a new password.</div>

      <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
      <?php endif; ?>

      <form class="auth-form" method="POST" action="">
        <div class="input-group">
          <label class="input-label" for="email">Email</label>
          <input type="email" name="email" id="email" placeholder="Enter your email" required>
        </div>

        <div class="input-group">
          <label class="input-label" for="new_password">New Password</label>
          <div class="password-wrapper">
            <input type="password" name="new_password" id="new_password" placeholder="Create a new password" required>
            <button type="button" class="toggle-password" onclick="togglePassword('new_password')">Show</button>
          </div>
        </div>

        <div class="input-group">
          <label class="input-label" for="confirm_password">Confirm Password</label>
          <div class="password-wrapper">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">Show</button>
          </div>
        </div>

        <button class="btn-form" type="submit">Reset Password</button>
      </form>

      <div class="fp-actions">
        <a href="store.php" class="fp-muted">Back to login</a>
      </div>
    </div>
  </div>
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
