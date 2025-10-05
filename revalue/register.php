<?php 
include("db.php");
session_start();

// Handle Register
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $fullname = trim($_POST['full-name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm-pass'];

    if ($password !== $confirm) {
        $registerError = "❌ Passwords do not match!";
    } else {
        // check if email already exists
        $check = $conn->prepare("SELECT * FROM users WHERE E_mail = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $registerError = "❌ This email is already registered!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Provide default values for required fields
            $fname = explode(' ', $fullname)[0]; // First name
            $lname = count(explode(' ', $fullname)) > 1 ? implode(' ', array_slice(explode(' ', $fullname), 1)) : ''; // Last name
            $number = '0'; // Default phone number
            $address = 'Not provided'; // Default address
            $country = 'Philippines'; // Default country
            
            $stmt = $conn->prepare("INSERT INTO users (Full_name, E_mail, Pass, F_name, L_name, number, address, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $fullname, $email, $hashedPassword, $fname, $lname, $number, $address, $country);

            if ($stmt->execute()) {
                $registerSuccess = "✅ Registered successfully! <a href='index.php'>Login here</a>";
            } else {
                $registerError = "❌ Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<?php include("templates/header.php"); ?>

<h2>Register</h2>

<?php if (!empty($registerError)): ?>
    <div style="color: red; margin: 10px 0;"><?php echo htmlspecialchars($registerError); ?></div>
<?php endif; ?>

<?php if (!empty($registerSuccess)): ?>
    <div style="color: green; margin: 10px 0;"><?php echo $registerSuccess; ?></div>
<?php endif; ?>

<form method="post" action="">
    <div style="margin: 10px 0;">
        <label for="full-name">Full Name:</label><br>
        <input type="text" id="full-name" name="full-name" placeholder="Enter your full name" required style="width: 300px; padding: 8px;">
    </div>
    
    <div style="margin: 10px 0;">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" placeholder="Enter your email" required style="width: 300px; padding: 8px;">
    </div>
    
    <div style="margin: 10px 0;">
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" placeholder="Create a password" required style="width: 300px; padding: 8px;">
    </div>
    
    <div style="margin: 10px 0;">
        <label for="confirm-pass">Confirm Password:</label><br>
        <input type="password" id="confirm-pass" name="confirm-pass" placeholder="Confirm your password" required style="width: 300px; padding: 8px;">
    </div>
    
    <div style="margin: 10px 0;">
        <button type="submit" name="register" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">Register</button>
    </div>
</form>

<div style="margin: 20px 0;">
    <a href="index.php">← Back to Login</a>
</div>

<?php include("templates/footer.php"); ?>
</html>