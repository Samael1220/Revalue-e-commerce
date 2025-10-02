<?php 
include("db.php"); 
session_start();

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
    $number     = !empty($_POST['number']) ? (int)$_POST['number'] : 0;
    $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
    $address    = trim($_POST['address'] ?? '');
    $country    = trim($_POST['country'] ?? '');
    $full_name  = trim($fname . " " . $lname);


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
                        country=?
                    WHERE id=?
                ");

                if ($stmt === false) {
                    $updateMsg = "⚠ Database error: " . $conn->error;
                } else {
                    $stmt->bind_param(
                        "ssssssssi",
                        $fname,
                        $lname,
                        $full_name,
                        $email,
                        $number,
                        $birth_date,
                        $address,
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
        "country" => ""
    ];
}
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
          <button class="btn-logout" onclick="handleLogout()">Logout</button>
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
                <span class="nav-icon">💰</span>
                <span>Total Spent</span>
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
              <div class="stat-value" id="totalOrders">24</div>
              <div class="stat-label">Total Orders</div>
            </div>

            <!-- PHP: SELECT SUM(total_amount) as total_spent FROM orders WHERE user_id = $_SESSION['user_id'] -->
            <div class="stat-card">
              <div class="stat-icon">💵</div>
              <div class="stat-value" id="totalSpentOverview">$3,247.50</div>
              <div class="stat-label">Total Spent</div>
            </div>

            <!-- PHP: SELECT created_at FROM orders WHERE user_id = $_SESSION['user_id'] ORDER BY created_at DESC LIMIT 1 -->
            <div class="stat-card">
              <div class="stat-icon">📅</div>
              <div class="stat-value" id="lastOrderDate">Dec 15, 2024</div>
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
                <!-- PHP: This will be populated with recent orders -->
                <!-- Query: SELECT * FROM orders WHERE user_id = $_SESSION['user_id'] ORDER BY created_at DESC LIMIT 5 -->
              </tbody>
            </table>
          </div>
        </section>

        <!-- Orders Section -->
        <section id="orders" class="content-section">
          <div class="section-header">
            <h1 class="section-title">My Orders</h1>
            <p class="section-subtitle">
              Track and manage all your orders in one place.
            </p>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Date</th>
                  <th>Items</th>
                  <th>Status</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody id="ordersTable">
                <!-- PHP: Replace with database query -->
                <!-- SELECT * FROM orders WHERE user_id = $_SESSION['user_id'] ORDER BY created_at DESC -->
              </tbody>
            </table>
          </div>
        </section>

        <!-- Total Spent Section -->
        <section id="spent" class="content-section">
          <div class="section-header">
            <h1 class="section-title">Total Spent</h1>
            <p class="section-subtitle">
              Track your spending and set budgets for future purchases.
            </p>
          </div>

          <div class="stats-grid">
            <div class="spent-card">
              <div class="spent-amount" id="totalSpent">
                <!-- PHP: Replace with SUM(total_amount) from orders WHERE user_id = $_SESSION['user_id'] -->
                $3,247.50
              </div>
              <div class="spent-label">Lifetime Spending</div>
            </div>
          </div>

          <div class="section-header">
            <h2 class="section-title">Spending Breakdown</h2>
          </div>

          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Month</th>
                  <th>Amount</th>
                  <th>Orders</th>
                  <th>Average Order Value</th>
                </tr>
              </thead>
              <tbody id="spendingBreakdownTable">
                <!-- PHP: Replace with SELECT MONTH(created_at), SUM(total_amount), COUNT(*), AVG(total_amount) FROM orders GROUP BY MONTH(created_at) -->
              </tbody>
            </table>
          </div>
        </section>

        <!-- Address Book Section -->
        <section id="addresses" class="content-section">
          <div class="section-header">
            <h1 class="section-title">Address Book</h1>
            <p class="section-subtitle">
              Manage your saved shipping and billing addresses.
            </p>
          </div>

          <div style="text-align: right; margin-bottom: var(--spacing-lg)">
            <!-- PHP: This button will trigger modal or redirect to add_address.php -->
            <button class="btn btn-primary" onclick="showAddAddressModal()">
              + Add Address
            </button>
          </div>

          <div class="address-grid" id="addressGrid">
            <!-- PHP: Replace with database query -->
            <!-- SELECT * FROM addresses WHERE user_id = $_SESSION['user_id'] -->
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
