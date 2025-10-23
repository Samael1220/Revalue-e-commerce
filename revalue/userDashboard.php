<?php 
include("db.php"); 
session_start();



//address book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['clear_main'])) {
        $conn->query("UPDATE users SET address='' WHERE id=$user_id");
    }
    if (isset($_POST['clear_home'])) {
        $conn->query("UPDATE users SET address2='' WHERE id=$user_id");
    }
    if (isset($_POST['clear_work'])) {
        $conn->query("UPDATE users SET address3='' WHERE id=$user_id");
    }

    // Reload data after update
    $result = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($result);
}


// Debug: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fname'])) {
    $fname      = trim($_POST['fname'] ?? '');
    $lname      = trim($_POST['lname'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $number = trim($_POST['number'] ?? '');
    $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
    $address    = trim($_POST['address'] ?? '');
    $country    = trim($_POST['country'] ?? '');
    $full_name  = trim($fname . " " . $lname);
    $address2   = trim($_POST['address2'] ?? '');
    $address3   = trim($_POST['address3'] ?? '');


    // Validate required fields
    if (empty($fname) || empty($lname) || empty($email)) {
        $updateMsg = "⚠ Please fill in all required fields (First Name, Last Name, Email).";
    } else {
        // Check if email is already taken by another user
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE E_mail = ? AND id != ?");
        if ($checkStmt === false) {
            $updateMsg = "⚠ Check statement failed: " . $conn->error;
        } else {
            $checkStmt->bind_param("si", $email, $_SESSION['user_id']);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                $updateMsg = "⚠ This email is already taken by another user.";
            } else {
                $stmt = $conn->prepare("
                  UPDATE users SET
                      F_name=?, 
                      L_name=?, 
                      Full_name=?, 
                      E_mail=?, 
                      number=?, 
                      birth_date=?, 
                      address=?, 
                      address2=?, 
                      address3=?, 
                      country=?
                  WHERE id=?
              ");

                if ($stmt === false) {
                    $updateMsg = "⚠ Database error: " . $conn->error;
                } else {
                    $stmt->bind_param(
                    "ssssssssssi",
                    $fname,
                    $lname,
                    $full_name,
                    $email,
                    $number,
                    $birth_date,
                    $address,
                    $address2,
                    $address3,
                    $country,
                    $_SESSION['user_id']
);

                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            $updateMsg = "✅ Profile updated successfully!";
                            // Update session variables
                            $_SESSION['full_name'] = $full_name;
                            $_SESSION['email'] = $email;
                            $_SESSION['F_name'] = $fname;
                            $_SESSION['L_name'] = $lname;
                            $_SESSION['number'] = $number;
                            $_SESSION['birth_date'] = $birth_date;
                            $_SESSION['address'] = $address;
                            $_SESSION['country'] = $country;
                        } else {
                            $updateMsg = "⚠ No changes were made to your profile.";
                        }
                    } else {
                        $updateMsg = "⚠ Update failed: " . $stmt->error;
                    }
                }
                $stmt->close();
            }
            $checkStmt->close();
        }
    }
}

// Fetch updated user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Ensure $user is always an array
if (!$user) {
    $user = [
    "F_name" => "",
    "L_name" => "",
    "Full_name" => "",
    "E_mail" => "",
    "number" => "",
    "birth_date" => "",
    "address" => "",
    "address2" => "",
    "address3" => "",
    "country" => ""
];
}

// --- Dashboard Overview Queries ---
$user_id = $_SESSION['user_id'];

// Total Orders
$totalOrdersQuery = $conn->prepare("SELECT COUNT(*) AS total_orders FROM orders WHERE user_id=?");
$totalOrdersQuery->bind_param("i", $user_id);
$totalOrdersQuery->execute();
$totalOrders = $totalOrdersQuery->get_result()->fetch_assoc()['total_orders'];

// Total Spent
$totalSpentQuery = $conn->prepare("SELECT SUM(total_amount) AS total_spent FROM orders WHERE user_id=?");
$totalSpentQuery->bind_param("i", $user_id);
$totalSpentQuery->execute();
$totalSpent = $totalSpentQuery->get_result()->fetch_assoc()['total_spent'] ?? 0;

// Last Order Date
$lastOrderQuery = $conn->prepare("SELECT order_date FROM orders WHERE user_id=? ORDER BY order_date DESC LIMIT 1");
$lastOrderQuery->bind_param("i", $user_id);
$lastOrderQuery->execute();
$lastOrder = $lastOrderQuery->get_result()->fetch_assoc()['order_date'] ?? "No orders yet";

// Recent 5 Orders
$recentOrdersQuery = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY order_date DESC LIMIT 5");
$recentOrdersQuery->bind_param("i", $user_id);
$recentOrdersQuery->execute();
$recentOrders = $recentOrdersQuery->get_result();

// All Orders (for My Orders section)
$allOrdersQuery = $conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY order_date DESC");
$allOrdersQuery->bind_param("i", $user_id);
$allOrdersQuery->execute();
$allOrders = $allOrdersQuery->get_result();





?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="user.css" />
    <!-- <link rel="stylesheet" href="app.css" /> -->
    <script defer src="user.js"></script>
    
    <title>User Dashboard</title>
  </head>
  <body>
    <!-- Top Navigation -->
    <nav class="top-nav">
      <div class="nav-content">
        <div class="logo">
          <div class="logo-icon">P</div>
          <span class="sage">Re-Value.PH</span>
        </div>

        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
          ☰
        </button>

        <div class="user-menu">
          <div class="user-info">
            <div class="user-avatar"><?php
        if (isset($_SESSION['full_name'])) {
            $parts = explode(" ", $_SESSION['full_name']);
            $initials = "";
            foreach ($parts as $p) {
                $initials .= strtoupper($p[0]);
            }
            echo $initials; // e.g. DH
        } else {
            echo "G"; // Default for guest
        }
      ?>
    
      </div>
        <!-- Show full name -->
    <span class="user-name">
      <?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'Guest'; ?>
    </span>
            <!-- PHP: Replace static name with -->
            
          </div>
          <!-- PHP: Logout button will POST to logout.php -->
          <button class="btn-logout" onclick="handleLogout()">Back</button>
        </div>
      </div>
    </nav>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
      <!-- Sidebar Navigation -->
      <aside class="sidebar" id="sidebar">
        <nav>
          <ul class="sidebar-nav">
            <li class="nav-item">
              <a href="#" class="nav-link active" data-section="overview">
                <span class="nav-icon">📊</span>
                <span>Dashboard Overview</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link" data-section="orders">
                <span class="nav-icon">📦</span>
                <span>My Orders</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link" data-section="spent">
                <span class="nav-icon">🛒</span>
                <span>My Cart</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link" data-section="addresses">
                <span class="nav-icon">📍</span>
                <span>Address Book</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link" data-section="personal">
                <span class="nav-icon">👤</span>
                <span>Personal Details</span>
              </a>
            </li>
          </ul>
        </nav>
      </aside>

      <!-- Main Content Area -->
      <main class="main-content">
        <!-- Overview Section -->
        <section id="overview" class="content-section active">
          <div class="section-header">
            <h1 class="section-title">Dashboard Overview</h1>
            <p class="section-subtitle">
              Welcome back! Here's what's happening with your account.
            </p>
          </div>

          <div class="stats-grid">
            <!-- PHP: These values will be populated from database queries -->
            <!-- Example: SELECT COUNT(*) as total_orders FROM orders WHERE user_id = $_SESSION['user_id'] -->
            <div class="stat-card">
  <div class="stat-icon">📦</div>
  <div class="stat-value"><?= $totalOrders ?></div>
  <div class="stat-label">Total Orders</div>
</div>

<div class="stat-card">
  <div class="stat-icon">💵</div>
  <div class="stat-value">₱<?= number_format($totalSpent, 2) ?></div>
  <div class="stat-label">Total Spent</div>
</div>

<div class="stat-card">
  <div class="stat-icon">📅</div>
  <div class="stat-value">
    <?= ($lastOrder !== "No orders yet") ? date("M d, Y", strtotime($lastOrder)) : $lastOrder ?>
  </div>
  <div class="stat-label">Last Order</div>
</div>
          </div>

          <div class="section-header">
            <h2 class="section-title">Recent Activity</h2>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody id="recentOrdersTable">
  <?php if ($recentOrders->num_rows > 0): ?>
    <?php while ($order = $recentOrders->fetch_assoc()): ?>
      <tr>
        <td>#<?= $order['id'] ?></td>
        <td><?= date("M d, Y", strtotime($order['order_date'])) ?></td>
        <td><?= htmlspecialchars($order['status']) ?></td>
        <td>₱<?= number_format($order['total_amount'], 2) ?></td>
      </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr><td colspan="4">No recent orders.</td></tr>
  <?php endif; ?>
</tbody>
            </table>
          </div>
        </section>



<!-- Orders Section -->
<section id="orders" class="content-section">
<?php


if (!isset($_SESSION['user_id'])) {
    echo "<p class='text-red-500'>No user session found.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all orders for this user
$query = "SELECT id, total_amount, order_date, status, payment_method FROM orders WHERE user_id = $user_id ORDER BY order_date DESC";
$result = mysqli_query($conn, $query);
?>

<div class="orders-container">
  <h2>My Orders</h2>
  <p>Track and manage all your orders in one place.</p>

  <div class="orders-table-wrapper">
    <table class="orders-table">
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Total Amount (₱)</th>
          <th>Order Date</th>
          <th>Status</th>
          <th>Payment Method</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['id']); ?></td>
              <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
              <td><?php echo htmlspecialchars($row['order_date']); ?></td>
              <td>
                <span class="status-badge <?php echo ($row['status'] === 'Pending') ? 'pending' : 'completed'; ?>">
                  <?php echo htmlspecialchars($row['status']); ?>
                </span>
              </td>
              <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="no-orders">
              No orders found. Orders will be loaded from the database.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</section>


        <!-- Cart Section -->
<section id="spent" class="content-section">
  <div class="section-header">
    <h1 class="section-title">My Cart</h1>
    <p class="section-subtitle">Review your selected items before checkout.</p>
  </div>

  <form method="POST" action="checkout_review.php">
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Select</th>
            <th>Product</th>
            <th>Name</th>
            <th>Price</th>
            <th>Size</th>
            <th>Total</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $user_id = $_SESSION['user_id'];
          $cartQuery = $conn->prepare("
            SELECT c.id AS cart_id, i.name, i.size, i.price, i.image, c.quantity
            FROM cart c
            JOIN inventory i ON c.product_id = i.id
            WHERE c.user_id=?
          ");
          $cartQuery->bind_param("i", $user_id);
          $cartQuery->execute();
          $cartResult = $cartQuery->get_result();

          $grandTotal = 0;
         if ($cartResult->num_rows > 0) {
    while ($item = $cartResult->fetch_assoc()) {
        $total = $item['price'] * $item['quantity'];
        $grandTotal += $total;
        echo "<tr>
                <td><input type='checkbox' name='cart_ids[]' value='{$item['cart_id']}'></td>
                <td class='product-cell'>
                  <img src='".htmlspecialchars($item['image'])."' alt='".htmlspecialchars($item['name'])."' style='width:60px; height:60px; object-fit:cover; border-radius:5px; margin-left:0;'>
                </td>
                <td>{$item['name']}</td>
                <td>₱".number_format($item['price'],2)."</td>
                <td>".htmlspecialchars($item['size'])."</td>
                <td>₱".number_format($total,2)."</td>
                <td>
                  <a href='remove_cart.php?id={$item['cart_id']}' class='btn-delete'>Delete</a>
                </td>
              </tr>";
    }

              echo "<tr>
                      <td colspan='5' style='text-align:right;'>Grand Total:</td>
                      <td colspan='2'>₱".number_format($grandTotal,2)."</td>
                    </tr>";
          } else {
              echo "<tr><td colspan='7'>Your cart is empty.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <?php if ($cartResult->num_rows > 0): ?>
      <div style="margin-top:20px; text-align:right;">
        <button type="submit" name="review_checkout" class="btn btn-pay">Proceed to Payment</button>
      </div>
    <?php endif; ?>
</form>
</section>

<!-- ADDRESS BOOK Section -->
<section id="addresses" class="content-section">
  <div class="section-header">
    <h1 class="section-title">Address Book</h1>
    <p class="section-subtitle">
      Manage your saved shipping and billing addresses.
    </p>
  </div>

  <div style="text-align: right; margin-bottom: var(--spacing-lg)">
    <button class="btn btn-primary" onclick="showAddAddressModal()">
      + Add Address
    </button>
  </div>

  <div class="address-grid">

    <!-- MAIN ADDRESS -->
    <div class="address-card">
      <span class="address-type">Main</span>
      <div class="address-details">
        <?php echo !empty($user['address']) 
          ? htmlspecialchars($user['address']) 
          : '<i>No address set</i>'; ?>
      </div>
      <div class="address-actions">
        <form method="POST" action="">
          <button type="submit" name="clear_main" class="btn btn-danger">Remove</button>
        </form>
        <form method="GET" action="editAddress.php">
          <input type="hidden" name="type" value="main">
          <button type="submit" class="btn btn-secondary">Edit</button>
        </form>
      </div>
    </div>

    <!-- HOME ADDRESS -->
    <div class="address-card">
      <span class="address-type">Home</span>
      <div class="address-details">
        <?php echo !empty($user['address2']) 
          ? htmlspecialchars($user['address2']) 
          : '<i>No address set</i>'; ?>
      </div>
      <div class="address-actions">
        <form method="POST" action="">
          <button type="submit" name="clear_home" class="btn btn-danger">Remove</button>
        </form>
        <form method="GET" action="editAddress.php">
          <input type="hidden" name="type" value="home">
          <button type="submit" class="btn btn-secondary">Edit</button>
        </form>
      </div>
    </div>

    <!-- WORK ADDRESS -->
    <div class="address-card">
      <span class="address-type">Work</span>
      <div class="address-details">
        <?php echo !empty($user['address3']) 
          ? htmlspecialchars($user['address3']) 
          : '<i>No address set</i>'; ?>
      </div>
      <div class="address-actions">
        <form method="POST" action="">
          <button type="submit" name="clear_work" class="btn btn-danger">Remove</button>
        </form>
        <form method="GET" action="editAddress.php">
          <input type="hidden" name="type" value="work">
          <button type="submit" class="btn btn-secondary">Edit</button>
        </form>
      </div>
    </div>

  </div>
</section>



        <!-- Personal Details Section -->
<section id="personal" class="content-section">
  <div class="section-header">
    <h1 class="section-title">Personal Details</h1>
    <p class="section-subtitle">
      Update your personal information and account settings.
    </p>
  </div>

  <?php if (isset($updateMsg)) echo "<p class='alert'>$updateMsg</p>"; ?>

  <form id="personalDetailsForm" method="POST" action="userDashboard.php">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="firstName">First Name <span style="color: red;">*</span></label>
        <input
          type="text"
          id="firstName"
          name="fname"
          class="form-input"
          value="<?php echo htmlspecialchars($user['F_name']); ?>"
          required
          maxlength="255"
        />
      </div>
      <div class="form-group">
        <label class="form-label" for="lastName">Last Name <span style="color: red;">*</span></label>
        <input
          type="text"
          id="lastName"
          name="lname"
          class="form-input"
          value="<?php echo htmlspecialchars($user['L_name']); ?>"
          required
          maxlength="255"
        />
      </div>
    </div>

    <div class="form-group">
      <label class="form-label" for="email">Email Address <span style="color: red;">*</span></label>
      <input
        type="email"
        id="email"
        name="email"
        class="form-input"
        value="<?php echo htmlspecialchars($user['E_mail']); ?>"
        required
        maxlength="25"
      />
    </div>

    <div class="form-group">
      <label class="form-label" for="phone">Phone Number</label>
      <input
        type="tel"
        id="phone"
        name="number"
        class="form-input"
        value="<?php echo htmlspecialchars($user['number']); ?>"
        pattern="[0-9]*"
        title="Please enter numbers only"
      />
    </div>

    <div class="form-group">
      <label class="form-label" for="birthdate">Birth Date</label>
      <input
        type="date"
        id="birthdate"
        name="birth_date"
        class="form-input"
        value="<?php echo htmlspecialchars($user['birth_date']); ?>"
        max="<?php echo date('Y-m-d'); ?>"
      />
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="address">Address</label>
        <input
          type="text"
          id="address"
          name="address"
          class="form-input"
          value="<?php echo htmlspecialchars($user['address']); ?>"
          maxlength="255"
        />
      </div>
      <div class="form-group">
        <label class="form-label" for="country">Country</label>
        <select id="country" name="country" class="form-input">
          <option value="">Select Country</option>
          <option value="US" <?php if ($user['country']=="US") echo "selected"; ?>>United States</option>
          <option value="CA" <?php if ($user['country']=="CA") echo "selected"; ?>>Canada</option>
          <option value="UK" <?php if ($user['country']=="UK") echo "selected"; ?>>United Kingdom</option>
          <option value="AU" <?php if ($user['country']=="AU") echo "selected"; ?>>Australia</option>
          <option value="DE" <?php if ($user['country']=="DE") echo "selected"; ?>>Germany</option>
          <option value="PH" <?php if ($user['country']=="PH") echo "selected"; ?>>Philippines</option>
          <option value="JP" <?php if ($user['country']=="JP") echo "selected"; ?>>Japan</option>
          <option value="KR" <?php if ($user['country']=="KR") echo "selected"; ?>>South Korea</option>
          <option value="SG" <?php if ($user['country']=="SG") echo "selected"; ?>>Singapore</option>
          <option value="TH" <?php if ($user['country']=="TH") echo "selected"; ?>>Thailand</option>
        </select>
      </div>
    </div>

    <div
      style="
        display: flex;
        gap: var(--spacing-md);
        justify-content: flex-end;
        margin-top: var(--spacing-2xl);
      "
    >
      <button
        type="reset"
        class="btn btn-secondary"
      >
        Reset
      </button>
      <button type="submit" class="btn btn-primary">
        Save Changes
      </button>
    </div>
  </form>
</section>

      </main>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast" class="toast"></div>
  </body>
</html>
