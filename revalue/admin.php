<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styleForAdmin.css" />
    <script defer src="admin.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <title>Admin Dashboard</title>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
            <img src="uploads/logo.webp" alt="logo" />
            <span class="logo-text">RE-VALUE.PH</span> 
          </div>
            <button class="sidebar-toggle" id="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item active" data-section="overview">
                <i class="fas fa-chart-line"></i>
                <span class="nav-text">Overview</span>
                <div class="nav-indicator"></div>
            </div>

            <div class="nav-item" data-section="orders">
                <i class="fas fa-shopping-bag"></i>
                <span class="nav-text">Orders</span>
                <div class="nav-indicator"></div>
            </div>

            <div class="nav-item" data-section="notifications">
                <i class="fas fa-bell"></i>
                <span class="nav-text">Notifications</span>
                <div class="nav-indicator"></div>
            </div>

            <div class="nav-item" data-section="products">
                <i class="fas fa-box"></i>
                <span class="nav-text">Products</span>
                <div class="nav-indicator"></div>
            </div>

            <div class="nav-item" data-section="messaging">
                <i class="fas fa-message"></i>
                <span class="nav-text">Messages</span>
                <!-- <span class="badge">3</span> -->
                <div class="nav-indicator"></div>
            </div>

            <div class="nav-item logout-item" data-section="logout">
                <i class="fas fa-right-from-bracket"></i>
                <span class="nav-text">Logout</span>
                <div class="nav-indicator"></div>
                
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Toast Container -->
        <div id="toast-container" class="toast-container"></div>
        
        <!-- Header -->
        <header class="header ">
            <div class="header-left">
                <div class="welcome-section">
                    <div class="user-avatar">
                        <span>ED</span>
                        <div class="status-indicator"></div>
                    </div>
                    <div class="welcome-text">
                        <small>Hi, Admin!</small>
                        <h1>Welcome Back!</h1>
                    </div>
                </div>
            </div>
            
            <div class="header-right">
                
            </div>
        </header>

        <!-- Overview Section -->
        <section class="content-section active" id="overview">
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

           <!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card completed-card">
        <div class="stat-content">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $completedOrders ?></h3>
                <p class="stat-label">Orders Completed</p>
            </div>
        </div>
    </div>

    <div class="stat-card pending-card">
        <div class="stat-content">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $pendingOrders ?></h3>
                <p class="stat-label">Orders Pending</p>
            </div>
        </div>
    </div>

    <div class="stat-card users-card">
        <div class="stat-content">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3 class="stat-value"><?= $totalUsers ?></h3>
                <p class="stat-label">Total Users</p>
            </div>
        </div>
    </div>
</div>

            <!-- Recent Orders -->
<!-- Recent Orders -->
<div class="card recent-orders-card">
    <div class="card-header">
        <div class="card-title">
            <h2>Recent Orders</h2>
            <span class="card-subtitle"><?= $orders->num_rows ?> orders displayed</span>
        </div>
    </div>

    <div class="table-container">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $orders->fetch_assoc()): ?>
                    <?php
                        // Set badge class based on status
                        $statusClass = match($row['status']) {
                        'Pending' => 'status-pending',
                        'Delivered' => 'status-delivered',
                        'Completed' => 'status-completed',
                        default => 'status-other',
                    };
                    ?>
                    <tr>
                        <td>
                            <div class="customer-info">
                                <div class="customer-avatar"><?= strtoupper(substr($row['Full_name'], 0, 2)) ?></div>
                                <span><?= htmlspecialchars($row['Full_name']) ?></span>
                            </div>
                        </td>
                        <td><strong>₱<?= number_format($row['total_amount'], 2) ?></strong></td>
                        <td>
    <span class="status-badge <?= $statusClass ?>" data-id="<?= $row['id'] ?>">
        <i class="fas fa-circle"></i>
        <?= htmlspecialchars($row['status']) ?>
    </span>
</td>
                        <td><?= date('M d, Y', strtotime($row['order_date'])) ?></td>
                        <td><?= date('h:i A', strtotime($row['order_date'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- CSS for status badges -->
<style>
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: 500;
    font-size: 0.9rem;
}

.status-pending {
    background-color: #ffc107; /* Yellow */
    color: #000;
}

.status-delivered {
    background-color: #007bff; /* Blue */
    color: #fff;
}

.status-other {
    background-color: #6c757d; /* Gray */
    color: #fff;
}

.status-cancelled {
    background-color: #dc3545; /* Red */
    color: #fff;
}

.btn-cancel-order {
    padding: 6px 12px;
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: background-color 0.2s;
}

.btn-cancel-order:hover:not(:disabled) {
    background-color: #c82333;
}

.btn-cancel-order:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
        </section>

        <!-- Orders Section -->
<section class="content-section" id="orders">
    <div class="section-header">
        <h2 class="section-title">All Orders</h2>
    </div>

    <div class="card">
        <div class="table-container">
            <table class="modern-table">
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
                        <th>Actions</th>
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
                            // Badge class - add cancelled status
                            $statusClass = match(strtolower($order['status'])) {
                                'pending' => 'status-pending',
                                'delivered' => 'status-delivered',
                                'completed' => 'status-completed',
                                'cancelled' => 'status-cancelled',
                                'canceled' => 'status-cancelled',
                                default => 'status-other',
                            };
                            $statusText = htmlspecialchars($order['status']);
                            $isCancelled = (strtolower($order['status']) === 'cancelled' || strtolower($order['status']) === 'canceled');
                            $isCompleted = (strtolower($order['status']) === 'completed');
                            $isDelivered = (strtolower($order['status']) === 'delivered');

                            // Decode product names and images
                            $names = json_decode($order['product_names'], true);
                            $images = json_decode($order['product_images'], true);
                            $productHTML = '<div class="product-list">';

                            if (!empty($names) && !empty($images)) {
                                foreach ($names as $idx => $name) {
                                    $img = isset($images[$idx]) ? $images[$idx] : '';
                                    $productHTML .= "<div class='product-item'>";
                                    $productHTML .= "<img src='" . htmlspecialchars($img) . "' alt='" . htmlspecialchars($name) . "'>";
                                    $productHTML .= "<span>" . htmlspecialchars($name) . "</span>";
                                    $productHTML .= "</div>";
                                }
                            }
                            $productHTML .= "</div>";

                            // Cancel button - only show if not already cancelled, completed, or delivered
                            $cancelButton = '';
                            if ($isCancelled) {
                                $cancelButton = "<button class='btn-cancel-order' disabled style='opacity: 0.5; cursor: not-allowed;'>
                                    <i class='fas fa-ban'></i> Cancelled
                                </button>";
                            } elseif ($isCompleted || $isDelivered) {
                                // No cancel button for completed or delivered orders
                                $cancelButton = "";
                            } else {
                                $cancelButton = "<button class='btn-cancel-order' data-order-id='{$order['order_id']}' onclick='cancelOrder({$order['order_id']}, this)'>
                                    <i class='fas fa-times-circle'></i> Cancel Order
                                </button>";
                            }

                            echo "
                            <tr>
                                <td><strong>#" . str_pad($order['order_id'], 5, '0', STR_PAD_LEFT) . "</strong></td>
                                <td>" . htmlspecialchars($order['user_name']) . "</td>
                                <td>" . htmlspecialchars($order['user_email']) . "</td>
                                <td><span class='text-muted'>" . htmlspecialchars($order['user_address']) . "</span></td>
                                <td>$productHTML</td>
                                <td><strong>₱" . number_format($order['total_amount'], 2) . "</strong></td>
                                <td>
                                    <span class='status-badge $statusClass' data-id='{$order['order_id']}'>
                                        <i class='fas fa-circle'></i> $statusText
                                    </span>
                                </td>
                                <td>" . date('M d, Y h:i A', strtotime($order['order_date'])) . "</td>
                                <td>$cancelButton</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>No orders found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<!-- Toast Elements -->
<div class="toast-overlay" id="toastOverlay"></div>
<div class="toast-container" id="toastContainer"></div>

<!-- JS for Toast System and Order Status Updates -->
<script>
    // Define Toast System
    const Toast = {
        container: null,
        overlay: null,

        init() {
            this.container = document.getElementById('toastContainer');
            this.overlay = document.getElementById('toastOverlay');
            
            if (this.overlay) {
                this.overlay.addEventListener('click', () => this.hide());
            }
        },

        show(options) {
            const { type = 'info', title, message, actions = [] } = options;
            this.container.innerHTML = '';

            const card = document.createElement('div');
            card.className = `toast-card ${type}`;

            const icons = {
                warning: '<i class="fas fa-exclamation-triangle"></i>',
                success: '<i class="fas fa-check-circle"></i>',
                error: '<i class="fas fa-times-circle"></i>',
                info: '<i class="fas fa-info-circle"></i>'
            };

            card.innerHTML = `
                <div class="toast-icon">${icons[type]}</div>
                ${title ? `<h2 class="toast-title">${title}</h2>` : ''}
                ${message ? `<p class="toast-message">${message}</p>` : ''}
                ${actions.length > 0 ? `
                    <div class="toast-actions">
                        ${actions.map((action, index) => `
                            <button class="toast-btn toast-btn-${action.style}" data-action="${index}">
                                ${action.label}
                            </button>
                        `).join('')}
                    </div>
                ` : ''}
            `;

            this.container.appendChild(card);

            actions.forEach((action, index) => {
                const btn = card.querySelector(`[data-action="${index}"]`);
                if (btn) btn.addEventListener('click', () => action.onClick && action.onClick());
            });

            document.body.style.overflow = 'hidden';

            setTimeout(() => {
                this.overlay.classList.add('show');
                this.container.classList.add('show');
            }, 10);
        },

        hide() {
            this.overlay.classList.remove('show');
            this.container.classList.remove('show');
            document.body.style.overflow = '';
        },

        showLoading(message = 'Loading...') {
            this.container.innerHTML = `
                <div class="toast-card">
                    <div class="toast-loading">
                        <div class="toast-spinner"></div>
                        <span style="color: var(--muted-foreground); font-size: 1rem;">${message}</span>
                    </div>
                </div>
            `;
            
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => {
                this.overlay.classList.add('show');
                this.container.classList.add('show');
            }, 10);
        },

        showSuccess(title, message, duration = 2000) {
            this.show({ type: 'success', title, message });
            if (duration) {
                setTimeout(() => this.hide(), duration);
            }
        },

        showSuccessWithLoading(title, message) {
            this.container.innerHTML = '';
            
            const card = document.createElement('div');
            card.className = 'toast-card success';
            card.innerHTML = `
                <div class="toast-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="toast-title">${title}</h2>
                <p class="toast-message">${message}</p>
                <div class="toast-loading-bar">
                    <div class="toast-loading-progress"></div>
                </div>
            `;
            
            this.container.appendChild(card);
            
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => {
                this.overlay.classList.add('show');
                this.container.classList.add('show');
            }, 10);
        }
    };

    // Initialize when DOM is ready
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Toast System
        Toast.init();

        // Update Recent Orders function
        const updateRecentOrders = (orderId, newStatus) => {
            const recentBadge = document.querySelector(`.recent-orders-card .status-badge[data-id='${orderId}']`);
            if (recentBadge) {
                recentBadge.innerHTML = `<i class='fas fa-circle'></i> ${newStatus}`;
                recentBadge.classList.remove("status-pending", "status-delivered", "status-completed", "status-cancelled", "status-other");
                
                let statusClass = "status-other";
                if (newStatus === "Delivered") {
                    statusClass = "status-delivered";
                } else if (newStatus === "Completed") {
                    statusClass = "status-completed";
                } else if (newStatus === "Cancelled" || newStatus === "Canceled") {
                    statusClass = "status-cancelled";
                } else if (newStatus === "Pending") {
                    statusClass = "status-pending";
                }
                
                recentBadge.classList.add(statusClass);
                recentBadge.style.cursor = "default";
            }
        };

        // Handle Pending Badge Clicks
        document.querySelectorAll("#orders .status-badge.status-pending").forEach(badge => {
            const handleClick = function() {
                const orderId = this.getAttribute("data-id");
                const badgeElement = this;

                // Show confirmation toast
                Toast.show({
                    type: 'info',
                    title: 'Update Order Status',
                    message: 'Mark this order as Delivered?',
                    actions: [
                        {
                            label: 'Cancel',
                            style: 'secondary',
                            onClick: () => Toast.hide()
                        },
                        {
                            label: 'Mark as Delivered',
                            style: 'primary',
                            onClick: () => {
                                // Show loading
                                Toast.showLoading('Updating order status...');

                                fetch("update_order_status.php", {
                                    method: "POST",
                                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                    body: "order_id=" + encodeURIComponent(orderId) + "&status=Delivered"
                                })
                                .then(res => res.text())
                                .then(response => {
                                    if (response.trim() === "success") {
                                        // Show success with inline loading
                                        Toast.showSuccessWithLoading(
                                            'Order Updated Successfully',
                                            'Status has been changed to Delivered'
                                        );

                                        // Update badge after showing success
                                        setTimeout(() => {
                                            badgeElement.innerHTML = "<i class='fas fa-circle'></i> Delivered";
                                            badgeElement.classList.remove("status-pending");
                                            badgeElement.classList.add("status-delivered");
                                            badgeElement.style.cursor = "default";

                                            // Remove click listener
                                            badgeElement.removeEventListener("click", handleClick);

                                            updateRecentOrders(orderId, "Delivered");

                                            // Auto-hide toast after 3 seconds
                                            setTimeout(() => Toast.hide(), 3000);
                                        }, 500);
                                    } else {
                                        // Show error toast
                                        Toast.show({
                                            type: 'error',
                                            title: 'Update Failed',
                                            message: 'Error updating order status. Please try again.',
                                            actions: [
                                                {
                                                    label: 'Close',
                                                    style: 'secondary',
                                                    onClick: () => Toast.hide()
                                                }
                                            ]
                                        });
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    
                                    // Show error toast
                                    Toast.show({
                                        type: 'error',
                                        title: 'Connection Error',
                                        message: 'Error connecting to server. Please check your connection.',
                                        actions: [
                                            {
                                                label: 'Close',
                                                style: 'secondary',
                                                onClick: () => Toast.hide()
                                            }
                                        ]
                                    });
                                });
                            }
                        }
                    ]
                });
            };

            badge.addEventListener("click", handleClick);
        });
    });

    // Cancel Order Function
    function cancelOrder(orderId, buttonElement) {
        // Show confirmation toast
        Toast.show({
            type: 'warning',
            title: 'Cancel Order',
            message: `Are you sure you want to cancel order #${String(orderId).padStart(5, '0')}? This action cannot be undone.`,
            actions: [
                {
                    label: 'No',
                    style: 'secondary',
                    onClick: () => Toast.hide()
                },
                {
                    label: 'Yes, Cancel Order',
                    style: 'primary',
                    onClick: () => {
                        // Show loading
                        Toast.showLoading('Cancelling order...');

                        fetch("cancel_order.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: "order_id=" + encodeURIComponent(orderId)
                        })
                        .then(res => res.text())
                        .then(response => {
                            if (response.trim() === "success") {
                                // Show success with inline loading
                                Toast.showSuccessWithLoading(
                                    'Order Cancelled',
                                    `Order #${String(orderId).padStart(5, '0')} has been cancelled successfully`
                                );

                                // Update UI after showing success
                                setTimeout(() => {
                                    // Update status badge
                                    const statusBadge = document.querySelector(`#orders .status-badge[data-id='${orderId}']`);
                                    if (statusBadge) {
                                        statusBadge.innerHTML = "<i class='fas fa-circle'></i> Cancelled";
                                        statusBadge.classList.remove("status-pending", "status-delivered", "status-completed", "status-other");
                                        statusBadge.classList.add("status-cancelled");
                                        statusBadge.style.cursor = "default";
                                    }

                                    // Update cancel button
                                    if (buttonElement) {
                                        buttonElement.disabled = true;
                                        buttonElement.style.opacity = '0.5';
                                        buttonElement.style.cursor = 'not-allowed';
                                        buttonElement.innerHTML = "<i class='fas fa-ban'></i> Cancelled";
                                        buttonElement.onclick = null;
                                    }

                                    // Update recent orders if exists
                                    updateRecentOrders(orderId, "Cancelled");

                                    // Auto-hide toast after 3 seconds
                                    setTimeout(() => Toast.hide(), 3000);
                                }, 500);
                            } else {
                                // Show error toast
                                Toast.show({
                                    type: 'error',
                                    title: 'Cancellation Failed',
                                    message: response.trim().startsWith('error:') ? response.trim().substring(7) : 'Error cancelling order. Please try again.',
                                    actions: [
                                        {
                                            label: 'Close',
                                            style: 'secondary',
                                            onClick: () => Toast.hide()
                                        }
                                    ]
                                });
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            
                            // Show error toast
                            Toast.show({
                                type: 'error',
                                title: 'Connection Error',
                                message: 'Error connecting to server. Please check your connection.',
                                actions: [
                                    {
                                        label: 'Close',
                                        style: 'secondary',
                                        onClick: () => Toast.hide()
                                    }
                                ]
                            });
                        });
                    }
                }
            ]
        });
    }

    // Logout Functions (can be called from anywhere)
    function handleFormLogout() {
        Toast.show({
            type: 'warning',
            title: 'Confirm Logout',
            message: 'Are you sure you want to logout?',
            actions: [
                {
                    label: 'Cancel',
                    style: 'secondary',
                    onClick: () => Toast.hide()
                },
                {
                    label: 'Logout',
                    style: 'primary',
                    onClick: () => {
                        Toast.showLoading('Logging out...');
                        setTimeout(() => {
                            document.getElementById('logoutForm').submit();
                        }, 1000);
                    }
                }
            ]
        });
    }

    function handleLogout() {
        Toast.show({
            type: 'warning',
            title: 'Confirm Logout',
            message: 'Are you sure you want to logout?',
            actions: [
                {
                    label: 'Cancel',
                    style: 'secondary',
                    onClick: () => Toast.hide()
                },
                {
                    label: 'Logout',
                    style: 'primary',
                    onClick: () => {
                        Toast.showLoading('Logging out...');
                        setTimeout(() => {
                            Toast.showSuccess('Logged Out', 'Redirecting...');
                            setTimeout(() => {
                                window.location.href = 'logout.php';
                            }, 2000);
                        }, 1000);
                    }
                }
            ]
        });
    }
</script>




        <!-- Notifications Section -->
<!-- Notifications Section -->
<section class="content-section" id="notifications">
    <div class="section-header">
        <h2 class="section-title">Notifications</h2>
        <p class="section-subtitle">Recent confirmations from customers who have marked their orders as received.</p>
    </div>

    <?php
    include('db.php');

    $notificationStmt = $conn->prepare("
        SELECT 
            o.id,
            o.user_id,
            o.total_amount,
            o.order_date,
            o.status,
            o.product_names,
            o.product_images,
            o.product_sizes,
            o.proof_of_delivery,
            u.Full_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.status = 'Completed'
        ORDER BY o.order_date DESC
        LIMIT 12
    ");
    $notificationStmt->execute();
    $notifications = $notificationStmt->get_result();
    ?>

    <div class="notification-grid">
        <?php if ($notifications && $notifications->num_rows > 0): ?>
            <?php while ($notification = $notifications->fetch_assoc()): ?>
                <?php
                // Decode JSON fields
                $items = json_decode($notification['product_names'], true) ?: [];
                $images = json_decode($notification['product_images'], true) ?: [];
                $sizes = json_decode($notification['product_sizes'] ?? '[]', true) ?: [];

                // Determine preview image: proof_of_delivery > first product image > placeholder
                $previewImage = !empty($notification['proof_of_delivery'])
                    ? $notification['proof_of_delivery']
                    : ($images[0] ?? null);

                // Prepare item chips
                $itemChips = [];
                foreach ($items as $index => $itemName) {
                    $label = htmlspecialchars($itemName, ENT_QUOTES, 'UTF-8');
                    if (!empty($sizes[$index])) {
                        $label .= ' · Size ' . htmlspecialchars($sizes[$index], ENT_QUOTES, 'UTF-8');
                    }
                    $itemChips[] = $label;
                }
                $chipDisplay = array_slice($itemChips, 0, 3);

                // Encode for data attributes
                $encodedImages = htmlspecialchars(json_encode($images, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                $encodedItems = htmlspecialchars(json_encode($items, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                ?>
                
                <article class="notification-card-clean">
                    <!-- Image Section -->
                    <div class="notif-image-section">
                        <?php if ($previewImage): ?>
                            <img src="<?php echo htmlspecialchars($previewImage, ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="Order preview" 
                                 class="notif-image" 
                                 loading="lazy" />
                        <?php else: ?>
                            <div class="notif-image-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>

                       
                    </div>


  



                    <!-- Content Section -->
                    <div class="notif-content-section">
                        <div class="notif-header">
                            <div>
                                <h3 class="notif-order-id">Order #<?php echo str_pad($notification['id'], 5, '0', STR_PAD_LEFT); ?></h3>
                                <p class="notif-customer"><?php echo htmlspecialchars($notification['Full_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <span class="notif-status-badge">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>

                        <div class="notif-items">
                            <?php if (!empty($chipDisplay)): ?>
                                <?php foreach (array_slice($chipDisplay, 0, 2) as $chip): ?>
                                    <span class="notif-item-tag"><?php echo $chip; ?></span>
                                <?php endforeach; ?>
                                <?php if (count($items) > 2): ?>
                                    <span class="notif-item-more">+<?php echo count($items) - 2; ?> more</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="notif-footer">
                            <div class="notif-info">
                                <span class="notif-amount">₱<?php echo number_format((float) $notification['total_amount'], 2); ?></span>
                                <span class="notif-date"><?php echo date('M d, Y', strtotime($notification['order_date'])); ?></span>
                            </div>
                           
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="notification-empty">
                <i class="fas fa-bell-slash"></i>
                <h3>No notifications yet</h3>
                <p>Completed orders will appear here once customers confirm receipt.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php
    if (isset($notificationStmt)) $notificationStmt->close();
    if (isset($conn)) $conn->close();
    ?>
</section>


<style>

</style>
        <div id="notification-preview-modal" class="notification-preview-modal" aria-hidden="true">
            <div class="notification-preview-dialog" role="dialog" aria-modal="true">
                <button type="button" class="notification-preview-close" aria-label="Close preview">&times;</button>
                <div class="notification-preview-header">
                    <h3 id="notification-preview-title">Order Photos</h3>
                    <p class="notification-preview-subtitle"></p>
                </div>
                <div class="notification-preview-gallery" id="notification-preview-gallery"></div>
                <div class="notification-preview-empty" id="notification-preview-empty">
                    <i class="fas fa-image"></i>
                    <p>No confirmation photos were provided for this order.</p>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <section class="content-section" id="products">
            <div class="section-header">
                <h2 class="section-title">Products</h2>
                <a href="addProduct.php">
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </a>
            </div>

            <?php
            include("db.php");
            $result = $conn->query("SELECT * FROM inventory ORDER BY id DESC");

            if ($result->num_rows > 0) {
                echo "<div class='products-grid'>";

                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product-card'>";
                    echo "<div class='product-image-container'>";
                    echo "<img src='" . $row['image'] . "' alt='" . htmlspecialchars($row['name']) . "' class='product-image'>";
                    echo "<div class='product-overlay'>";
                    echo "<a href='editProduct.php?id=" . $row['id'] . "' class='overlay-btn'><i class='fas fa-edit'></i></a>";
                    echo "<a href='deleteProduct.php?id=" . $row['id'] . "' onclick=\"return confirm('Are you sure you want to delete this product?');\" class='overlay-btn danger'><i class='fas fa-trash'></i></a>";
                    echo "</div>";
                    echo "</div>";
                    echo "<div class='product-details'>";
                    echo "<h3 class='product-name'>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<div class='product-meta'>";
                    echo "<span class='product-category'><i class='fas fa-tag'></i> " . htmlspecialchars($row['category']) . "</span>";
                    echo "<span class='product-size'><i class='fas fa-ruler'></i> " . htmlspecialchars($row['size']) . "</span>";
                    echo "</div>";
                    echo "<div class='product-price'>₱" . number_format($row['price']) . "</div>";
                    echo "</div>";
                    echo "</div>";
                }

                echo "</div>";
            } else {
                echo "<div class='empty-state'>";
                echo "<i class='fas fa-box-open'></i>";
                echo "<p>No products found.</p>";
                echo "<a href='addProduct.php'><button class='btn btn-primary'>Add Your First Product</button></a>";
                echo "</div>";
            }
            $conn->close();
            ?>
        </section>

        <!-- Messages Section -->
        <section class="content-section" id="messaging">
           

            <div class="dm-container">
                <aside class="dm-sidebar">
                    <div class="dm-sidebar-header">
                        <h3>Conversations</h3>
                      
                    </div>
                    
                    <div id="dm-user-list" class="dm-user-list">
                        <div class="dm-loading">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Loading conversations...</span>
                        </div>
                    </div>
                </aside>
                
                <section class="dm-chat">
                    <div id="dm-header" class="dm-chat-header">
                        <div class="chat-header-content">
                            <i class="fas fa-message"></i>
                            <span>Select a conversation</span>
                        </div>
                    </div>
                    <div id="dm-box" class="dm-box">
                        <div class="dm-empty-state">
                            <i class="fas fa-comments"></i>
                            <p>Select a user to start messaging</p>
                        </div>
                    </div>
                    <form id="dm-form" class="dm-form">
                        <div class="dm-input-container">
                            <input id="dm-input" class="dm-input" type="text" placeholder="Type your message..." autocomplete="off" />
                            <button type="submit" class="dm-send">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </section>
            </div>
        </section>
    </main>

   

    <!-- Messaging Logic -->
    <script>
    (function(){
        const listEl = document.getElementById('dm-user-list');
        const boxEl = document.getElementById('dm-box');
        const headerEl = document.getElementById('dm-header');
        const formEl = document.getElementById('dm-form');
        const inputEl = document.getElementById('dm-input');
        if(!listEl || !boxEl || !headerEl || !formEl || !inputEl) return;

        let partnerId = 0;
        let lastId = 0;

        function appendMsg(m){
            const div = document.createElement('div');
            const isReceived = (m.sender_id === partnerId);
            div.className = isReceived ? 'message received' : 'message sent';
            div.textContent = m.body;
            boxEl.appendChild(div);
            boxEl.scrollTop = boxEl.scrollHeight;
        }

        function fetchUsers(){
            listEl.innerHTML = '<div class="dm-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
            fetch('chat_users.php').then(r=>r.json()).then(data=>{
                if(!data.success){ 
                    listEl.innerHTML = '<div class="dm-error"><i class="fas fa-exclamation-circle"></i> Failed to load users</div>'; 
                    return; 
                }
                listEl.innerHTML = '';
                if(!data.users.length){ 
                    listEl.innerHTML = '<div class="dm-empty"><i class="fas fa-user-slash"></i> No users yet</div>'; 
                    return; 
                }
                data.users.forEach(u=>{
                    const item = document.createElement('div');
                    item.className = 'dm-user-item';
                    item.innerHTML = `
                        <div class="user-avatar">${(u.name || u.email).substring(0, 2).toUpperCase()}</div>
                        <div class="user-info">
                            <div class="user-name">${u.name || u.email}</div>
                            <div class="user-status">Online</div>
                        </div>
                    `;
                    item.addEventListener('click', ()=>{
                        document.querySelectorAll('.dm-user-item').forEach(el => el.classList.remove('active'));
                        item.classList.add('active');
                        partnerId = u.id;
                        lastId = 0;
                        headerEl.innerHTML = `
                            <div class="chat-header-content">
                                <div class="user-avatar">${(u.name || u.email).substring(0, 2).toUpperCase()}</div>
                                <div class="user-info">
                                    <div class="user-name">${u.name || u.email}</div>
                                    <div class="user-status">Online</div>
                                </div>
                            </div>
                        `;
                        boxEl.innerHTML = '';
                        fetchMessages();
                    });
                    listEl.appendChild(item);
                });
            });
        }

        function fetchMessages(){
            if(!partnerId) return;
            fetch('chat_fetch.php?partner=' + partnerId + '&since=' + lastId)
                .then(r=>r.json())
                .then(data=>{
                    if(data.success && data.messages){
                        data.messages.forEach(m=>{ appendMsg(m); lastId = Math.max(lastId, parseInt(m.id)); });
                    }
                });
        }

        formEl.addEventListener('submit', function(e){
            e.preventDefault();
            if(!partnerId) return;
            const v = inputEl.value.trim();
            if(!v) return;
            const fd = new FormData();
            fd.append('message', v);
            fd.append('to', partnerId);
            inputEl.value = '';
            fetch('chat_send.php', { method:'POST', body: fd })
                .then(r=>r.json())
                .then(data=>{ if(data.success){ fetchMessages(); } });
        });

        fetchUsers();
        setInterval(fetchMessages, 3000);
    })();
    </script>

    <div class="toast-overlay" id="toastOverlay"></div>
    <div class="toast-container" id="toastContainer"></div>
</body>
</html>