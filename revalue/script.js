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
            console.log(
                "User is logged in - register form elements not available"
            );
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

function showToast(message, type = "success", title = null) {
    const toast = document.getElementById("toast");
    const toastIcon = toast.querySelector(".toast-icon");
    const toastTitle = toast.querySelector(".toast-title");
    const toastMessage = toast.querySelector(".toast-message");
    const toastProgress = toast.querySelector(".toast-progress");

    // Set content - following blueprint design
    toastMessage.textContent = message;

    // Set type and icon
    toast.className = `toast ${type}`;

    if (type === "success") {
        toastIcon.textContent = "✓";
        toastTitle.textContent = title || "Success";
        toastTitle.style.display = "block";
    } else if (type === "error") {
        toastIcon.textContent = "✕";
        toastTitle.textContent = title || "Error";
        toastTitle.style.display = "block";
    }

    // Prepare animation
    toast.style.display = "block";
    toast.offsetHeight; // Force reflow before adding 'show'
    toast.classList.add("show");

    // Reset progress animation
    toastProgress.style.animation = "none";
    toastProgress.offsetHeight; // Reflow again
    toastProgress.style.animation = "toastProgress 4s linear forwards";

    // Auto hide after 4 seconds
    setTimeout(() => {
        hideToast();
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
    const registerContainer = document.getElementById(
        "register-form-container"
    );
    const authOverlay = document.getElementById("auth-overlay");

    if (loginContainer && registerContainer && authOverlay) {
        console.log("Switching to register form");
        loginContainer.style.display = "none";
        registerContainer.style.display = "block";
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
    const registerContainer = document.getElementById(
        "register-form-container"
    );
    const authOverlay = document.getElementById("auth-overlay");

    if (loginContainer && registerContainer && authOverlay) {
        console.log("Switching to login form");
        registerContainer.style.display = "none";
        loginContainer.style.display = "block";
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
                showToast(data.message, "success", "Success");

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
                showToast(data.message, "error", "Failed to Add");

                // Re-enable button with error styling
                buttonElement.disabled = false;
                buttonElement.textContent = "Try Again";
                buttonElement.style.opacity = "1";
                buttonElement.style.background =
                    "hsl(var(--destructive) / 0.1)";
                buttonElement.style.border =
                    "1px solid hsl(var(--destructive) / 0.3)";
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
                "Network error. Please check your connection and try again.",
                "error",
                "Connection Error"
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
