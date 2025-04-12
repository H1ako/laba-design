document.addEventListener("DOMContentLoaded", function () {
  initSidebar();
  initNotifications();
  initTooltips();
  initConfirmDialogs();
});

/**
 * Initialize the sidebar functionality
 */
function initSidebar() {
  const sidebarToggle = document.getElementById("sidebar-toggle");
  const adminLayout = document.querySelector(".admin-layout");

  // Check if sidebar state is saved in localStorage
  const sidebarCollapsed =
    localStorage.getItem("admin_sidebar_collapsed") === "true";
  if (sidebarCollapsed) {
    adminLayout.classList.add("sidebar-collapsed");
  }

  // Toggle sidebar when button is clicked
  sidebarToggle?.addEventListener("click", function () {
    adminLayout.classList.toggle("sidebar-collapsed");
    // Save state to localStorage
    localStorage.setItem(
      "admin_sidebar_collapsed",
      adminLayout.classList.contains("sidebar-collapsed")
    );
  });

  // Collapse sidebar automatically on small screens
  function checkScreenSize() {
    if (
      window.innerWidth < 768 &&
      !adminLayout.classList.contains("sidebar-collapsed")
    ) {
      adminLayout.classList.add("sidebar-collapsed");
      localStorage.setItem("admin_sidebar_collapsed", "true");
    }
  }

  // Check on load and resize
  checkScreenSize();
  window.addEventListener("resize", checkScreenSize);
}

/**
 * Initialize notification system
 */
function initNotifications() {
  const container = document.getElementById("notifications-container");
  if (!container) {
    // Create notifications container if it doesn't exist
    const notifyContainer = document.createElement("div");
    notifyContainer.id = "notifications-container";
    document.body.appendChild(notifyContainer);
  }

  // Check for flash messages and display them
  const flashMessages = document.querySelectorAll(".flash-message");
  flashMessages.forEach((message) => {
    const type = message.dataset.type || "info";
    const text = message.textContent;
    showNotification(text, type);
    message.remove();
  });
}

/**
 * Show a notification message
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, info, warning)
 * @param {number} duration - Duration in ms to show the notification
 */
function showNotification(message, type = "info", duration = 5000) {
  const container = document.getElementById("notifications-container");
  if (!container) return;

  const notification = document.createElement("div");
  notification.className = `notification notification--${type}`;

  const icon = getNotificationIcon(type);

  notification.innerHTML = `
      <div class="notification__icon">${icon}</div>
      <div class="notification__content">${message}</div>
      <button class="notification__close">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    `;

  container.appendChild(notification);

  // Animate entrance
  setTimeout(() => {
    notification.classList.add("visible");
  }, 10);

  // Add close functionality
  const closeBtn = notification.querySelector(".notification__close");
  closeBtn.addEventListener("click", () => {
    closeNotification(notification);
  });

  // Auto-close after duration
  if (duration > 0) {
    setTimeout(() => {
      closeNotification(notification);
    }, duration);
  }
}

/**
 * Close a notification with animation
 * @param {Element} notification - The notification element to close
 */
function closeNotification(notification) {
  notification.classList.remove("visible");
  notification.classList.add("hiding");

  setTimeout(() => {
    notification.remove();
  }, 300);
}

/**
 * Get SVG icon for notification type
 * @param {string} type - The type of notification
 * @returns {string} - SVG markup for the icon
 */
function getNotificationIcon(type) {
  switch (type) {
    case "success":
      return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>`;
    case "error":
      return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="15" y1="9" x2="9" y2="15"></line>
                  <line x1="9" y1="9" x2="15" y2="15"></line>
                </svg>`;
    case "warning":
      return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                  <line x1="12" y1="9" x2="12" y2="13"></line>
                  <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>`;
    default: // info
      return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>`;
  }
}

/**
 * Initialize tooltips for any elements with [data-tooltip] attribute
 */
function initTooltips() {
  const tooltipElements = document.querySelectorAll("[data-tooltip]");

  tooltipElements.forEach((element) => {
    const tooltipText = element.getAttribute("data-tooltip");

    element.addEventListener("mouseenter", function (e) {
      const tooltip = document.createElement("div");
      tooltip.className = "tooltip";
      tooltip.textContent = tooltipText;
      document.body.appendChild(tooltip);

      // Position the tooltip
      const rect = element.getBoundingClientRect();
      const tooltipHeight = tooltip.offsetHeight;
      const tooltipWidth = tooltip.offsetWidth;

      tooltip.style.top = `${rect.top - tooltipHeight - 5 + window.scrollY}px`;
      tooltip.style.left = `${rect.left + rect.width / 2 - tooltipWidth / 2}px`;

      // Show the tooltip
      setTimeout(() => {
        tooltip.classList.add("visible");
      }, 10);

      // Store reference to the tooltip
      element._tooltip = tooltip;
    });

    element.addEventListener("mouseleave", function () {
      if (element._tooltip) {
        element._tooltip.remove();
        element._tooltip = null;
      }
    });
  });
}

/**
 * Initialize tab navigation
 */
function initTabNavigation() {
  const tabsContainer = document.querySelector('.tabs');
  if (!tabsContainer) return;

  const tabButtons = tabsContainer.querySelectorAll('.tab-btn');
  const tabPanes = tabsContainer.querySelectorAll('.tab-pane');

  tabButtons.forEach(button => {
    button.addEventListener('click', function() {
      const tab = this.dataset.tab;
      
      // Deactivate all tabs
      tabButtons.forEach(btn => btn.classList.remove('active'));
      tabPanes.forEach(pane => pane.classList.remove('active'));
      
      // Activate the selected tab
      this.classList.add('active');
      document.getElementById(`${tab}-tab`)?.classList.add('active');
      
      // Save active tab in localStorage
      const path = window.location.pathname;
      localStorage.setItem(`active_tab_${path}`, tab);
    });
  });

  // Check if there's a saved active tab
  const path = window.location.pathname;
  const savedTab = localStorage.getItem(`active_tab_${path}`);
  
  if (savedTab) {
    const tabButton = tabsContainer.querySelector(`.tab-btn[data-tab="${savedTab}"]`);
    if (tabButton) {
      tabButton.click();
    }
  }
}

/**
 * Initialize file input previews
 */
function initFileInputs() {
  const fileInputs = document.querySelectorAll('.file-input');
  
  fileInputs.forEach(input => {
    const previewContainer = input.parentElement.querySelector('.file-preview');
    const previewImage = previewContainer?.querySelector('.file-preview-image');
    const placeholder = previewContainer?.querySelector('.file-preview-placeholder');
    
    input.addEventListener('change', function() {
      if (this.files && this.files[0] && previewContainer) {
        const file = this.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
          if (previewImage) {
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';
          }
          
          if (placeholder) {
            placeholder.style.display = 'none';
          }
          
          previewContainer.classList.add('has-file');
        };
        
        reader.readAsDataURL(file);
      }
    });
    
    // Enable drag and drop
    if (previewContainer) {
      previewContainer.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
      });
      
      previewContainer.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
      });
      
      previewContainer.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0 && input) {
          input.files = files;
          
          // Trigger change event
          const event = new Event('change', { bubbles: true });
          input.dispatchEvent(event);
        }
      });
      
      // Click to select file
      previewContainer.addEventListener('click', function() {
        input.click();
      });
    }
  });
}

/**
 * Initialize confirmation dialogs for elements with [data-confirm] attribute
 */
function initConfirmDialogs() {
  const confirmElements = document.querySelectorAll('[data-confirm]');

  confirmElements.forEach((element) => {
    element.addEventListener('click', function(e) {
      const message = this.getAttribute('data-confirm') || 'Вы уверены?';
      if (!confirm(message)) {
        e.preventDefault();
        e.stopPropagation();
      }
    });
  });
}