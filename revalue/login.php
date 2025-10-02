<?php include("db.php"); session_start(); ?>

<!DOCTYPE html>
<html>


<h2>Login</h2>
<form method="post" action="">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit" name="login">Login</button>
</form>

<?php
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Verify password
        //var_dump($password, $row['password']);
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['user_name'];
            header("Location: index.html");
            exit;
        } else {
            echo "❌ Wrong password!";
        }
    } else {
        echo "❌ User not found!";
    }
}
?>


</html>