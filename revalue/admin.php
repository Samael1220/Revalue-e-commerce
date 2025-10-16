<?php
include("db.php"); // your DB connection

if (isset($_POST['submit'])) {
    // Collect form data
    $name     = $_POST['name'];
    $size     = $_POST['size'];
    $category = $_POST['category'];
    $price    = $_POST['price'];

    // Handle file upload
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // create folder if it doesn't exist
    }

    $fileName = time() . "_" . basename($_FILES["image"]["name"]); // unique name
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        // Insert product into DB
        $stmt = $conn->prepare("INSERT INTO inventory (name, image, category, size, price) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $name, $targetFilePath, $category, $size, $price);

        if ($stmt->execute()) {
            echo "✅ Product added successfully!";
        } else {
            echo "❌ Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❌ Failed to upload image.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="styleForAdmin.css" />
        <script defer src="admin.js"></script>
           <link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
/>
        <title>Admin Dashboard</title>
    </head>
    <body>
        <!-- Sidebar -->
        <div class="sidebar">
           <div class="logo"> <i class="fa-solid fa-leaf" style="color:darkgreen"></i></div>

            <div class="nav-item active" data-section="overview">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"
                    />
                </svg>
                
            </div>

            <div class="nav-item" data-section="orders">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"
                    />
                </svg>
               
            </div>

            <div class="nav-item" data-section="analytics">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M3 3v18h18V3H3zm6 14H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"
                    />
                </svg>
                
            </div>

            <div class="nav-item" data-section="products">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 2l-5.5 9h11L12 2zm0 3.84L13.93 9h-3.87L12 5.84zM17.5 13c-2.49 0-4.5 2.01-4.5 4.5s2.01 4.5 4.5 4.5 4.5-2.01 4.5-4.5-2.01-4.5-4.5-4.5zm0 7c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5zM3 21.5h8v-8H3v8zm2-6h4v4H5v-4z"
                    />
                </svg>
               
            </div>

            <div class="nav-item" data-section="notifications">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"
                    />
                </svg>
               
            </div>

            <div class="nav-item settings-icon" data-section="settings">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"
                    />
                </svg>
                
            </div>

            <div class="nav-item logout-icon" data-section="logout">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"
                    />
                </svg>
                
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-section">
                    <div class="user-avatar">ED</div>
                    <div class="welcome-text">
                        <small>Hi, Eitan Dwane!</small>
                        <h2>Welcome Back!</h2>
                    </div>
                </div>
                <div class="header-actions">
                    <div class="search-bar">
                        <input type="text" placeholder="Search..." />
                        <svg
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </div>
                    <div class="header-icons">
                        <button class="icon-btn">
                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path d="M7 10l5 5 5-5z" />
                            </svg>
                        </button>
                        <button class="icon-btn">
                            <svg
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"
                                />
                            </svg>
                            <span class="notification-badge"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Overview Section -->
<div class="content-section active" id="overview">
    <?php
    include('db.php');

    // --- Get summary counts ---
    $completedOrders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='Completed'")->fetch_assoc()['total'];
    $pendingOrders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='Pending'")->fetch_assoc()['total'];
    $totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];

    // --- Fetch last 25 orders ---
    $orders = $conn->query("
        SELECT o.*, u.Full_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.id DESC
        LIMIT 25
    ");
    ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green">✓</div>
            <div class="stat-info">
                <div class="stat-label">Orders Completed</div>
                <div class="stat-value"><?= $completedOrders ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">⏳</div>
            <div class="stat-info">
                <div class="stat-label">Orders Pending</div>
                <div class="stat-value"><?= $pendingOrders ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">👤</div>
            <div class="stat-info">
                <div class="stat-label">Total Users</div>
                <div class="stat-value"><?= $totalUsers ?></div>
            </div>
        </div>
    </div>

    <!-- Order Stats -->
    <div class="order-stats">
        <div class="order-stats-header">
            <div>
                <span class="order-stats-title">Order Stats</span>
                <span class="orders-ready"><?= $orders->num_rows ?> Recent Orders Displayed</span>
            </div>
        </div>

        <table class="orders-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Full_name']) ?></td>
                        <td>₱<?= number_format($row['total_amount'], 2) ?></td>
                        <td>
                            <span class="status-badge <?= $row['status'] === 'Completed' ? 'paid' : 'pending' ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td><?= date('Y-m-d', strtotime($row['order_date'])) ?></td>
                        <td><?= date('H:i', strtotime($row['order_date'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- Orders Section -->
<div class="content-section" id="orders">
    <h2>Orders</h2>

    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Products</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include('db.php');

            // Fetch all orders with user info
            $query = "
                SELECT 
                    o.id AS order_id,
                    o.total_amount,
                    o.status,
                    o.order_date,
                    o.product_names,
                    o.product_images,
                    u.Full_name AS user_name,
                    u.E_mail AS user_email,
                    u.address AS user_address
                FROM orders o
                JOIN users u ON o.user_id = u.id
                ORDER BY o.order_date DESC
            ";

            $orders = $conn->query($query);

            if ($orders && $orders->num_rows > 0) {
                while ($order = $orders->fetch_assoc()) {
                    $statusClass = strtolower($order['status']) === 'completed' ? 'status-completed' : 'status-pending';
                    $statusText = htmlspecialchars($order['status']);

                    // Decode product names and images
                    $names = json_decode($order['product_names'], true);
                    $images = json_decode($order['product_images'], true);
                    $productHTML = '';

                    if (!empty($names) && !empty($images)) {
                        foreach ($names as $idx => $name) {
                            $img = isset($images[$idx]) ? $images[$idx] : '';
                            $productHTML .= "<div class='order-product' style='margin-bottom:5px;'>";
                            $productHTML .= "<img src='" . htmlspecialchars($img) . "' alt='" . htmlspecialchars($name) . "' style='width:50px;height:50px;object-fit:cover;margin-right:5px;'>";
                            $productHTML .= "<span>" . htmlspecialchars($name) . "</span>";
                            $productHTML .= "</div>";
                        }
                    }

                    echo "
                    <tr>
                        <td>{$order['order_id']}</td>
                        <td>" . htmlspecialchars($order['user_name']) . "</td>
                        <td>" . htmlspecialchars($order['user_email']) . "</td>
                        <td>" . htmlspecialchars($order['user_address']) . "</td>
                        <td>$productHTML</td>
                        <td>₱" . number_format($order['total_amount'], 2) . "</td>
                        <td><span class='status-badge $statusClass' data-id='{$order['order_id']}'>$statusText</span></td>
                        <td>" . htmlspecialchars($order['order_date']) . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No orders found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- JavaScript to handle status click -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".status-badge.status-pending").forEach(badge => {
        badge.addEventListener("click", function() {
            const orderId = this.getAttribute("data-id");
            const badgeElement = this;

            if (confirm("Are you sure you want to mark this order as Completed?")) {
                fetch("update_order_status.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "order_id=" + encodeURIComponent(orderId)
                })
                .then(res => res.text())
                .then(response => {
                    if (response.trim() === "success") {
                        badgeElement.textContent = "Completed";
                        badgeElement.classList.remove("status-pending");
                        badgeElement.classList.add("status-completed");
                    } else {
                        alert("Error updating order status.");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Error connecting to server.");
                });
            }
        });
    });
});
</script>





            <!-- Messaging Section (replaces Analytics) -->
            <div class="content-section" id="analytics">
                <link rel="stylesheet" href="chat.css" />
                <div class="admin-messaging">
                    <div class="admin-user-list">
                        <div class="admin-user-list-header">Users</div>
                        <div class="admin-user-items" id="admin-user-items"></div>
                    </div>
                    <div class="admin-chat">
                        <div class="admin-chat-header" id="admin-chat-header">Select a user</div>
                        <div class="admin-chat-box" id="admin-chat-box"></div>
                        <form class="admin-chat-form" id="admin-chat-form">
                            <input type="text" id="admin-chat-input" placeholder="Type a message..." autocomplete="off" />
                            <button type="submit">Send</button>
                        </form>
                    </div>
                </div>
                <script>
                (function(){
                  const userList = document.getElementById('admin-user-items');
                  const chatBox = document.getElementById('admin-chat-box');
                  const chatHeader = document.getElementById('admin-chat-header');
                  const form = document.getElementById('admin-chat-form');
                  const input = document.getElementById('admin-chat-input');
                  let activeUserId = 0;
                  let lastId = 0;

                  function appendMessage(msg){
                    const div = document.createElement('div');
                    // sender comparison will be done server-side by ids
                    div.className = 'message ' + (msg.sender_id == activeUserId ? 'received' : 'sent');
                    div.textContent = msg.body;
                    chatBox.appendChild(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                  }

                  function loadUsers(){
                    fetch('chat_users.php').then(r=>r.json()).then(data=>{
                      if(data.success){
                        userList.innerHTML = '';
                        data.users.forEach(u=>{
                          const item = document.createElement('div');
                          item.className = 'admin-user-item';
                          item.textContent = u.name || u.email;
                          item.addEventListener('click', ()=>{
                            activeUserId = u.id;
                            lastId = 0;
                            chatHeader.textContent = 'Chat with ' + (u.name || u.email);
                            chatBox.innerHTML='';
                            fetchMessages();
                          });
                          userList.appendChild(item);
                        });
                      }
                    });
                  }

                  function fetchMessages(){
                    if(!activeUserId) return;
                    fetch('chat_fetch.php?partner=' + activeUserId + '&since=' + lastId)
                      .then(r=>r.json())
                      .then(data=>{
                        if(data.success && data.messages){
                          data.messages.forEach(m=>{ appendMessage(m); lastId = Math.max(lastId, parseInt(m.id)); });
                        }
                      });
                  }

                  form.addEventListener('submit', function(e){
                    e.preventDefault();
                    if(!activeUserId) return;
                    const v = input.value.trim();
                    if(!v) return;
                    const fd = new FormData();
                    fd.append('message', v);
                    fd.append('to', activeUserId);
                    fetch('chat_send.php', { method:'POST', body: fd })
                      .then(r=>r.json())
                      .then(data=>{ if(data.success){ input.value=''; fetchMessages(); } });
                  });

                  loadUsers();
                  setInterval(()=>{ fetchMessages(); }, 3000);
                })();
                </script>
            </div>

            <!-- Products Section -->
<!-- Products Section -->
<div class="content-section" id="products">
  <h2>Products</h2>

  <?php
  include("db.php");

  // Fetch products from DB
  $result = $conn->query("SELECT * FROM inventory ORDER BY id DESC");

  if ($result->num_rows > 0) {
      echo "<div style='display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;'>";

      while ($row = $result->fetch_assoc()) {
          echo "<div style='border:1px solid #ccc; padding:10px; text-align:center; border-radius:10px;'>";

          // Image
          echo "<img src='" . $row['image'] . "' alt='" . htmlspecialchars($row['name']) . "' style='width:150px; height:auto;'><br>";

          // Product Info
          echo "<strong>" . htmlspecialchars($row['name']) . "</strong><br>";
          echo "Category: " . htmlspecialchars($row['category']) . "<br>";
          echo "Size: " . htmlspecialchars($row['size']) . "<br>";
          echo "Price: ₱" . number_format($row['price']) . "<br><br>";

          // Action Buttons
          echo "<a href='editProduct.php?id=" . $row['id'] . "'>
                  <button style='margin:5px; padding:5px 10px;'>✏ Edit</button>
                </a>";
          echo "<a href='deleteProduct.php?id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this product?');\">
                  <button style='margin:5px; padding:5px 10px; background:red; color:white;'>🗑 Delete</button>
                </a>";

          echo "</div>";
      }

      echo "</div>";
  } else {
      echo "<p>No products found.</p>";
  }

  $conn->close();
  ?>

  <br>
  <!-- Add Product Button -->
  <a href="addProduct.php">
    <button style="margin-top:20px; padding:10px 20px; font-size:16px;">➕ Add Product</button>
  </a>
</div>

            <!-- Notifications Section (Empty Container) -->
            <div class="content-section" id="notifications">
                <div class="empty-container">
                    Notifications Section - Add your content here
                </div>
            </div>

            <!-- Settings Section (Empty Container) -->
            <div class="content-section" id="settings">
                <div class="empty-container">
                    Settings Section - Add your content here
                </div>
            </div>
        </div>
    </body>

<!--AJAX-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.order-status').forEach(status => {
        status.addEventListener('click', function() {
            const orderId = this.dataset.id;
            const element = this;

            if (element.textContent.trim().toLowerCase() === 'completed') return; // already done

            fetch('update_order_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'order_id=' + orderId
            })
            .then(res => res.text())
            .then(data => {
                if (data === 'success') {
                    element.textContent = 'Completed';
                    element.classList.remove('status-pending');
                    element.classList.add('status-completed');
                } else {
                    alert('Failed to update order status.');
                }
            });
        });
    });
});
</script>
<script>
document.querySelectorAll('.status-toggle').forEach(element => {
    element.addEventListener('click', () => {
        const orderId = element.getAttribute('data-id');
        const currentStatus = element.textContent.trim();

        const newStatus = currentStatus === 'Pending' ? 'Completed' : 'Pending';

        fetch('update_order_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `order_id=${orderId}&new_status=${newStatus}`
        })
        .then(res => res.text())
        .then(data => {
            if (data === 'success') {
                element.textContent = newStatus;
                element.classList.toggle('status-pending');
                element.classList.toggle('status-completed');
            } else {
                alert('Failed to update status.');
            }
        });
    });
});
</script>

</html>
