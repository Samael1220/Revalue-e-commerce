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


// Base query
$sql = "SELECT * FROM inventory WHERE 1";

// Apply Category
if (!empty($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $sql .= " AND category='$category'";
}

// Apply Size
if (!empty($_GET['size'])) {
    $size = $conn->real_escape_string($_GET['size']);
    $sql .= " AND size='$size'";
}

// Apply Price Range
if (!empty($_GET['price'])) {
    $price = $_GET['price'];
    if ($price == "0-500") {
        $sql .= " AND price <= 500";
    } elseif ($price == "500-1000") {
        $sql .= " AND price BETWEEN 500 AND 1000";
    } elseif ($price == "1000-2000") {
        $sql .= " AND price BETWEEN 1000 AND 2000";
    } elseif ($price == "2000+") {
        $sql .= " AND price > 2000";
    }
}

$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);


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
  <form method="GET" action="">
    <div class="flt"><h2>Filters</h2></div>

    <!-- Categories -->
<div class="categories">
  <span><h6>CATEGORIES</h6></span>
  
  <!-- All Option -->
  <label class="radio-box">
    <input type="radio" name="category" value="" <?= (!isset($_GET['category']) || $_GET['category']=="") ? "checked" : "" ?> onchange="this.form.submit()">
    <span>All</span>
  </label>

  <label class="radio-box">
    <input type="radio" name="category" value="vintage" <?= (isset($_GET['category']) && $_GET['category']=="vintage") ? "checked" : "" ?> onchange="this.form.submit()">
    <span>Vintage</span>
  </label>
  <label class="radio-box">
    <input type="radio" name="category" value="modern" <?= (isset($_GET['category']) && $_GET['category']=="modern") ? "checked" : "" ?> onchange="this.form.submit()">
    <span>Modern</span>
  </label>
  <label class="radio-box">
    <input type="radio" name="category" value="jackets" <?= (isset($_GET['category']) && $_GET['category']=="jackets") ? "checked" : "" ?> onchange="this.form.submit()">
    <span>Jackets</span>
  </label>
  <label class="radio-box">
    <input type="radio" name="category" value="coats" <?= (isset($_GET['category']) && $_GET['category']=="coats") ? "checked" : "" ?> onchange="this.form.submit()">
    <span>Coats</span>
  </label>
  <label class="radio-box">
    <input type="radio" name="category" value="pants" <?= (isset($_GET['category']) && $_GET['category']=="pants") ? "checked" : "" ?> onchange="this.form.submit()">
    <span>Pants</span>
  </label>
</div>

    <!-- Sizes -->
    <div class="sizes">
      <span><h6>SIZES</h6></span>
      <?php 
        $sizes = ["XS","S","M","L","XL","XXL"];
        foreach($sizes as $s){
          $active = (isset($_GET['size']) && $_GET['size']==$s) ? "active" : "";
          echo "<button type='submit' name='size' value='$s' class='size-btn $active' 
                  style='cursor:pointer;'>$s</button>";
        }
      ?>
    </div>

    <!-- Prices -->
    <span><h6 class="prc">PRICES</h6></span>
    <div class="prices">
      <button type="submit" name="price" value="0-500" class="size-btn">Under ₱500</button>
      <button type="submit" name="price" value="500-1000" class="size-btn">₱500-₱1000</button>
      <button type="submit" name="price" value="1000-2000" class="size-btn">₱1000-₱2000</button>
      <button type="submit" name="price" value="2000+" class="size-btn">Above ₱2000</button>
    </div>
  </form>
</aside>

    <div class="cl-cnt">
      <h3 class="cl">Our Collection</h3>
      <h6 class="cl-des">Handpicked and made special for you</h6>
      <div class="cl-pos">
        <!-- KEEP ALL ORIGINAL PRODUCT CARDS HERE -->
        <!-- Products Section -->
<div class="content-section" id="products">
  

  <div class="products-grid">
    <?php
    include("db.php");

    // Build base query
    $sql = "SELECT * FROM inventory WHERE 1";

    // Apply filters
    if (!empty($_GET['category'])) {
        $category = $conn->real_escape_string($_GET['category']);
        $sql .= " AND category='$category'";
    }
    if (!empty($_GET['size'])) {
        $size = $conn->real_escape_string($_GET['size']);
        $sql .= " AND size='$size'";
    }
    if (!empty($_GET['price'])) {
        $price = $_GET['price'];
        if ($price == "0-500") {
            $sql .= " AND price <= 500";
        } elseif ($price == "500-1000") {
            $sql .= " AND price BETWEEN 500 AND 1000";
        } elseif ($price == "1000-2000") {
            $sql .= " AND price BETWEEN 1000 AND 2000";
        } elseif ($price == "2000+") {
            $sql .= " AND price > 2000";
        }
    }

    $sql .= " ORDER BY id DESC";
    $result = $conn->query($sql);

    // Display products
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="card-cnt">
              <div class="img-container">
                <img src="<?= $row['image'] ?>" alt="<?= htmlspecialchars($row['name']) ?>">
              </div>
              <div class="img-des-container">
                <h3 class="img-des"><?= htmlspecialchars($row['name']) ?></h3>
                <div class="in">
                  <h4 class="img-size">Size: <?= htmlspecialchars($row['size']) ?></h4>
                  <span class="checker">Available</span>
                </div>
                <span class="price">₱<?= number_format($row['price']) ?></span>
                <div class="cart-container">
  <?php if (isset($_SESSION['user_id'])): ?>
      <?php
      // ✅ check if product already in cart
      $checkCart = $conn->prepare("SELECT id FROM cart WHERE user_id=? AND product_id=?");
      $checkCart->bind_param("ii", $_SESSION['user_id'], $row['id']);
      $checkCart->execute();
      $cartResult = $checkCart->get_result();
      $alreadyInCart = $cartResult->num_rows > 0;
      ?>
      
      <?php if ($alreadyInCart): ?>
          <button class="btn-cart" style="background:#ccc; cursor:not-allowed;" disabled>
              Already in Cart
          </button>
          <script>
            document.addEventListener("DOMContentLoaded", function(){
              let toast = document.getElementById("toast");
              toast.innerText = "<?= htmlspecialchars($row['name']) ?> is already in your cart!";
              toast.style.display = "block";
              setTimeout(()=> toast.style.display="none", 3000);
            });
          </script>
      <?php else: ?>
          <form action="cart.php" method="POST">
            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
            <button type="submit" class="btn-cart">Add to Cart</button>
          </form>
      <?php endif; ?>
  <?php else: ?>
      <!-- if not logged in -->
      <button class="btn-cart" onclick="openModal()">Login to Add</button>
  <?php endif; ?>
</div>
              </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No products match your filters.</p>";
    }

    $conn->close();
    ?>
  </div>
</div>



      </div>
    </div>
  </div>
</div>
  <!-- login ata -->
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

        <form  class="auth-form" method="post" action="">
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
            <a href="#" onclick="event.preventDefault(); openRegisterModal()">Create an account</a>
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
  const register = document.querySelector('.register-container');
  const login = document.querySelector('#auth-overlay > .modal .form-content > form.auth-form'); // only the login form

  if (register) register.style.display = 'block';
  if (login) login.style.display = 'none';
}

function closeRegisterModal() {
  const register = document.querySelector('.register-container');
  const login = document.querySelector('#auth-overlay > .modal .form-content > form.auth-form'); // only the login form

  if (register) register.style.display = 'none';
  if (login) login.style.display = 'block';
}
</script>
<div id="toast" class="toast"></div>
</body>
</html>
