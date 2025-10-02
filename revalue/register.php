<?php include("db.php"); ?>

<!DOCTYPE html>
<html>
<?php include("templates/header.php"); ?>

<h2>Register</h2>
<form method="post" action="">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit" name="register">Register</button>
</form>

<?php
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO log (user_name, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);

    if ($stmt->execute()) {
        echo "✅ Registered successfully! <a href='login.php'>Login here</a>";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
}
?>

<?php include("templates/footer.php"); ?>
</html>