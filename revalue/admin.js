// Navigation functionality
const navItems = document.querySelectorAll(".nav-item");
const contentSections = document.querySelectorAll(".content-section");

navItems.forEach((item) => {
  item.addEventListener("click", function () {
    const targetSection = this.getAttribute("data-section");

    // Handle logout
    if (targetSection === "logout") {
      if (confirm("Are you sure you want to logout?")) {
        alert("Logging out...");
        // Add your logout logic here
        window.location.href = "logout.php";
      }
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
  // Navigate to notifications section
  document.querySelector('[data-section="notifications"]').click();
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
const tableRows = document.querySelectorAll(".orders-table tbody tr");
tableRows.forEach((row) => {
  row.style.cursor = "pointer";
  row.addEventListener("click", function () {
    const product = this.querySelector(".product-cell span").textContent;
    console.log("Order clicked:", product);
    // Add order details modal or navigation here
  });

  row.addEventListener("mouseenter", function () {
    this.style.backgroundColor = "#f8f9fa";
  });

  row.addEventListener("mouseleave", function () {
    this.style.backgroundColor = "";
  });
});

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

// Export functions for PHP integration
window.dashboardAPI = {
  updateValues: updateDashboardValues,
  addOrder: addOrderRow,
  navigateToSection: function (sectionName) {
    const navItem = document.querySelector(`[data-section="${sectionName}"]`);
    if (navItem) navItem.click();
  },
};
