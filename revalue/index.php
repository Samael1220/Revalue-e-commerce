<?php
include("db.php");
session_start();

// ---- Handle Register ----
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

            $stmt = $conn->prepare("INSERT INTO users (Full_name, E_mail, Pass) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashedPassword);

            if ($stmt->execute()) {
                $_SESSION['registerSuccess'] = "✅ Registered successfully! You can now log in.";
                header("Location: index.php");
                exit;
            } else {
                $registerError = "❌ Error: " . $stmt->error;
            }
        }
    }
}

// ---- Handle Login ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['username']); // form field "username" actually contains email
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE E_mail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['Pass'])) {
            // ✅ Store session data
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['email']     = $row['E_mail'];
            $_SESSION['full_name'] = $row['Full_name'];
            $_SESSION['F_name']    = $row['F_name'];
            $_SESSION['L_name']    = $row['L_name'];
            $_SESSION['number']    = $row['number'];
            $_SESSION['birth_date']= $row['birth_date'];
            $_SESSION['address']   = $row['address'];
            $_SESSION['country']   = $row['country'];

            // ✅ Check if admin
            if ($row['E_mail'] === "admin@revalue.com") {
                header("Location: admin.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            $loginError = "❌ Wrong password!";
        }
    } else {
        $loginError = "❌ User not found!";
    }
}

// ---- Handle Logout ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Display messages
$registerError = isset($registerError) ? $registerError : '';
$loginError = isset($loginError) ? $loginError : '';
$registerSuccess = isset($_SESSION['registerSuccess']) ? $_SESSION['registerSuccess'] : '';
unset($_SESSION['registerSuccess']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Re-Value.PH</title>
<link rel="stylesheet" href="app.css" />
<link rel="stylesheet" href="specificity.css" />
<script defer src="script.js"></script>
</head>
<body>
<div class="cont-head">
  <header class="header ps-mg">
    <div class="left">
      <h6>Logo</h6>
      <h3>Re-Value.PH</h3>
    </div>

    <div class="center container">
      <input type="text" placeholder="Search…" />
    </div>

    <div class="right">
      <button class="btn btn-outline">Categories</button>
      <button class="btn btn-outline">Add to Cart</button>
      <?php if(isset($_SESSION['user_id'])): ?>
      <button class="btn btn-outline"><a href="userDashboard.php">My Account</a></button>
      <form method="POST" style="display:inline;">
        <button class="btn btn-outline" type="submit" name="logout">Log out</button>
      </form>
      <?php else: ?>
      <button class="btn btn-outline" onclick="openModal()">My Account</button>
      <?php endif; ?>
    </div>
  </header>
  <hr />
</div>

<main>
<div class="sec-intro">
  <div class="cnt">
    <h1 class="mg">Sustainable Style,</h1>
    <h1 class="text-sage mg">Timeless Fashion</h1>
    <h4 class="text-muted">
      Discover unique vintage pieces that tell a story. Every purchase
      helps reduce <br />textile waste and supports a more sustainable
      future.
    </h4>
    <div class="btn-container">
      <button>Shop Collection</button>
      <button class="btn btn-outline">Learn Our Story</button>
    </div>
  </div>
</div>

<div class="bd-container">
  <div class="bd-child-container ps-mg">
    <aside>
      <div class="flt"><h2>Filters</h2></div>
      <div class="categories">
        <span><h6>CATEGORIES</h6></span>
        <label class="radio-box">
          <input type="radio" name="category" value="vintage" />
          <span>Vintage Clothing</span>
        </label>
        <label class="radio-box">
          <input type="radio" name="category" value="modern" />
          <span>Modern Clothing</span>
        </label>
        <label class="radio-box">
          <input type="radio" name="category" value="shoes" />
          <span>Shoes & Accessories</span>
        </label>
        <label class="radio-box">
          <input type="radio" name="category" value="bag" />
          <span>Bags & Purses</span>
        </label>
        <label class="radio-box">
          <input type="radio" name="category" value="jackets" />
          <span>Jackets & Coats</span>
        </label>
      </div>

      <div class="sizes">
        <span><h6>SIZES</h6></span>
        <button class="size-btn">XS</button>
        <button class="size-btn">S</button>
        <button class="size-btn">M</button>
        <button class="size-btn">L</button>
        <button class="size-btn">XL</button>
        <button class="size-btn">XXL</button>
      </div>

      <span><h6 class="prc">PRICES</h6></span>
      <div class="prices">
        <button class="size-btn">Under ₱500</button>
        <button class="size-btn">₱500-₱1000</button>
        <button class="size-btn">₱1000-₱2000</button>
        <button class="size-btn">Above ₱2000</button>
      </div>
    </aside>

    <div class="cl-cnt">
      <h3 class="cl">Our Collection</h3>
      <h6 class="cl-des">Handpicked and made special for you</h6>
      <div class="cl-pos">
        <!-- KEEP ALL ORIGINAL PRODUCT CARDS HERE -->
        <?php for($i=0;$i<8;$i++): ?>
        <div class="card-cnt">
          <div class="img-container"></div>
          <div class="img-des-container">
            <h3 class="img-des">Vintage Denim Jacket</h3>
            <div class="in">
              <h4 class="img-size">Size: M</h4>
              <span class="checker">Available</span>
            </div>
            <span class="price text-sage text-xl">₱2000</span>
            <div class="cart-container">
              <button class="btn-cart">Add to Cart</button>
            </div>
          </div>
        </div>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</div>

<?php if (!isset($_SESSION['user_id'])): ?>
<div class="modal-overlay" id="auth-overlay">
  <div class="modal">
    <div class="first-container">
      <div class="form-content">
        <h1 class="h1-modal">Re-Value.PH</h1>
        <h2 class="h2-modal">Welcome Back!</h2>
        <p class="p-modal">Please enter your login details below</p>

        <?php if (!empty($loginError)): ?>
          <div class="alert-error"><?php echo htmlspecialchars($loginError); ?></div>
        <?php endif; ?>

        <?php if (!empty($registerSuccess)): ?>
          <div class="alert-success"><?php echo htmlspecialchars($registerSuccess); ?></div>
        <?php endif; ?>

        <form class="auth-form" method="post" action="">
          <div class="input-group">
            <label class="input-label" for="username">Email</label>
            <input type="email" id="username" name="username" placeholder="Enter your email" required />
          </div>
          <div class="input-group">
            <label class="input-label" for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required />
          </div>
          <button class="btn-form" type="submit" name="login">Sign In</button>
        </form>

        <div class="forgot-password">
          <a href="#" onclick="alert('Forgot password clicked!')">Forgot your password?</a>
          <div class="register-link">
            <span>New here? </span>
            <a href="#" onclick="openRegisterModal()">Create an account</a>
          </div>
        </div>

        <!-- Register container -->
        <div class="register-container" style="display: none;">
          <h1 class="h1-modal">Re-Value.PH</h1>
          <h2 class="h2-modal">Create Account</h2>
          <p class="p-modal">Join our community of sustainable fashion lovers</p>

          <?php if (!empty($registerError)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($registerError); ?></div>
          <?php endif; ?>

          <form class="auth-form" method="post" action="">
            <div class="input-group">
              <label class="input-label" for="full-name">Full name</label>
              <input type="text" id="full-name" name="full-name" placeholder="Enter your full name" required />
            </div>
            <div class="input-group">
              <label class="input-label" for="email">Email</label>
              <input type="email" id="email" name="email" placeholder="Enter your email" required />
            </div>
            <div class="input-group">
              <label class="input-label" for="reg-password">Password</label>
              <input type="password" id="reg-password" name="password" placeholder="Create a password" required />
            </div>
            <div class="input-group">
              <label class="input-label" for="reg-confirm">Confirm password</label>
              <input type="password" id="reg-confirm" name="confirm-pass" placeholder="Confirm your password" required />
            </div>
            <button class="btn-form" type="submit" name="register">Create account</button>
          </form>

          <div class="forgot-password">
            <div class="register-link">
              <span>Already have an account? </span>
              <a href="#" onclick="closeRegisterModal(); openModal();">Sign in</a>
            </div>
          </div>
        </div>
 </div>
    </div>

    <div class="sec-container">
      <div class="decorative-circle"></div>
      <div class="decorative-circle-2"></div>
      <div class="image-placeholder">Your Image Goes Here</div>
    </div>
  </div>
</div>
<?php endif; ?>
</main>

<script>
function openModal() {
  document.getElementById('auth-overlay').style.display = 'block';
}

function closeModal() {
  document.getElementById('auth-overlay').style.display = 'none';
}

function openRegisterModal() {
  document.querySelector('.register-container').style.display = 'block';
  document.querySelector('.auth-form').style.display = 'none';
}

function closeRegisterModal() {
  document.querySelector('.register-container').style.display = 'none';
  document.querySelector('.auth-form').style.display = 'block';
}
</script>

</body>
</html>
