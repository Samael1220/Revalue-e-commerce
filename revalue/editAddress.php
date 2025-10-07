<?php
include("db.php");
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate the type parameter (main, home, or work)
$valid_types = ['main', 'home', 'work'];
$type = $_GET['type'] ?? '';
if (!in_array($type, $valid_types)) {
    die("Invalid address type.");
}

// Map address type to database column
$column = match ($type) {
    'main' => 'address',
    'home' => 'address2',
    'work' => 'address3',
};

// Fetch current address value
$result = mysqli_query($conn, "SELECT $column FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($result);
$current_address = $user[$column] ?? "";

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_address'])) {
    $new_address = trim($_POST['new_address']);

    $stmt = $conn->prepare("UPDATE users SET $column=? WHERE id=?");
    $stmt->bind_param("si", $new_address, $user_id);
    $stmt->execute();

    // Redirect back to dashboard after saving
    header("Location: userDashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Address</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #8f2222ff;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .edit-container {
      background: #5a0303ff;
      padding: 30px;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      text-transform: capitalize;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    input[type="text"] {
      padding: 10px;
      border: none;
      border-radius: 8px;
      width: 100%;
    }

    .buttons {
      display: flex;
      justify-content: space-between;
    }

    button {
      padding: 10px 15px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      color: white;
    }

    .save-btn {
      background-color: #da4545ff;
    }

    .cancel-btn {
      background-color: crimson;
      text-decoration: none;
      display: inline-block;
      line-height: 36px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="edit-container">
    <h2>Edit <?php echo htmlspecialchars($type); ?> Address</h2>
    <form method="POST">
      <input type="text" name="new_address" 
             value="<?php echo htmlspecialchars($current_address); ?>" 
             placeholder="Enter new address" required>

      <div class="buttons">
        <button type="submit" name="update_address" class="save-btn">💾 Save</button>
        <a href="userDashboard.php" class="cancel-btn">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
