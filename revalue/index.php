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
        $_SESSION['registerError'] = "❌ Passwords do not match!";
        header("Location: index.php");
        exit;
    } else {
        // check if email already exists
        $check = $conn->prepare("SELECT * FROM users WHERE E_mail = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $_SESSION['registerError'] = "❌ This email is already registered!";
            header("Location: index.php");
            exit;
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
                $_SESSION['registerSuccess'] = "✅ Registered successfully!";
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['registerError'] = "❌ Error: " . $stmt->error;
                header("Location: index.php");
                exit;
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
           $loginError = "Incorrect password. Please try again.";

        }
    } else {
  $loginError = "User not found!";


    }
}

// ---- Handle Logout ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Display messages
$registerError = isset($_SESSION['registerError']) ? $_SESSION['registerError'] : '';
$loginError = isset($loginError) ? $loginError : '';
$registerSuccess = isset($_SESSION['registerSuccess']) ? $_SESSION['registerSuccess'] : '';
unset($_SESSION['registerSuccess']);
unset($_SESSION['registerError']);


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
 <script src="https://unpkg.com/lucide@latest" defer></script>
    <script defer>
    document.addEventListener('DOMContentLoaded', () => {
      
      lucide.createIcons();
    });
  </script>
 <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="app.css" />
<link rel="stylesheet" href="chat.css">
<link rel="stylesheet" href="specificity.css" />
<script defer src="script.js"></script>
</head>

<body>
<div class="cont-head">
  <header class="header ps-mg">
    <div class="left">
    <h2 style="margin-bottom: 0;"><i class="fa-solid fa-leaf" style="color:darkgreen"></i></h2>
      <h3>Re-Value.PH</h3>
    </div>

    <div class="center container">
      <input type="text" placeholder="Search…" />
    </div>

    <div class="right">
     
      <button class="btn btn-outline hv" onclick="openCartModal()"><i data-lucide="shopping-cart">Cart</i></button>
      <?php if(isset($_SESSION['user_id'])): ?>
     <button class="btn btn-outline lcd hv" ><a href="userDashboard.php"><i data-lucide="user">User</i> </a></button>
      <form method="POST" style="display:inline;">
        <button class="btn btn-outline hv" type="submit" name="logout">Log out</button>
      </form>
      <?php else: ?>
      <button class="btn btn-outline hv" onclick="openModal()">My Account</button>
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
  <!-- CATEGORY FILTER -->
  <form method="GET">
    <h6>CATEGORIES</h6>
    <input type="hidden" name="size" value="<?= isset($_GET['size']) ? htmlspecialchars($_GET['size']) : '' ?>">
    <input type="hidden" name="price" value="<?= isset($_GET['price']) ? htmlspecialchars($_GET['price']) : '' ?>">

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
  </form>

  <!-- SIZE FILTER -->
  <form method="GET">
    <h6>SIZES</h6>
    <input type="hidden" name="category" value="<?= isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '' ?>">
    <input type="hidden" name="price" value="<?= isset($_GET['price']) ? htmlspecialchars($_GET['price']) : '' ?>">

    <?php 
      $sizes = ["XS","S","M","L","XL","XXL"];
      foreach($sizes as $s){
        $active = (isset($_GET['size']) && $_GET['size']==$s) ? "active" : "";
        echo "<button type='submit' name='size' value='$s' class='size-btn $active'>$s</button>";
      }
    ?>
  </form>

  <!-- PRICE FILTER -->
  <form method="GET">
    <h6>PRICES</h6>
    <input type="hidden" name="category" value="<?= isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '' ?>">
    <input type="hidden" name="size" value="<?= isset($_GET['size']) ? htmlspecialchars($_GET['size']) : '' ?>">

    <button type="submit" name="price" value="0-500" class="size-btn <?= (isset($_GET['price']) && $_GET['price']=="0-500") ? "active" : "" ?>">Under ₱500</button>
    <button type="submit" name="price" value="500-1000" class="size-btn <?= (isset($_GET['price']) && $_GET['price']=="500-1000") ? "active" : "" ?>">₱500-₱1000</button>
    <button type="submit" name="price" value="1000-2000" class="size-btn <?= (isset($_GET['price']) && $_GET['price']=="1000-2000") ? "active" : "" ?>">₱1000-₱2000</button>
    <button type="submit" name="price" value="2000+" class="size-btn <?= (isset($_GET['price']) && $_GET['price']=="2000+") ? "active" : "" ?>">Above ₱2000</button>
  </form>
</aside>


    <div class="cl-cnt">
      <h3 class="cl">Our Collection</h3>
      <h6 class="cl-des">Handpicked and made special for you</h6>
      <div class="cl-pos">
        <!-- Products Section -->
<a id="products"></a>
<div class="content-section" id="products">
  <div class="products-grid" id="products-grid">
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
      <?php else: ?>
          <button type="button" class="btn-cart" onclick="addToCart(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name']) ?>', this)">
              Add to Cart
          </button>
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
  <!-- login ata -->
<?php if (!isset($_SESSION['user_id'])): ?>
<div class="modal-overlay" id="auth-overlay">
  <div class="modal">
    <div class="first-container">
      <div class="form-content">
        <h1 class="h1-modal">Re-Value.PH</h1>
        <h2 class="h2-modal">Welcome Back!</h2>
        <p class="p-modal">Please enter your login details below</p>


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
          <a href="forgotPass.php" >Forgot your password?</a>
          <div class="register-link">
            <span>New here? </span>
            <a href="#" id="show-register-form">Create an account</a>
          </div>
        </div>

 </div>
    </div>

    <!-- Register Form Container -->
    <div class="first-container" id="register-form-container" style="display: none;">
      <div class="form-content">
        <h1 class="h1-modal">Re-Value.PH</h1>
        <h2 class="h2-modal">Create Account</h2>
        <p class="p-modal">Please fill in your details to create an account</p>


        <form class="auth-form" method="post" action="">
          <div class="input-group">
            <label class="input-label" for="full-name">Full Name</label>
            <input type="text" id="full-name" name="full-name" placeholder="Enter your full name" required />
          </div>
          <div class="input-group">
            <label class="input-label" for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required />
          </div>
          <div class="input-group">
            <label class="input-label" for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required />
          </div>
          <div class="input-group">
            <label class="input-label" for="confirm-pass">Confirm Password</label>
            <input type="password" id="confirm-pass" name="confirm-pass" placeholder="Confirm your password" required />
          </div>
          <button class="btn-form" type="submit" name="register">Create Account</button>
        </form>

        <div class="forgot-password">
          <div class="register-link">
            <span>Already have an account? </span>
            <a href="#" id="back-to-login">Sign in here</a>
          </div>
        </div>
      </div>
    </div>

    <div class="sec-container">
      <div class="decorative-circle"></div>
      <div class="decorative-circle-2"></div>
      <div class="image-placeholder">
        <img src="uploads/anthony-sebbo-Qn8VH9dE7-U-unsplash.jpg" alt="Fashion Image">
      </div>
    </div>
  </div>
</div>

<?php endif; ?>

<!-- Cart Modal -->
<?php if(isset($_SESSION['user_id'])): ?>
<div class="modal-overlay" id="cart-overlay" style="display: none;">
  <div class="cart-modal">
    <div class="cart-header">
      <h2>Shopping Cart</h2>
      <button class="close-cart-btn" onclick="closeCartModal()">
        <i data-lucide="x"></i>
      </button>
    </div>
    
    <div class="cart-content">
      <div class="cart-items" id="cart-items">
        <!-- Cart items will be loaded here -->
        <div class="cart-loading">
          <p>Loading cart items...</p>
        </div>
      </div>
      
      <div class="cart-summary">
        <div class="cart-total">
          <span>Total: ₱<span id="cart-total">0</span></span>
        </div>
        <button class="btn-checkout" onclick="proceedToCheckout()">
          Proceed to Checkout
        </button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

</main>

<div id="toast-container"></div>

<div id="toast" class="toast">
    <div class="toast-content">
        <div class="toast-icon"></div>
        <div class="toast-body">
            <div class="toast-title"></div>
            <div class="toast-message"></div>
        </div>
    </div>
    <div class="toast-progress"></div>
</div>

<script>
  // Toast notification system
  function showToast(type, title, message) {
    const toast = document.getElementById('toast');
    const toastIcon = toast.querySelector('.toast-icon');
    const toastTitle = toast.querySelector('.toast-title');
    const toastMessage = toast.querySelector('.toast-message');
    
    // Set toast content
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    // Set toast type and icon
    toast.className = `toast ${type}`;
    if (type === 'success') {
      toastIcon.textContent = '✓';
    } else if (type === 'error') {
      toastIcon.textContent = '✕';
    } else if (type === 'info') {
      toastIcon.textContent = 'ℹ';
    } else if (type === 'warning') {
      toastIcon.textContent = '⚠';
    }
    
    // Show toast
    toast.style.display = 'block';
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Auto hide after 4 seconds
    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toast.style.display = 'none', 350);
    }, 4000);
  }

  // Show toasts for PHP messages
  <?php if (!empty($loginError)): ?>
    showToast('error', 'Login Failed', '<?= addslashes($loginError) ?>');
  <?php endif; ?>

  <?php if (!empty($registerError)): ?>
    showToast('error', 'Registration Failed', '<?= addslashes($registerError) ?>');
  <?php endif; ?>

  <?php if (!empty($registerSuccess)): ?>
    showToast('success', 'Registration Successful', '<?= addslashes($registerSuccess) ?>');
  <?php endif; ?>

  // Save scroll position before reload
  window.addEventListener("beforeunload", () => {
    localStorage.setItem("scrollPosition", window.scrollY);
  });

  // Restore scroll position after reload
  window.addEventListener("load", () => {
    const scrollPosition = localStorage.getItem("scrollPosition");
    if (scrollPosition) {
      window.scrollTo(0, parseInt(scrollPosition));
      localStorage.removeItem("scrollPosition");
    }
  });

   // ✅ Handle "All" category click properly
  document.querySelectorAll('input[name="category"]').forEach(radio => {
    radio.addEventListener("change", function() {
      if (this.value === "") {
        // Clear size + price filters from URL
        const url = new URL(window.location.href);
        url.searchParams.delete("size");
        url.searchParams.delete("price");
        url.searchParams.delete("category");
        window.location.href = url.pathname; // reload page with no filters
      }
    });
  });

</script>

<?php if(isset($_SESSION['user_id'])): ?>


<?php endif; ?>

</body>
</html>
