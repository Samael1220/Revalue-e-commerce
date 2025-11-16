// Navigation functionality
const navItems = document.querySelectorAll(".nav-item");
const contentSections = document.querySelectorAll(".content-section");

navItems.forEach((item) => {
  item.addEventListener("click", function () {
    const targetSection = this.getAttribute("data-section");

    // Handle logout
    // Add this to your existing JavaScript
    const Toast = {
      container: null,
      overlay: null,

      init() {
        this.container = document.getElementById("toastContainer");
        this.overlay = document.getElementById("toastOverlay");
        this.overlay.addEventListener("click", () => this.hide());
      },

      show(options) {
        const { type = "info", title, message, actions = [] } = options;
        this.container.innerHTML = "";
        document.body.style.overflow = "hidden";

        const card = document.createElement("div");
        card.className = `toast-card ${type}`;

        const icons = {
          warning: '<i class="fas fa-exclamation-triangle"></i>',
          success: '<i class="fas fa-check-circle"></i>',
        };

        card.innerHTML = `
            <div class="toast-icon">${icons[type]}</div>
            ${title ? `<h2 class="toast-title">${title}</h2>` : ""}
            ${message ? `<p class="toast-message">${message}</p>` : ""}
            ${
              actions.length > 0
                ? `
                <div class="toast-actions">
                    ${actions
                      .map(
                        (action, index) => `
                        <button class="toast-btn toast-btn-${action.style}" data-action="${index}">
                            ${action.label}
                        </button>
                    `
                      )
                      .join("")}
                </div>
            `
                : ""
            }
        `;

        this.container.appendChild(card);

        actions.forEach((action, index) => {
          const btn = card.querySelector(`[data-action="${index}"]`);
          if (btn)
            btn.addEventListener(
              "click",
              () => action.onClick && action.onClick()
            );
        });

        setTimeout(() => {
          this.overlay.classList.add("show");
          this.container.classList.add("show");
        }, 10);
      },

      hide() {
        this.overlay.classList.remove("show");
        this.container.classList.remove("show");
        document.body.style.overflow = "";
      },

      showLoading(message = "Loading...") {
        this.container.innerHTML = `
            <div class="toast-card">
                <div class="toast-loading">
                    <div class="toast-spinner"></div>
                    <span style="color: var(--muted-foreground);">${message}</span>
                </div>
            </div>
        `;
        setTimeout(() => {
          this.overlay.classList.add("show");
          this.container.classList.add("show");
        }, 10);
        document.body.style.overflow = "hidden";
      },

      showSuccess(title, message) {
        this.show({ type: "success", title, message });
        setTimeout(() => this.hide(), 2000);
      },
    };

    Toast.init();

    function handleLogout() {
      Toast.show({
        type: "warning",
        title: "Confirm Logout",
        message: "Are you sure you want to logout?",
        actions: [
          {
            label: "Cancel",
            style: "secondary",
            onClick: () => Toast.hide(),
          },
          {
            label: "Logout",
            style: "primary",
            onClick: () => {
              Toast.showLoading("Logging out...");
              setTimeout(() => {
                Toast.showSuccess("Logged Out", "Redirecting...");
                setTimeout(() => (window.location.href = "logout.php"), 2000);
              }, 1000);
            },
          },
        ],
      });
    }

    // Update your existing nav click handler
    if (targetSection === "logout") {
      handleLogout();
      return;
    }

    // Remove active class from all nav items
    navItems.forEach((navItem) => {
      navItem.classList.remove("active");
    });

    // Add active class to clicked item
    this.classList.add("active");

    // Hide all content sections
    contentSections.forEach((section) => {
      section.classList.remove("active");
    });

    // Show the selected section
    const selectedSection = document.getElementById(targetSection);
    if (selectedSection) {
      selectedSection.classList.add("active");
    }
  });
});

// Search functionality
const searchInput = document.querySelector(".search-bar input");
searchInput.addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    const searchValue = this.value.trim();
    if (searchValue) {
      console.log("Searching for:", searchValue);
      // Add your search logic here
      alert(
        "Search functionality will be implemented with backend integration. Searching for: " +
          searchValue
      );
    }
  }
});

// Dropdown functionality
const sortDropdown = document.querySelector(".sort-dropdown");
sortDropdown.addEventListener("change", function () {
  console.log("Sort by:", this.value);
  // Add your sorting logic here
  // This will be connected to PHP backend later
});

// Notification button
const notificationBtn = document.querySelector(".icon-btn:nth-child(2)");
notificationBtn.addEventListener("click", function () {
  // Navigate to messaging section
  document.querySelector('[data-section="messaging"]').click();
});

// Add hover effect for stat cards
const statCards = document.querySelectorAll(".stat-card");
statCards.forEach((card) => {
  card.addEventListener("click", function () {
    console.log(
      "Stat card clicked:",
      this.querySelector(".stat-label").textContent
    );
    // Add navigation or modal logic here
  });
});

// Table row click handling

// Initialize tooltips for sidebar when not expanded
const sidebar = document.querySelector(".sidebar");
let sidebarHovered = false;

sidebar.addEventListener("mouseenter", function () {
  sidebarHovered = true;
});

sidebar.addEventListener("mouseleave", function () {
  sidebarHovered = false;
});

// Dynamic data that can be updated with PHP
const dashboardData = {
  ordersCompleted: "300K",
  ordersPending: "10K",
  ordersCancelled: "100K",
  totalUsers: "350K",
  totalVisitors: "650K",
  productViews: "10K",
  newOrders: "5K",
  cancelled: "2K",
  ordersReady: 25,
};

// Function to update dashboard values (will be called from PHP)
function updateDashboardValues(data) {
  // Update stat cards
  const statValues = document.querySelectorAll(".stat-value");
  if (data.ordersCompleted) statValues[0].textContent = data.ordersCompleted;
  if (data.ordersPending) statValues[1].textContent = data.ordersPending;
  if (data.ordersCancelled) statValues[2].textContent = data.ordersCancelled;
  if (data.totalUsers) statValues[3].textContent = data.totalUsers;

  // Update side stats
  const sideStatValues = document.querySelectorAll(".side-stat-value");
  if (data.totalVisitors) sideStatValues[0].textContent = data.totalVisitors;
  if (data.productViews) sideStatValues[1].textContent = data.productViews;

  const subStatValues = document.querySelectorAll(".sub-stat-value");
  if (data.newOrders) subStatValues[0].textContent = data.newOrders;
  if (data.cancelled) subStatValues[1].textContent = data.cancelled;

  // Update orders ready count
  if (data.ordersReady) {
    document.querySelector(".orders-ready").textContent =
      data.ordersReady + "+ Orders Ready to be Shipped";
  }
}

// Function to add new order row (can be called from PHP)
function addOrderRow(orderData) {
  const tbody = document.querySelector(".orders-table tbody");
  const newRow = document.createElement("tr");

  const statusClass = orderData.status.toLowerCase().replace(" ", "-");

  newRow.innerHTML = `
                <td>
                    <div class="product-cell">
                        <div class="product-img" style="background: #f0f0f0;"></div>
                        <span>${orderData.product}</span>
                    </div>
                </td>
                <td>${orderData.quantity}x</td>
                <td><span class="status-badge ${statusClass}">${orderData.status}</span></td>
                <td>${orderData.date}</td>
                <td>${orderData.time}</td>
            `;

  tbody.appendChild(newRow);

  // Add click handler to new row
  newRow.style.cursor = "pointer";
  newRow.addEventListener("click", function () {
    console.log("Order clicked:", orderData.product);
  });

  newRow.addEventListener("mouseenter", function () {
    this.style.backgroundColor = "#f8f9fa";
  });

  newRow.addEventListener("mouseleave", function () {
    this.style.backgroundColor = "";
  });
}

// Example: Simulate real-time updates (remove this when connecting to PHP)
setTimeout(() => {
  console.log("Dashboard initialized. Ready for PHP integration.");
  // updateDashboardValues({
  //     ordersCompleted: '305K',
  //     ordersPending: '11K'
  // });
}, 2000);

// Toast notification system
function showToast(message, type = "success") {
  const toastContainer = document.getElementById("toast-container");
  if (!toastContainer) return;

  const toast = document.createElement("div");
  toast.className = `toast toast-${type}`;

  const icon =
    type === "success"
      ? "✅"
      : type === "error"
      ? "❌"
      : type === "warning"
      ? "⚠️"
      : "ℹ️";
  toast.innerHTML = `
    <div class="toast-content">
      <span class="toast-icon">${icon}</span>
      <span class="toast-message">${message}</span>
    </div>
    <button class="toast-close" onclick="closeToast(this)">×</button>
  `;

  toastContainer.appendChild(toast);

  // Auto remove after 5 seconds
  setTimeout(() => {
    if (toast.parentNode) {
      toast.classList.add("toast-hide");
      setTimeout(() => {
        if (toast.parentNode) {
          toastContainer.removeChild(toast);
        }
      }, 300);
    }
  }, 5000);
}

function closeToast(button) {
  const toast = button.parentNode;
  toast.classList.add("toast-hide");
  setTimeout(() => {
    if (toast.parentNode) {
      document.getElementById("toast-container").removeChild(toast);
    }
  }, 300);
}

// Show success toast on page load if success parameter is present
document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get("success") === "1") {
    setTimeout(() => {
      showToast("Product added successfully!", "success");
    }, 1000);
  }
});

// Export functions for PHP integration
window.dashboardAPI = {
  updateValues: updateDashboardValues,
  addOrder: addOrderRow,
  navigateToSection: function (sectionName) {
    const navItem = document.querySelector(`[data-section="${sectionName}"]`);
    if (navItem) navItem.click();
  },
  showToast: showToast,
};

// --- Messaging (admin) ---
document.addEventListener("DOMContentLoaded", function () {
  const listEl = document.getElementById("dm-user-list");
  const boxEl = document.getElementById("dm-box");
  const headerEl = document.getElementById("dm-header");
  const formEl = document.getElementById("dm-form");
  const inputEl = document.getElementById("dm-input");
  if (!listEl || !boxEl || !headerEl || !formEl || !inputEl) return; // Section not present

  let partnerId = 0;
  let lastId = 0;

  function appendMsg(m) {
    const div = document.createElement("div");
    const isReceived = m.sender_id === partnerId;
    div.style.maxWidth = "75%";
    div.style.margin = "6px 0";
    div.style.padding = "8px 10px";
    div.style.borderRadius = "10px";
    div.style.fontSize = "14px";
    div.style.lineHeight = "1.3";
    if (isReceived) {
      div.style.marginRight = "auto";
      div.style.background = "#fff";
      div.style.border = "1px solid #e5e7eb";
      div.style.color = "#111827";
    } else {
      div.style.marginLeft = "auto";
      div.style.background = "#dcfce7";
      div.style.color = "#065f46";
    }
    div.textContent = m.body;
    boxEl.appendChild(div);
    boxEl.scrollTop = boxEl.scrollHeight;
  }

  function fetchUsers() {
    listEl.innerHTML =
      '<div style="padding:10px; color:#6b7280;">Loading…</div>';
    fetch("chat_users.php")
      .then((r) => r.text())
      .then((txt) => {
        let data;
        try {
          data = JSON.parse(txt);
        } catch (e) {
          data = null;
        }
        if (!data || !data.users) {
          listEl.innerHTML =
            '<div style="padding:10px; color:#ef4444;">Failed to load users</div>';
          console.warn("chat_users raw:", txt);
          return;
        }
        listEl.innerHTML = "";
        if (!data.users.length) {
          listEl.innerHTML =
            '<div style="padding:10px; color:#6b7280;">No users yet</div>';
          return;
        }
        let first = null;
        data.users.forEach((u) => {
          const item = document.createElement("div");
          item.textContent = u.name || u.email;
          item.style.padding = "10px";
          item.style.cursor = "pointer";
          item.style.borderBottom = "1px solid #f1f5f9";
          item.addEventListener("click", () => {
            partnerId = u.id;
            lastId = 0;
            headerEl.textContent = "Chat with " + (u.name || u.email);
            boxEl.innerHTML = "";
            fetchMessages();
          });
          if (!first) first = item;
          listEl.appendChild(item);
        });
        if (first) first.click();
      })
      .catch((err) => {
        listEl.innerHTML =
          '<div style="padding:10px; color:#ef4444;">Failed to load users</div>';
        console.error("chat_users error:", err);
      });
  }

  function fetchMessages() {
    if (!partnerId) return;
    fetch("chat_fetch.php?partner=" + partnerId + "&since=" + lastId)
      .then((r) => r.json())
      .then((data) => {
        if (data.success && data.messages) {
          data.messages.forEach((m) => {
            appendMsg(m);
            lastId = Math.max(lastId, parseInt(m.id));
          });
        }
      });
  }

  formEl.addEventListener("submit", function (e) {
    e.preventDefault();
    if (!partnerId) return;
    const v = inputEl.value.trim();
    if (!v) return;
    const fd = new FormData();
    fd.append("message", v);
    fd.append("to", partnerId);
    inputEl.value = "";
    fetch("chat_send.php", { method: "POST", body: fd })
      .then((r) => r.json())
      .then((data) => {
        if (data.success) {
          fetchMessages();
        }
      });
  });

  fetchUsers();
  setInterval(fetchMessages, 3000);
});

document.addEventListener("DOMContentLoaded", function () {
  const previewButtons = document.querySelectorAll(".notification-preview-btn");
  const modal = document.getElementById("notification-preview-modal");
  if (!modal) {
    return;
  }

  const gallery = document.getElementById("notification-preview-gallery");
  const emptyState = document.getElementById("notification-preview-empty");
  const title = document.getElementById("notification-preview-title");
  const subtitle = modal.querySelector(".notification-preview-subtitle");
  const closeBtn = modal.querySelector(".notification-preview-close");

  const openModal = ({
    images = [],
    orderId = "",
    customer = "",
    items = [],
  }) => {
    if (!gallery || !emptyState || !title) {
      return;
    }

    gallery.innerHTML = "";
    emptyState.style.display = "none";

    title.textContent = orderId ? `Order #${orderId} Photos` : "Order Photos";

    if (subtitle) {
      const summary =
        Array.isArray(items) && items.length
          ? `${customer ? `${customer} • ` : ""}${items
              .slice(0, 3)
              .join(", ")}${
              items.length > 3 ? ` (+${items.length - 3} more)` : ""
            }`
          : customer;
      subtitle.textContent = summary || "Customer confirmation preview";
    }

    if (Array.isArray(images) && images.length) {
      images.forEach((src, index) => {
        if (!src) {
          return;
        }
        const wrapper = document.createElement("div");
        const img = document.createElement("img");
        img.src = src;
        img.alt = `Order photo ${index + 1}`;
        img.loading = "lazy";
        wrapper.appendChild(img);
        gallery.appendChild(wrapper);
      });
    } else {
      emptyState.style.display = "flex";
    }

    modal.classList.add("show");
    modal.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
  };

  const closeModal = () => {
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
  };

  previewButtons.forEach((button) => {
    button.addEventListener("click", () => {
      let images = [];
      let items = [];

      const imageData = button.getAttribute("data-images");
      if (imageData) {
        try {
          const parsed = JSON.parse(imageData);
          if (Array.isArray(parsed)) {
            images = parsed;
          }
        } catch (error) {
          console.warn("Failed to parse notification images", error);
        }
      }

      const itemData = button.getAttribute("data-items");
      if (itemData) {
        try {
          const parsedItems = JSON.parse(itemData);
          if (Array.isArray(parsedItems)) {
            items = parsedItems;
          }
        } catch (error) {
          console.warn("Failed to parse notification items", error);
        }
      }

      openModal({
        images,
        items,
        orderId: button.getAttribute("data-order") || "",
        customer: button.getAttribute("data-customer") || "",
      });
    });
  });

  closeBtn?.addEventListener("click", closeModal);

  modal.addEventListener("click", (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && modal.classList.contains("show")) {
      closeModal();
    }
  });

  const manageLinks = document.querySelectorAll(".notification-link");
  const ordersNav = document.querySelector('.nav-item[data-section="orders"]');

  manageLinks.forEach((link) => {
    link.addEventListener("click", (event) => {
      event.preventDefault();
      ordersNav?.click();
      const ordersSection = document.getElementById("orders");
      if (ordersSection) {
        ordersSection.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });
});

document.querySelectorAll(".notif-view-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    const parent = this.closest(".notif-image-section");
    const img = parent.querySelector("img");

    if (img) {
      const modal = document.getElementById("imageModal");
      const modalImg = document.getElementById("modalImg");

      modalImg.src = img.src;
      modal.classList.add("show");
    }
  });
});

document.getElementById("closeModal").addEventListener("click", function () {
  document.getElementById("imageModal").classList.remove("show");
});

// Close modal on outside click
document.getElementById("imageModal").addEventListener("click", function (e) {
  if (e.target === this) {
    this.classList.remove("show");
  }
});
