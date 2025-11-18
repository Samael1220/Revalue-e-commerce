<?php
include("db.php");
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user addresses
$stmtAddr = $conn->prepare("SELECT address, address2, address3 FROM users WHERE id=?");
$stmtAddr->bind_param("i", $user_id);
$stmtAddr->execute();
$userAddresses = $stmtAddr->get_result()->fetch_assoc();
$stmtAddr->close();

// Handle NULL correctly before converting
$addr1 = strtolower(trim($userAddresses['address'] ?? ""));
$addr2 = strtolower(trim($userAddresses['address2'] ?? ""));
$addr3 = strtolower(trim($userAddresses['address3'] ?? ""));

// Block checkout if all addresses are invalid
if (
    ($addr1 === "not provided" || $addr1 === "") &&
    ($addr2 === "" || $addr2 === "not provided") &&
    ($addr3 === "" || $addr3 === "not provided")
) {
    $_SESSION['order_error'] = "You must update your address before checking out.";
    header("Location: userDashboard.php");
    exit();
}

// Fetch cart items
$stmtCart = $conn->prepare("
    SELECT c.id AS cart_id, i.id AS product_id, i.name, i.size, i.price, i.image, c.quantity
    FROM cart c
    JOIN inventory i ON c.product_id = i.id
    WHERE c.user_id = ?
");
$stmtCart->bind_param("i", $user_id);
$stmtCart->execute();
$selectedItems = $stmtCart->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtCart->close();

if (!$selectedItems) {
    header("Location: userDashboard.php");
    exit();
}

// Calculate grand total
$grandTotal = array_reduce($selectedItems, function($carry, $item) {
    return $carry + ($item['price'] * $item['quantity']);
}, 0.0);

// Handle order confirmation
if (isset($_POST['confirm_order'])) {
    $shippingAddress   = $_POST['shipping_address'] ?? '';
    $selectedPayment   = $_POST['payment_method'] ?? 'Cash on Delivery';
    // Store as "Online Payment" when GCash is used
    $paymentMethodDb   = ($selectedPayment === 'GCash') ? 'Online Payment' : $selectedPayment;

    // Optional: handle uploaded GCash receipt (required on front-end)
    // This will save the file inside the existing uploads/ folder.
    $gcashReceiptPath = null;
    if ($selectedPayment === 'GCash') {
        if (!isset($_FILES['gcash_receipt']) || $_FILES['gcash_receipt']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['order_error'] = "Please upload your GCash payment receipt.";
            header("Location: checkout_review.php");
            exit();
        }

        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = $_FILES['gcash_receipt']['name'];
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
        if ($safeExt === '') {
            $safeExt = 'jpg';
        }

        $newFileName = time() . '_gcash_' . bin2hex(random_bytes(4)) . '.' . $safeExt;
        $targetPath = $uploadDir . $newFileName;

        if (!move_uploaded_file($_FILES['gcash_receipt']['tmp_name'], $targetPath)) {
            $_SESSION['order_error'] = "Failed to upload GCash receipt. Please try again.";
            header("Location: checkout_review.php");
            exit();
        }

        $gcashReceiptPath = $targetPath;
    }

    $conn->begin_transaction();

    try {
        // Prepare JSON arrays for orders table
        $productNames  = [];
        $productImages = [];
        $productSizes  = [];
        $productPrices = [];

        foreach ($selectedItems as $item) {
            $productNames[]  = $item['name'];
            $productImages[] = $item['image'];
            $productSizes[]  = $item['size'];
            $productPrices[] = $item['price']; // Store individual prices
        }

        $namesJson  = json_encode($productNames);
        $imagesJson = json_encode($productImages);
        $sizesJson  = json_encode($productSizes);
        $pricesJson = json_encode($productPrices);

        // Insert into orders table (including optional payment_proof for online payments)
        $stmtOrder = $conn->prepare("
            INSERT INTO orders 
            (user_id, total_amount, payment_method, payment_proof, order_date, shipping_address, product_names, product_images, product_sizes, product_prices)
            VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)
        ");
        $paymentMethod = $paymentMethodDb;
        $paymentProof  = $gcashReceiptPath; // NULL for COD, path string for GCash
        $stmtOrder->bind_param(
            "idsssssss", // 9 variables
            $user_id,
            $grandTotal,
            $paymentMethod,
            $paymentProof,
            $shippingAddress,
            $namesJson,
            $imagesJson,
            $sizesJson,
            $pricesJson
        );
        $stmtOrder->execute();
        $order_id = $stmtOrder->insert_id;
        $stmtOrder->close();

        // Insert into order_items table
        $stmtItem = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, product_image, size, quantity, price)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($selectedItems as $item) {
            $stmtItem->bind_param(
                "iisssid",
                $order_id,
                $item['product_id'],
                $item['name'],
                $item['image'],
                $item['size'],
                $item['quantity'],
                $item['price']
            );
            $stmtItem->execute();
        }
        $stmtItem->close();

        // Clear cart
        $stmtDelCart = $conn->prepare("DELETE FROM cart WHERE user_id=?");
        $stmtDelCart->bind_param("i", $user_id);
        $stmtDelCart->execute();
        $stmtDelCart->close();

        $conn->commit();

        $_SESSION['order_success_id'] = $order_id;
        header("Location: order_success.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['order_error'] = "Checkout failed: " . $e->getMessage();
        header("Location: userDashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Review Checkout</title>
<link rel="stylesheet" href="checkout.css">
</head>
<body>
<!-- Keep the PHP part the same as before, just ensure the CSS classes match -->
<div class="checkout-section">
  <div class="checkout-container">
    <div class="checkout-header">
      <div class="header-content">
        <h1 class="checkout-title">Review Your Order</h1>
        <p class="checkout-subtitle">Please confirm your items and total before placing the order.</p>
      </div>
      <div class="progress-indicator">
        <div class="progress-step">
          <span class="step-number">1</span>
          <span class="step-label">Cart</span>
        </div>
        <div class="progress-step active">
          <span class="step-number">2</span>
          <span class="step-label">Review</span>
        </div>
        <div class="progress-step">
          <span class="step-number">3</span>
          <span class="step-label">Complete</span>
        </div>
      </div>
    </div>

    <div class="checkout-layout">
      <!-- Order Summary -->
      <div class="order-summary">
        <div class="summary-header">
          <h3>Order Summary</h3>
          <span class="items-count"><?= count($selectedItems) ?> item<?= count($selectedItems) !== 1 ? 's' : '' ?></span>
        </div>

        <div class="cart-items">
          <?php foreach ($selectedItems as $item): ?>
            <div class="cart-item">
              <div class="item-image">
                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-thumb">
              </div>
              <div class="item-details">
                <h4 class="item-name"><?= htmlspecialchars($item['name']) ?></h4>
                <div class="item-meta">
                  <span class="item-size">Size: <?= htmlspecialchars($item['size']) ?></span>
                  
                </div>
                <div class="item-price">₱<?= number_format($item['price'], 2) ?></div>
              </div>
              <div class="item-total">
                ₱<?= number_format($item['price'] * $item['quantity'], 2) ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="order-totals">
          <div class="total-row">
            <span>Subtotal</span>
            <span>₱<?= number_format($grandTotal, 2) ?></span>
          </div>
          <div class="total-row">
            <span>Shipping</span>
            <span>Free</span>
          </div>
          <div class="total-row final">
            <span>Total</span>
            <span class="grand-total">₱<?= number_format($grandTotal, 2) ?></span>
          </div>
        </div>
      </div>

      <!-- Checkout Form -->
      <div class="checkout-form-container">
        <form method="POST" id="checkout-form" class="checkout-form" enctype="multipart/form-data">
          <div class="form-section">
            <h4 class="section-title">Shipping Address</h4>
            <div class="select-wrapper">
             
              <select name="shipping_address" id="shipping_address" class="styled-select" required>
                <?php foreach (['address','address2','address3'] as $key): ?>
                  <?php if (!empty($userAddresses[$key])): ?>
                    <option value="<?= htmlspecialchars($userAddresses[$key]) ?>">
                      <?= htmlspecialchars($userAddresses[$key]) ?>
                    </option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-section">
            <h4 class="section-title">Payment Method</h4>
            <div class="payment-options">
              <label class="payment-option selected">
                <input type="radio" name="payment_method" value="Cash on Delivery" checked>
                <div class="payment-content">
                  <span class="payment-icon">💵</span>
                  <div class="payment-info">
                    <span class="payment-name">Cash on Delivery</span>
                    <span class="payment-desc">Pay when you receive your order</span>
                  </div>
                </div>
              </label>
              <label class="payment-option">
                <input type="radio" name="payment_method" value="GCash">
                <div class="payment-content">
                  <span class="payment-icon">📱</span>
                  <div class="payment-info">
                    <span class="payment-name">GCash (Online Payment)</span>
                    <span class="payment-desc">Pay via GCash and upload your receipt</span>
                  </div>
                </div>
              </label>
            </div>
          </div>

          <div class="order-security">
            <div class="security-features">
              <div class="security-item">
                <span class="security-icon">🔒</span>
                <span>Secure checkout</span>
              </div>
              <div class="security-item">
                <span class="security-icon">🚚</span>
                <span>Free delivery</span>
              </div>
              <div class="security-item">
                <span class="security-icon">↩️</span>
                <span>Easy returns</span>
              </div>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" name="confirm_order" class="btn-place-order">
              <span class="btn-text">Confirm Order</span>
             
            </button>
            <a href="userDashboard.php" class="btn-back">Cancel Order</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Lightweight Confirmation Modal (scoped to this page) -->
<div id="cr-confirm-overlay" class="cr-confirm-overlay" aria-hidden="true" style="display:none;">
  <div class="cr-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="cr-confirm-title">
    <div class="cr-confirm-header">
      <h3 id="cr-confirm-title" class="cr-confirm-title">Place Order</h3>
      <button type="button" class="cr-confirm-close" aria-label="Close">&times;</button>
    </div>
    <div class="cr-confirm-body">
      <p class="cr-confirm-message">Are you sure you want to place this order?</p>
    </div>
    <div class="cr-confirm-actions">
      <button type="button" class="cr-confirm-btn cr-confirm-cancel">Cancel</button>
      <button type="button" class="cr-confirm-btn cr-confirm-confirm">Confirm</button>
    </div>
  </div>
</div>

<!-- GCash Payment Modal -->
<div id="gcash-overlay" class="cr-confirm-overlay" aria-hidden="true" style="display:none;">
  <div class="cr-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="gcash-title">
    <div class="cr-confirm-header">
      <h3 id="gcash-title" class="cr-confirm-title">GCash Payment</h3>
      <button type="button" class="cr-confirm-close gcash-close" aria-label="Close">&times;</button>
    </div>
    <div class="cr-confirm-body">
      <p class="cr-confirm-message">Scan the QR code below to pay via GCash, then upload a screenshot or photo of your payment receipt.</p>
      <div style="text-align:center; margin: 16px 0;">
        <img src="uploads/gcash.jpg" alt="GCash QR Code" style="max-width:320px; width:100%; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.15);">
      </div>
      <div style="margin-top: 10px;">
        <label for="gcash_receipt" style="display:block; font-weight:600; margin-bottom:6px;">Upload GCash Receipt <span style="color:#dc2626;">*</span></label>
        <input type="file" id="gcash_receipt" name="gcash_receipt" accept="image/*" form="checkout-form"
               style="width:100%; padding:8px 10px; border-radius:8px; border:1px solid #e5e7eb; font-size:0.9rem;">
        <small id="gcash-error" style="color:#dc2626; display:none; margin-top:4px;">Receipt is required to continue.</small>
      </div>
    </div>
    <div class="cr-confirm-actions">
      <button type="button" class="cr-confirm-btn cr-confirm-cancel gcash-cancel">Cancel</button>
      <button type="button" class="cr-confirm-btn cr-confirm-confirm gcash-confirm">Confirm Payment</button>
    </div>
  </div>
</div>

<style>
  
  .cr-confirm-overlay { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,.5); z-index: 9999; padding: 16px; }
  .cr-confirm-overlay.show { display: flex; }
  .cr-confirm-dialog { background: #fff; color: #111; border-radius: 12px; width: min(420px, 100%); box-shadow: 0 20px 50px rgba(0,0,0,.25); overflow: hidden; }
  .cr-confirm-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid #eee; }
  .cr-confirm-title { margin: 0; font-size: 1.1rem; font-weight: 700; }
  .cr-confirm-close { background: transparent; border: none; font-size: 22px; line-height: 1; cursor: pointer; color: #666; border-radius: 6px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; }
  .cr-confirm-close:hover { background: #f3f4f6; color: #111; }
  .cr-confirm-body { padding: 18px 18px 18px 18px; color: #374151; }
  .cr-confirm-actions { display: flex; gap: 10px; justify-content: flex-end; padding: 0 18px 16px; }
  .cr-confirm-btn { min-width: 110px; padding: 10px 14px; border-radius: 8px; font-weight: 600; cursor: pointer; border: 1px solid transparent; }
  .cr-confirm-cancel { background: #fff; color: #374151; border-color: #e5e7eb; }
  .cr-confirm-cancel:hover { background: #f3f4f6; }
  .cr-confirm-confirm { background: #28a745; color: #fff; }
  .cr-confirm-confirm:hover { filter: brightness(0.95); }
</style>

<style>
 
  .cr-confirm-overlay { opacity: 0; pointer-events: none; transition: opacity 180ms ease; }
  .cr-confirm-overlay.show { opacity: 1; pointer-events: auto; }

  .cr-confirm-dialog { 
    transform: translateY(10px) scale(0.985); 
    opacity: 0; 
    transition: transform 220ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease, box-shadow 220ms ease; 
  }
  .cr-confirm-overlay.show .cr-confirm-dialog { transform: translateY(0) scale(1); opacity: 1; }

  .cr-confirm-close { transition: background-color 160ms ease, color 160ms ease, transform 160ms ease; }
  .cr-confirm-close:hover { transform: translateY(-1px); }

  .cr-confirm-btn { transition: background-color 160ms ease, color 160ms ease, border-color 160ms ease, transform 160ms ease, box-shadow 160ms ease; }
  .cr-confirm-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(59,130,246,.25); }
  .cr-confirm-cancel:hover { transform: translateY(-1px); }
  .cr-confirm-confirm { box-shadow: 0 10px 22px rgba(40,167,69,0.18); }
  .cr-confirm-confirm:hover { transform: translateY(-1px); filter: brightness(0.97); }

  @media (prefers-reduced-motion: reduce) {
    .cr-confirm-overlay, .cr-confirm-dialog, .cr-confirm-btn, .cr-confirm-close { transition: none !important; }
    .cr-confirm-dialog { transform: none !important; opacity: 1 !important; }
  }
</style>

<script>
  // Intercept submit to show confirmation modal
  (function() {
    const form = document.querySelector('.checkout-form');
    const overlay = document.getElementById('cr-confirm-overlay');
    const gcashOverlay = document.getElementById('gcash-overlay');
    const btnConfirm = overlay ? overlay.querySelector('.cr-confirm-confirm') : null;
    const btnCancel = overlay ? overlay.querySelector('.cr-confirm-cancel') : null;
    const btnClose = overlay ? overlay.querySelector('.cr-confirm-close') : null;

    const gcashConfirm = gcashOverlay ? gcashOverlay.querySelector('.gcash-confirm') : null;
    const gcashCancel  = gcashOverlay ? gcashOverlay.querySelector('.gcash-cancel') : null;
    const gcashClose   = gcashOverlay ? gcashOverlay.querySelector('.gcash-close') : null;
    const gcashInput   = gcashOverlay ? gcashOverlay.querySelector('#gcash_receipt') : null;
    const gcashError   = gcashOverlay ? gcashOverlay.querySelector('#gcash-error') : null;

    if (!form || !overlay) return;

    // Toggle selected class for payment options
    const paymentRadios = form.querySelectorAll('input[name="payment_method"]');
    paymentRadios.forEach(radio => {
      radio.addEventListener('change', () => {
        document.querySelectorAll('.payment-option').forEach(label => {
          label.classList.remove('selected');
        });
        if (radio.closest('.payment-option')) {
          radio.closest('.payment-option').classList.add('selected');
        }
      });
    });

    function getSelectedPayment() {
      const checked = form.querySelector('input[name="payment_method"]:checked');
      return checked ? checked.value : 'Cash on Delivery';
    }

    function openConfirm() {
      overlay.classList.add('show');
      overlay.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      setTimeout(() => btnConfirm && btnConfirm.focus(), 50);
    }
    function closeConfirm() {
      overlay.classList.remove('show');
      overlay.style.display = 'none';
      document.body.style.overflow = '';
    }

    form.addEventListener('submit', function(e) {
      // If already confirmed once, allow submit
      if (form.dataset.confirmed === 'true') return;
      e.preventDefault();

      const paymentMethod = getSelectedPayment();
      if (paymentMethod === 'GCash' && gcashOverlay) {
        // Show GCash modal first
        gcashOverlay.classList.add('show');
        gcashOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        if (gcashError) {
          gcashError.style.display = 'none';
        }
        return;
      }

      openConfirm();
    });

    // Confirm action -> submit form for real
    btnConfirm && btnConfirm.addEventListener('click', function() {
      form.dataset.confirmed = 'true';
      // Ensure PHP sees the submit button value when submitting programmatically
      var hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'confirm_order';
      hidden.value = '1';
      form.appendChild(hidden);
      closeConfirm();
      form.submit();
    });

    // GCash confirm -> validate receipt then submit
    gcashConfirm && gcashConfirm.addEventListener('click', function() {
      if (!gcashInput || !gcashInput.files || gcashInput.files.length === 0) {
        if (gcashError) {
          gcashError.style.display = 'block';
        }
        return;
      }

      form.dataset.confirmed = 'true';
      var hidden = document.createElement('input');
      hidden.type = 'hidden';
      hidden.name = 'confirm_order';
      hidden.value = '1';
      form.appendChild(hidden);

      // Close GCash modal and submit
      gcashOverlay.classList.remove('show');
      gcashOverlay.style.display = 'none';
      document.body.style.overflow = '';
      form.submit();
    });

    // Cancel/Close handlers
    btnCancel && btnCancel.addEventListener('click', closeConfirm);
    btnClose && btnClose.addEventListener('click', closeConfirm);

    // GCash cancel/close handlers
    function closeGcash() {
      if (!gcashOverlay) return;
      gcashOverlay.classList.remove('show');
      gcashOverlay.style.display = 'none';
      document.body.style.overflow = '';
    }
    gcashCancel && gcashCancel.addEventListener('click', closeGcash);
    gcashClose && gcashClose.addEventListener('click', closeGcash);

    // Close when clicking outside dialogs
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) closeConfirm();
    });
    gcashOverlay && gcashOverlay.addEventListener('click', function(e) {
      if (e.target === gcashOverlay) closeGcash();
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && overlay.classList.contains('show')) closeConfirm();
      if (e.key === 'Escape' && gcashOverlay && gcashOverlay.classList.contains('show')) closeGcash();
    });
  })();
</script>


</body>
</html>