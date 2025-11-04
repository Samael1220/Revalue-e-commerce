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
        header("Location: store.php");
        exit;
    } else {
        // check if email already exists
        $check = $conn->prepare("SELECT * FROM users WHERE E_mail = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $_SESSION['registerError'] = "❌ This email is already registered!";
            header("Location: store.php");
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
                header("Location: store.php");
                exit;
            } else {
                $_SESSION['registerError'] = "❌ Error: " . $stmt->error;
                header("Location: store.php");
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
                header("Location: store.php");
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
    header("Location: store.php");
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

// ---- Handle Reset Filters ----
if (isset($_GET['reset'])) {
    header("Location: store.php");
    exit;
}

$sql .= " ORDER BY id DESC";
$result = $conn->query($sql);


?>












<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>RE-VALUE.PH</title>
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
    <h2 style="margin-bottom: 0;"></h2>
    <img src="uploads/logo.webp" alt="Logo">
      <h3 class="ht">RE-VALUE.PH</h3>
    </div>

    

    <div class="right">
     
      <button class="btn btn-outline hv bt" onclick="openCartModal()"><i data-lucide="shopping-cart">Cart</i></button>
      <?php if(isset($_SESSION['user_id'])): ?>
     <button class="btn btn-outline lcd hv bt" ><a href="userDashboard.php"><i data-lucide="user">User</i> </a></button>
      <form method="POST" style="display:inline;">
        <button class="btn btn-outline hv bt" type="submit" name="logout">Log out</button>
      </form>
      <?php else: ?>
        <!-- if walang account eto mag ddisplay -->
      <button class="btn btn-outline hv bt" onclick="openModal()">My Account</button>
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
      <button onclick="window.open('index.html', '_blank')" class="bt">Shop Collection</button>


      <button onclick="window.open('story.html','_blank' )" class="btn btn-outline bt" >Learn Our Story</button>
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

  <!-- RESET FILTERS BUTTON -->
<form method="GET">
  <button type="submit" name="reset" value="1" class="size-btn bt" style="background:#ccc; margin-top:10px; width:95%;">
    Reset Filter
  </button>
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
        <h1 class="h1-modal">RE-VALUE.PH</h1>
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
        <h1 class="h1-modal">RE-VALUE.PH</h1>
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

<?php if(isset($_SESSION['user_id'])): ?>
<!-- Floating Chat Button + Widget for messaging Admin -->
<style>
.chat-fab{position:fixed;right:20px;bottom:20px;z-index:9999;width:56px;height:56px;border-radius:50%;background:#15803d;color:#fff;border:none;box-shadow:0 8px 20px rgba(0,0,0,.2);cursor:pointer;transition:transform .15s ease,box-shadow .15s ease}
.chat-fab:hover{background:#166534;transform:translateY(-1px);box-shadow:0 12px 24px rgba(0,0,0,.24)}
.chat-widget{position:fixed;right:20px;bottom:88px;width:320px;max-height:60vh;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.15);display:flex;flex-direction:column;overflow:hidden;z-index:9999;opacity:0;transform:translateY(8px);visibility:hidden;pointer-events:none;transition:opacity .18s ease,transform .18s ease,visibility .18s ease}
.chat-widget.open{opacity:1;transform:translateY(0);visibility:visible;pointer-events:auto}
.chat-header{padding:12px 14px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;background:#f8fafc}
.chat-messages{padding:12px;height:300px;overflow:auto;background:#f8fafc}
.msg{max-width:75%;margin:6px 0;padding:8px 10px;border-radius:10px;font-size:14px;line-height:1.3}
.msg.sent{margin-left:auto;background:#dcfce7;color:#065f46}
.msg.received{margin-right:auto;background:#fff;border:1px solid #e5e7eb;color:#111827}
.chat-input{display:flex;gap:6px;padding:10px;border-top:1px solid #e5e7eb;background:#fff}
.chat-input input{flex:1;padding:10px 12px;border:1px solid #e5e7eb;border-radius:8px;transition:border-color .15s ease,box-shadow .15s ease}
.chat-input input:focus{outline:none;border-color:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.15)}
.chat-input button{padding:10px 12px;background:#16a34a;color:#fff;border:none;border-radius:8px;cursor:pointer;transition:transform .15s ease,box-shadow .15s ease}
.chat-input button:hover{transform:translateY(-1px);box-shadow:0 8px 16px rgba(22,163,74,.35)}
</style>

<button class="chat-fab" id="open-user-chat" aria-label="Chat"><i class="fa-solid fa-message"></i></button>
<div class="chat-widget" id="user-chat">
  <div class="chat-header">
    <strong>Message Admin</strong>
    <button id="close-user-chat" style="border:none;background:none;cursor:pointer">✕</button>
  </div>
  <div class="chat-messages" id="user-chat-box"></div>
  <form class="chat-input" id="user-chat-form">
    <input type="text" id="user-chat-input" placeholder="Type a message…" autocomplete="off" />
    <button type="submit">Send</button>
  </form>
</div>
<?php endif; ?>

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

  <?php if(isset($_SESSION['user_id'])): ?>
  // User chat widget (peer-to-admin)
  (function(){
    const openBtn = document.getElementById('open-user-chat');
    const closeBtn = document.getElementById('close-user-chat');
    const widget = document.getElementById('user-chat');
    const form = document.getElementById('user-chat-form');
    const input = document.getElementById('user-chat-input');
    const box = document.getElementById('user-chat-box');
    const sendBtn = form ? form.querySelector('button[type="submit"]') : null;
    let lastId = 0;

    function appendMessage(m){
      const div = document.createElement('div');
      div.className = 'msg ' + (m.is_self ? 'sent' : 'received');
      div.textContent = m.body;
      box.appendChild(div);
      box.scrollTop = box.scrollHeight;
    }

    function fetchMessages(){
      fetch('chat_fetch.php?since=' + lastId)
        .then(r=>r.json())
        .then(data=>{
          if(data.success && data.messages){
            data.messages.forEach(m=>{
              appendMessage({ body: m.body, is_self: (m.sender_id == <?php echo (int)($_SESSION['user_id'] ?? 0); ?>) });
              lastId = Math.max(lastId, parseInt(m.id));
            });
          }
        });
    }

    function openWidget(){
      if(!widget.classList.contains('open')){
        widget.classList.add('open');
        setTimeout(()=>input && input.focus(), 60);
        fetchMessages();
      }
    }
    function closeWidget(){ widget.classList.remove('open'); }
    openBtn && openBtn.addEventListener('click', ()=>{
      if(widget.classList.contains('open')){ closeWidget(); } else { openWidget(); }
    });
    closeBtn && closeBtn.addEventListener('click', closeWidget);

    form && form.addEventListener('submit', function(e){
      e.preventDefault();
      const v = input.value.trim();
      if(!v) return;
      // send to server, then refresh from backend to avoid duplicates
      input.value = '';
      sendBtn && (sendBtn.disabled = true);
      const fd = new FormData();
      fd.append('message', v);
      fetch('chat_send.php', { method:'POST', body: fd })
        .then(r=>r.json())
        .then(data=>{ 
          if(!data.success){
            appendMessage({ body: 'Message failed: ' + (data.error || 'server'), is_self: false });
          }
          fetchMessages(); 
        })
        .catch(()=>{
          appendMessage({ body: 'Message failed: network error', is_self: false });
        })
        .finally(()=>{ sendBtn && (sendBtn.disabled = false); });
    });

    setInterval(fetchMessages, 3000);
  })();
  <?php endif; ?>

</script>

<?php if(isset($_SESSION['user_id'])): ?>


<?php endif; ?>

</body>
</html>
