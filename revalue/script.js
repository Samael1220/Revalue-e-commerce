function openModal() {
  document.getElementById("auth-overlay").style.display = "grid";
  // Lock background scroll
  document.body.style.overflow = "hidden";
}

function closeModal() {
  document.getElementById("auth-overlay").style.display = "none";
  // Unlock background scroll if no other modal is open
  if (document.getElementById("register-overlay").style.display === "none") {
    document.body.style.overflow = "";
  }
}

function openRegisterModal() {
  // Ensure the login modal is closed to avoid overlap
  closeModal();
  document.getElementById("register-overlay").style.display = "grid";
  // Lock background scroll
  document.body.style.overflow = "hidden";
}
function closeRegisterModal() {
  document.getElementById("register-overlay").style.display = "none";
  // Unlock background scroll if no other modal is open
  if (document.getElementById("auth-overlay").style.display === "none") {
    document.body.style.overflow = "";
  }
}

// Open modal on page load
document.addEventListener("DOMContentLoaded", function () {
  openModal();
  // Prevent default jumps for '#' links to avoid layout shifts
  document.querySelectorAll('a[href="#"]').forEach(function (a) {
    a.addEventListener("click", function (e) {
      e.preventDefault();
    });
  });
  // Toggle embedded register container inside login modal
  var embeddedShow = document.getElementById("link-show-register-embedded");
  var embeddedBack = document.getElementById("embedded-reg-to-login");
  var embeddedPanel = document.querySelector(
    "#auth-overlay .register-container"
  );
  if (embeddedShow && embeddedPanel) {
    embeddedShow.addEventListener("click", function () {
      embeddedPanel.style.display = "block";
      var scroller = embeddedPanel.closest(".form-content");
      if (scroller) scroller.scrollTop = 0;
    });
  }
  if (embeddedBack && embeddedPanel) {
    embeddedBack.addEventListener("click", function () {
      embeddedPanel.style.display = "none";
      var scroller = document.querySelector("#auth-overlay .form-content");
      if (scroller) scroller.scrollTop = 0;
    });
  }
});

// Close modal when clicking outside
document.getElementById("auth-overlay").addEventListener("click", function (e) {
  if (e.target === this) {
    closeModal();
  }
});
document
  .getElementById("register-overlay")
  .addEventListener("click", function (e) {
    if (e.target === this) {
      closeRegisterModal();
    }
  });

// Close modal with Escape key
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    closeModal();
    closeRegisterModal();
  }
});

// Return-to-login links inside the register modal
/*document.addEventListener("click", function (e) {
  var backLink = e.target.closest("#reg-to-login");
  if (backLink) {
    e.preventDefault();
    // Clean swap: close register, then open login after paint
    closeRegisterModal();
    setTimeout(function () {
      openModal();
      // Reset scroll to top of login form for consistent initial layout
      var loginScroller = document.querySelector("#auth-overlay .form-content");
      if (loginScroller) loginScroller.scrollTop = 0;
    }, 50);
  }
});*/
