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

const confirmModalState = {
  overlay: null,
  titleEl: null,
  messageEl: null,
  confirmBtn: null,
  cancelBtn: null,
  closeBtn: null,
  onConfirm: null,
};

document.addEventListener("DOMContentLoaded", function () {
  const overlay = document.getElementById("confirm-overlay");
  if (!overlay) {
    return;
  }

  confirmModalState.overlay = overlay;
  confirmModalState.titleEl = overlay.querySelector(".confirm-title");
  confirmModalState.messageEl = overlay.querySelector(".confirm-message");
  confirmModalState.confirmBtn = overlay.querySelector(".confirm-confirm");
  confirmModalState.cancelBtn = overlay.querySelector(".confirm-cancel");
  confirmModalState.closeBtn = overlay.querySelector(".confirm-close");

  const closeHandler = () => closeConfirmModal();

  if (confirmModalState.cancelBtn) {
    confirmModalState.cancelBtn.addEventListener("click", closeHandler);
  }

  if (confirmModalState.closeBtn) {
    confirmModalState.closeBtn.addEventListener("click", closeHandler);
  }

  if (confirmModalState.confirmBtn) {
    confirmModalState.confirmBtn.addEventListener("click", () => {
      const handler = confirmModalState.onConfirm;
      closeConfirmModal();
      if (typeof handler === "function") {
        handler();
      }
    });
  }

  overlay.addEventListener("click", function (event) {
    if (event.target === overlay) {
      closeConfirmModal();
    }
  });
});

function openConfirmModal({
  title = "Confirm",
  message = "Are you sure you want to continue?",
  confirmText = "Confirm",
  cancelText = "Cancel",
  onConfirm,
} = {}) {
  if (!confirmModalState.overlay) {
    if (typeof onConfirm === "function") {
      onConfirm();
    }
    return;
  }

  if (confirmModalState.titleEl) {
    confirmModalState.titleEl.textContent = title;
  }
  if (confirmModalState.messageEl) {
    confirmModalState.messageEl.textContent = message;
  }
  if (confirmModalState.confirmBtn) {
    confirmModalState.confirmBtn.textContent = confirmText;
    confirmModalState.confirmBtn.disabled = false;
  }
  if (confirmModalState.cancelBtn) {
    confirmModalState.cancelBtn.textContent = cancelText;
  }

  confirmModalState.onConfirm = onConfirm;

  confirmModalState.overlay.classList.add("show");
  confirmModalState.overlay.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";

  if (confirmModalState.confirmBtn) {
    setTimeout(() => confirmModalState.confirmBtn.focus(), 50);
  }
}

function closeConfirmModal() {
  if (!confirmModalState.overlay) {
    return;
  }

  confirmModalState.overlay.classList.remove("show");
  confirmModalState.overlay.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
  confirmModalState.onConfirm = null;
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
    // Disable checkout while loading
    setCheckoutEnabled(false, true);
    loadCartItems();
  }
}

function closeCartModal() {
  const cartModal = document.getElementById("cart-overlay");

  if (cartModal) {
    cartModal.style.display = "none";
    document.body.style.overflow = "";

    // Save scroll position so the refresh looks invisible
    const scrollPos = window.scrollY;
    sessionStorage.setItem("scrollPos", scrollPos);

    // Refresh page
    location.reload();
  }
}

// Restore scroll after refresh
window.addEventListener("load", () => {
  const pos = sessionStorage.getItem("scrollPos");
  if (pos) {
    window.scrollTo(0, pos);
    sessionStorage.removeItem("scrollPos");
  }
});

function setCheckoutEnabled(enabled, isLoading = false) {
  const checkoutBtn = document.querySelector(".btn-checkout");
  if (!checkoutBtn) return;

  if (enabled) {
    checkoutBtn.disabled = false;
    checkoutBtn.style.cursor = "pointer";
    checkoutBtn.style.opacity = "1";
    checkoutBtn.innerHTML = "Proceed to Checkout";
  } else {
    checkoutBtn.disabled = true;
    checkoutBtn.style.cursor = "not-allowed";
    checkoutBtn.style.opacity = "0.6";
    checkoutBtn.innerHTML = isLoading
      ? 'Loading... <span class="dot-red"></span>'
      : 'Cart is empty <span class="dot-red"></span>';
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
        setCheckoutEnabled(false);
      }
    })
    .catch((error) => {
      console.error("Error loading cart:", error);
      cartItems.innerHTML =
        '<div class="cart-empty"><p>Error loading cart items.</p></div>';
      setCheckoutEnabled(false);
    });
}

function displayCartItems(items, total) {
  const cartItems = document.getElementById("cart-items");
  const cartTotal = document.getElementById("cart-total");

  if (items.length === 0) {
    cartItems.innerHTML =
      '<div class="cart-empty"><p>Your cart is empty.</p></div>';
    cartTotal.textContent = "0";
    setCheckoutEnabled(false);
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
  setCheckoutEnabled(true);
}

function removeFromCart(cartId) {
  openConfirmModal({
    title: "Remove item",
    message: "Are you sure you want to remove this item from your cart?",
    confirmText: "Remove",
    cancelText: "Keep item",
    onConfirm: () => executeCartRemoval(cartId),
  });
}

function executeCartRemoval(cartId) {
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
// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
// Helper to reset button
function resetButton(button, text = "Add to Cart") {
  button.disabled = false;
  button.textContent = text;
  button.style.opacity = "1";
  button.style.background = "";
  button.style.border = "";
  button.style.color = "";
  button.style.cursor = "pointer";
  button.style.animation = "";
}

// Add to cart function using AJAX
function addToCart(productId, productName, buttonElement) {
  // Disable button during request
  buttonElement.disabled = true;
  buttonElement.textContent = "Adding...";
  buttonElement.style.opacity = "0.7";
  buttonElement.style.pointerEvents = "none";

  // Use URLSearchParams to ensure PHP reads POST data
  const params = new URLSearchParams();
  params.append("product_id", productId);

  fetch("cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((response) => {
      if (!response.ok) throw new Error(`HTTP error ${response.status}`);
      return response.json();
    })
    .then((data) => {
      if (data.success) {
        // Update cart count on success
        updateCartCount();

        // Show success toast
        showToast("success", "Success", data.message);

        // Style button as added
        buttonElement.style.background = "hsl(var(--muted))";
        buttonElement.style.color = "hsl(var(--muted-foreground))";
        buttonElement.style.cursor = "not-allowed";
        buttonElement.style.border = "1px solid hsl(var(--border))";
        buttonElement.textContent = "✓ Already in Cart";
        buttonElement.disabled = true;
        buttonElement.style.opacity = "1";

        // Pulse effect
        buttonElement.style.animation = "pulse 0.6s ease-in-out";
        setTimeout(() => {
          buttonElement.style.animation = "";
        }, 600);
      } else {
        // Show error toast
        showToast("error", "Failed to Add", data.message);

        // Reset button for retry
        resetButton(buttonElement);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showToast(
        "error",
        "Connection Error",
        "Network error. Please check your connection and try again."
      );
      resetButton(buttonElement);
    });
}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

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

// Terms and Conditions Functions
function initTermsAndConditions() {
  const termsCheckbox = document.getElementById("terms");
  const registerBtn = document.getElementById("register-btn");
  const termsError = document.getElementById("terms-error");
  const registerForm = document.querySelector(
    "#register-form-container .auth-form"
  );

  // Enable/disable register button based on terms acceptance
  function toggleRegisterButton() {
    if (termsCheckbox.checked) {
      registerBtn.disabled = false;
      termsError.classList.remove("show");
      termsError.textContent = "";
    } else {
      registerBtn.disabled = true;
    }
  }

  // Validate terms on form submission
  function validateTerms() {
    if (!termsCheckbox.checked) {
      termsError.textContent =
        "You must accept the Terms and Conditions to continue";
      termsError.classList.add("show");
      return false;
    }
    termsError.classList.remove("show");
    return true;
  }

  // Real-time validation
  termsCheckbox.addEventListener("change", toggleRegisterButton);

  // Form submission validation
  registerForm.addEventListener("submit", function (e) {
    if (!validateTerms()) {
      e.preventDefault();

      // Add shake animation to terms checkbox
      termsCheckbox.parentElement.style.animation = "shake 0.5s ease-in-out";
      setTimeout(() => {
        termsCheckbox.parentElement.style.animation = "";
      }, 500);
    }
  });

  // Initialize button state
  toggleRegisterButton();
}

// Toast Notification Functions for Terms
function initTermsToast() {
  const toastContainer = document.getElementById("toast-container");

  function showTermsToast(title, message, duration = 5000) {
    const toast = document.createElement("div");
    toast.className = "toast terms-toast";
    toast.innerHTML = `
      <div class="toast-icon">📄</div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;

    toastContainer.appendChild(toast);

    // Trigger animation
    setTimeout(() => toast.classList.add("show"), 100);

    // Auto remove after duration
    if (duration > 0) {
      setTimeout(() => {
        if (toast.parentElement) {
          toast.classList.remove("show");
          setTimeout(() => toast.remove(), 300);
        }
      }, duration);
    }

    return toast;
  }

  return { showTermsToast };
}

// Terms Links Handler - Updated for new HTML pages
function initTermsLinks() {
  document.querySelectorAll(".terms-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const type = this.getAttribute("data-type");

      if (type === "terms") {
        // Open terms.html in a new tab
        window.open("terms.html", "_blank", "width=900,height=700");
      } else if (type === "privacy") {
        // Open privacy.html in a new tab
        window.open("privacy.html", "_blank", "width=900,height=700");
      }
    });
  });
}

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  initTermsAndConditions();
  initTermsLinks();
});

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  initTermsAndConditions();
  initTermsLinks();
});
