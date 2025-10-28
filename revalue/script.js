function openModal() {
  document.getElementById("auth-overlay").style.display = "grid";
  // Lock background scroll
  document.body.style.overflow = "hidden";
}

function closeModal() {
  document.getElementById("auth-overlay").style.display = "none";
  // Unlock background scroll
  document.body.style.overflow = "";
}

// Open modal on page load
document.addEventListener("DOMContentLoaded", function () {
  const authOverlayInit = document.getElementById("auth-overlay");
  if (authOverlayInit) {
    openModal();

    // Check if there's a register error and show register form
    const registerError = document.querySelector(
      "#register-form-container .alert-error"
    );
    if (registerError) {
      showRegisterForm();
    }
  }

  // Prevent default jumps for '#' links to avoid layout shifts
  document.querySelectorAll('a[href="#"]').forEach(function (a) {
    a.addEventListener("click", function (e) {
      e.preventDefault();
    });
  });

  // NEW: Add event listeners for register form
  console.log("Looking for register form elements...");

  // Use setTimeout to ensure DOM is fully loaded
  setTimeout(function () {
    console.log("=== DEBUGGING REGISTER FORM ===");

    const showRegisterLink = document.getElementById("show-register-form");
    const backToLoginLink = document.getElementById("back-to-login");
    const registerContainer = document.getElementById(
      "register-form-container"
    );

    console.log("DOMContentLoaded - Elements found:", {
      showRegisterLink,
      backToLoginLink,
      registerContainer,
    });

    // Check if user is logged in (modal might not exist)
    const authOverlay = document.getElementById("auth-overlay");
    if (!authOverlay) {
      console.log("User is logged in - register form elements not available");
      return;
    }

    console.log("Auth overlay found, proceeding with event listeners...");

    if (showRegisterLink) {
      console.log("Adding click listener to show register link");
      showRegisterLink.addEventListener("click", function (e) {
        e.preventDefault();
        console.log("=== SHOW REGISTER LINK CLICKED ===");
        showRegisterForm();
      });
    } else {
      console.error("showRegisterLink not found!");
    }

    if (backToLoginLink) {
      console.log("Adding click listener to back to login link");
      backToLoginLink.addEventListener("click", function (e) {
        e.preventDefault();
        console.log("=== BACK TO LOGIN LINK CLICKED ===");
        showLoginForm();
      });
    } else {
      console.error("backToLoginLink not found!");
    }

    console.log("=== END DEBUGGING ===");
  }, 100);

  // FALLBACK: Use event delegation as backup
  console.log("Setting up event delegation fallback...");
  document.addEventListener("click", function (e) {
    if (e.target && e.target.id === "show-register-form") {
      e.preventDefault();
      console.log("=== FALLBACK: SHOW REGISTER LINK CLICKED ===");
      showRegisterForm();
    }
    if (e.target && e.target.id === "back-to-login") {
      e.preventDefault();
      console.log("=== FALLBACK: BACK TO LOGIN LINK CLICKED ===");
      showLoginForm();
    }
  });
});

// Close modal when clicking outside
(function () {
  const overlay = document.getElementById("auth-overlay");
  if (overlay) {
    overlay.addEventListener("click", function (e) {
      if (e.target === this) {
        closeModal();
      }
    });
  }
})();

// Close modal with Escape key
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    closeModal();
  }
});

function showToast(type, title, message) {
  const toast = document.getElementById("toast");
  const toastIcon = toast.querySelector(".toast-icon");
  const toastTitle = toast.querySelector(".toast-title");
  const toastMessage = toast.querySelector(".toast-message");
  const toastProgress = toast.querySelector(".toast-progress");

  // Set toast content
  toastTitle.textContent = title;
  toastMessage.textContent = message;

  // Set toast type and icon
  toast.className = `toast ${type}`;
  if (type === "success") {
    toastIcon.textContent = "✓";
  } else if (type === "error") {
    toastIcon.textContent = "✕";
  } else if (type === "info") {
    toastIcon.textContent = "ℹ";
  } else if (type === "warning") {
    toastIcon.textContent = "⚠";
  }

  // Show toast
  toast.style.display = "block";
  setTimeout(() => toast.classList.add("show"), 10);

  // Auto hide after 4 seconds
  setTimeout(() => {
    toast.classList.remove("show");
    setTimeout(() => (toast.style.display = "none"), 350);
  }, 4000);
}

function hideToast() {
  const toast = document.getElementById("toast");
  const toastProgress = toast.querySelector(".toast-progress");

  toastProgress.style.animationPlayState = "paused";
  toast.classList.remove("show");

  // Wait for transition to end before hiding
  setTimeout(() => {
    toast.style.display = "none";
    toast.className = "toast";
    toastProgress.style.animation = "none";
  }, 400);
}

// Function to show register form
function showRegisterForm() {
  console.log("=== showRegisterForm() called ===");
  const loginContainer = document.querySelector(
    ".first-container:not(#register-form-container)"
  );
  const registerContainer = document.getElementById("register-form-container");
  const authOverlay = document.getElementById("auth-overlay");

  if (loginContainer && registerContainer && authOverlay) {
    console.log("Switching to register form");
    loginContainer.style.display = "none";
    registerContainer.style.display = "flex";
    authOverlay.classList.add("register-active");
  } else {
    console.error("Could not find form containers:", {
      loginContainer,
      registerContainer,
      authOverlay,
    });
  }
}

// Function to show login form
function showLoginForm() {
  console.log("=== showLoginForm() called ===");
  const loginContainer = document.querySelector(
    ".first-container:not(#register-form-container)"
  );
  const registerContainer = document.getElementById("register-form-container");
  const authOverlay = document.getElementById("auth-overlay");

  if (loginContainer && registerContainer && authOverlay) {
    console.log("Switching to login form");
    registerContainer.style.display = "none";
    loginContainer.style.display = "flex";
    authOverlay.classList.remove("register-active");
  } else {
    console.error("Could not find form containers:", {
      loginContainer,
      registerContainer,
      authOverlay,
    });
  }
}

// Filter form submission with scroll preservation
function submitFilterForm() {
  // Store current scroll position in sessionStorage
  sessionStorage.setItem(
    "scrollPosition",
    window.pageYOffset || document.documentElement.scrollTop
  );

  // Submit the form normally (this will reload the page)
  // The scroll position will be restored on page load
  return true;
}

// Restore scroll position on page load
document.addEventListener("DOMContentLoaded", function () {
  const savedScrollPosition = sessionStorage.getItem("scrollPosition");
  if (savedScrollPosition) {
    // Small delay to ensure page is fully loaded
    setTimeout(() => {
      window.scrollTo(0, parseInt(savedScrollPosition));
      sessionStorage.removeItem("scrollPosition");
    }, 100);
  }
});

// Cart Modal Functions
function openCartModal() {
  const cartModal = document.getElementById("cart-overlay");
  if (cartModal) {
    cartModal.style.display = "flex";
    document.body.style.overflow = "hidden";
    loadCartItems();
  }
}

function closeCartModal() {
  const cartModal = document.getElementById("cart-overlay");
  if (cartModal) {
    cartModal.style.display = "none";
    document.body.style.overflow = "";
  }
}

function loadCartItems() {
  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");

  if (!cartItems || !cartTotal) return;

  // Show loading state
  cartItems.innerHTML =
    '<div class="cart-loading"><p>Loading cart items...</p></div>';

  fetch("get_cart.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        displayCartItems(data.items, data.total);
      } else {
        cartItems.innerHTML =
          '<div class="cart-empty"><p>Failed to load cart items.</p></div>';
      }
    })
    .catch((error) => {
      console.error("Error loading cart:", error);
      cartItems.innerHTML =
        '<div class="cart-empty"><p>Error loading cart items.</p></div>';
    });
}

function displayCartItems(items, total) {
  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");

  if (items.length === 0) {
    cartItems.innerHTML =
      '<div class="cart-empty"><p>Your cart is empty.</p></div>';
    cartTotal.textContent = "0";
    return;
  }

  let html = "";
  items.forEach((item) => {
    html += `
        <div class="cart-item">
          <img src="${item.image}" alt="${item.name}" class="cart-item-image">
          <div class="cart-item-details">
            <h4>${item.name}</h4>
            <p>Size: ${item.size}</p>
            <p>Price: ₱<strong>${item.price.toFixed(2)}</strong></p>
           
          </div>
          <div class="cart-item-actions">
            <button onclick="removeFromCart(${
              item.cart_id
            })" class="btn-remove">Remove</button>
          </div>
        </div>
      `;
  });

  cartItems.innerHTML = html;
  cartTotal.textContent = total.toFixed(2);
}

function removeFromCart(cartId) {
  if (!confirm("Are you sure you want to remove this item from your cart?")) {
    return;
  }

  fetch("remove_cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `cart_id=${cartId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast("success", "Item Removed", data.message);
        loadCartItems(); // Reload cart items
      } else {
        showToast("error", "Error", data.message);
      }
    })
    .catch((error) => {
      console.error("Error removing item:", error);
      showToast("error", "Error", "Failed to remove item from cart.");
    });
}

function proceedToCheckout() {
  const checkoutBtn = document.querySelector(".btn-checkout");
  if (!checkoutBtn) return;

  // Store original text and state
  const originalText = checkoutBtn.textContent;
  const originalDisabled = checkoutBtn.disabled;

  // Show processing state
  checkoutBtn.textContent = "Processing...";
  checkoutBtn.disabled = true;
  checkoutBtn.style.opacity = "0.7";
  checkoutBtn.style.cursor = "not-allowed";

  // Add a subtle loading animation using --sage color
  checkoutBtn.style.background =
    "linear-gradient(90deg, hsl(var(--sage)), hsl(var(--sage) / 0.85), hsl(var(--sage)))";
  checkoutBtn.style.backgroundSize = "200% 100%";
  checkoutBtn.style.animation = "processing 1.5s ease-in-out infinite";

  // Add the processing animation
  if (!document.getElementById("processing-styles")) {
    const style = document.createElement("style");
    style.id = "processing-styles";
    style.textContent = `
      @keyframes processing {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
      }
    `;
    document.head.appendChild(style);
  }

  // Simulate processing time and redirect
  setTimeout(() => {
    // Show final state before redirect
    checkoutBtn.textContent = "Redirecting...";
    checkoutBtn.style.background = "hsl(var(--sage))";
    checkoutBtn.style.animation = "none";

    // Redirect after a short delay
    setTimeout(() => {
      window.location.href = "checkout_review.php";
    }, 800);
  }, 2000); // 2 seconds processing + 0.8 seconds redirect = 2.8 seconds total
}

// Close cart modal when clicking outside
document.addEventListener("click", function (e) {
  const cartModal = document.getElementById("cart-overlay");
  if (e.target === cartModal) {
    closeCartModal();
  }
});

// Close cart modal with Escape key
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    const cartModal = document.getElementById("cart-overlay");
    if (cartModal && cartModal.style.display === "flex") {
      closeCartModal();
    }
  }
});

// Add to cart function using AJAX
function addToCart(productId, productName, buttonElement) {
  // Disable button to prevent multiple clicks
  buttonElement.disabled = true;
  buttonElement.textContent = "Adding...";
  buttonElement.style.opacity = "0.7";

  // Create form data
  const formData = new FormData();
  formData.append("product_id", productId);

  // Make AJAX request
  fetch("cart.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Show beautiful success toast - matching blueprint style
        showToast("success", "Success", data.message);

        // Update button to show "Already in Cart"
        buttonElement.style.background = "hsl(var(--muted))";
        buttonElement.style.color = "hsl(var(--muted-foreground))";
        buttonElement.style.cursor = "not-allowed";
        buttonElement.style.border = "1px solid hsl(var(--border))";
        buttonElement.textContent = "✓ Already in Cart";
        buttonElement.disabled = true;
        buttonElement.style.opacity = "1";

        // Add a subtle pulse effect
        buttonElement.style.animation = "pulse 0.6s ease-in-out";
        setTimeout(() => {
          buttonElement.style.animation = "";
        }, 600);
      } else {
        // Show error toast
        showToast("error", "Failed to Add", data.message);

        // Re-enable button with error styling
        buttonElement.disabled = false;
        buttonElement.textContent = "Try Again";
        buttonElement.style.opacity = "1";
        buttonElement.style.background = "hsl(var(--destructive) / 0.1)";
        buttonElement.style.border = "1px solid hsl(var(--destructive) / 0.3)";
        buttonElement.style.color = "hsl(var(--destructive))";

        // Reset button after 2 seconds
        setTimeout(() => {
          buttonElement.textContent = "Add to Cart";
          buttonElement.style.background = "";
          buttonElement.style.border = "";
          buttonElement.style.color = "";
        }, 2000);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showToast(
        "error",
        "Connection Error",
        "Network error. Please check your connection and try again."
      );

      // Re-enable button
      buttonElement.disabled = false;
      buttonElement.textContent = "Add to Cart";
      buttonElement.style.opacity = "1";
      buttonElement.style.background = "";
      buttonElement.style.border = "";
      buttonElement.style.color = "";
    });
}

// Add pulse animation for success feedback
const style = document.createElement("style");
style.textContent = `
      @keyframes pulse {
          0% { transform: scale(1); }
          50% { transform: scale(1.05); }
          100% { transform: scale(1); }
      }
  `;
document.head.appendChild(style);
