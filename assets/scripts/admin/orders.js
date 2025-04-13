document.addEventListener("DOMContentLoaded", function () {
  initOrderStatusUpdate();
  initOrderDelete();
  initOrderItemRemove();
  initOrderFilters();
  initOrderSearch();
  initOrderEditForm();
});

/**
 * Initialize order status update functionality
 */
function initOrderStatusUpdate() {
  const statusButtons = document.querySelectorAll("[data-order-status-update]");

  statusButtons.forEach((button) => {
    button.addEventListener("click", async function () {
      const orderId = this.getAttribute("data-order-id");
      const status = this.getAttribute("data-status");

      try {
        const response = await fetch(`/api/admin/orders/${orderId}/status`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token":
              document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "",
          },
          body: JSON.stringify({ status }),
        });

        const result = await response.json();

        if (result.status === "success") {
          // Update status label on the page
          updateOrderStatusUI(orderId, status);
          showNotification("Order status updated successfully", "success");
        } else {
          showNotification(
            result.message || "Failed to update order status",
            "error"
          );
        }
      } catch (error) {
        console.error("Error updating order status:", error);
        showNotification(
          "An error occurred while updating the order status",
          "error"
        );
      }
    });
  });
}

/**
 * Update order status UI elements
 */
function updateOrderStatusUI(orderId, status) {
  // Update status badge
  const statusBadge = document.querySelector(
    `[data-order-id="${orderId}"] .order-status`
  );
  if (statusBadge) {
    // Remove all status classes
    statusBadge.classList.remove(
      "order-status--initial",
      "order-status--working",
      "order-status--success",
      "order-status--canceled"
    );
    // Add new status class
    statusBadge.classList.add(`order-status--${status}`);

    // Update text
    let statusText = "";
    switch (status) {
      case "initial":
        statusText = "Создан";
        break;
      case "working":
        statusText = "В работе";
        break;
      case "success":
        statusText = "Выполнен";
        break;
      case "canceled":
        statusText = "Отменен";
        break;
    }
    statusBadge.textContent = statusText;
  }

  // Highlight all status buttons
  const statusButtons = document.querySelectorAll(
    `[data-order-id="${orderId}"][data-order-status-update]`
  );
  statusButtons.forEach((btn) => {
    btn.classList.toggle("active", btn.getAttribute("data-status") === status);
  });
}

/**
 * Initialize order deletion functionality
 */
function initOrderDelete() {
  const deleteButtons = document.querySelectorAll("[data-order-delete]");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", async function (e) {
      e.preventDefault();

      const orderId = this.getAttribute("data-order-id");

      if (
        !confirm(
          "Are you sure you want to delete this order? This action cannot be undone."
        )
      ) {
        return;
      }

      try {
        const response = await fetch(`/api/admin/orders/${orderId}`, {
          method: "DELETE",
          headers: {
            "X-CSRF-Token":
              document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "",
          },
        });

        const result = await response.json();

        if (result.status === "success") {
          // Remove the order from the page or redirect
          if (result.redirect) {
            window.location.href = result.redirect;
          } else {
            const orderElement = document.querySelector(
              `[data-order-id="${orderId}"]`
            );
            if (orderElement) {
              orderElement.remove();
              showNotification("Order deleted successfully", "success");
            }
          }
        } else {
          showNotification(result.message || "Failed to delete order", "error");
        }
      } catch (error) {
        console.error("Error deleting order:", error);
        showNotification("An error occurred while deleting the order", "error");
      }
    });
  });
}

/**
 * Initialize order item removal functionality
 */
function initOrderItemRemove() {
  const removeButtons = document.querySelectorAll("[data-order-item-remove]");

  removeButtons.forEach((button) => {
    button.addEventListener("click", async function (e) {
      e.preventDefault();

      const orderId = this.getAttribute("data-order-id");
      const itemId = this.getAttribute("data-item-id");

      if (
        !confirm("Are you sure you want to remove this item from the order?")
      ) {
        return;
      }

      try {
        const response = await fetch(
          `/api/admin/orders/${orderId}/items/${itemId}`,
          {
            method: "DELETE",
            headers: {
              "X-CSRF-Token":
                document
                  .querySelector('meta[name="csrf-token"]')
                  ?.getAttribute("content") || "",
            },
          }
        );

        const result = await response.json();

        if (result.status === "success") {
          // Remove the item from the page or redirect
          if (result.redirect) {
            window.location.href = result.redirect;
          } else {
            const itemElement = document.querySelector(
              `[data-item-id="${itemId}"]`
            );
            if (itemElement) {
              itemElement.remove();
              showNotification(
                "Item removed from order successfully",
                "success"
              );

              // Update order totals if appropriate functions exist
              if (typeof updateOrderTotals === "function") {
                updateOrderTotals();
              }
            }
          }
        } else {
          showNotification(
            result.message || "Failed to remove item from order",
            "error"
          );
        }
      } catch (error) {
        console.error("Error removing order item:", error);
        showNotification("An error occurred while removing the item", "error");
      }
    });
  });
}

/**
 * Initialize order filters
 */
function initOrderFilters() {
  const statusFilter = document.getElementById("status-filter");

  if (statusFilter) {
    statusFilter.addEventListener("change", function () {
      const currentUrl = new URL(window.location.href);

      if (this.value) {
        currentUrl.searchParams.set("status", this.value);
      } else {
        currentUrl.searchParams.delete("status");
      }

      // Reset to first page when filter changes
      currentUrl.searchParams.delete("page");

      window.location.href = currentUrl.toString();
    });
  }
}

/**
 * Initialize order search
 */
function initOrderSearch() {
  const searchForm = document.getElementById("order-search-form");

  if (searchForm) {
    searchForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const searchInput = this.querySelector('input[name="search"]');
      const currentUrl = new URL(window.location.href);

      if (searchInput.value.trim()) {
        currentUrl.searchParams.set("search", searchInput.value.trim());
      } else {
        currentUrl.searchParams.delete("search");
      }

      // Reset to first page when search changes
      currentUrl.searchParams.delete("page");

      window.location.href = currentUrl.toString();
    });
  }
}

/**
 * Initialize order edit form submission
 */
function initOrderEditForm() {
  const form = document.getElementById("order-form");

  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Reset any previous error states
    form.querySelectorAll(".is-invalid").forEach((el) => {
      el.classList.remove("is-invalid");
    });
    form.querySelectorAll(".invalid-feedback").forEach((el) => {
      el.style.display = "none";
      el.textContent = "";
    });

    const formData = new FormData(form);
    const data = {};

    formData.forEach((value, key) => {
      data[key] = value;
    });

    fetch(form.action, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token":
          document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "",
      },
      body: JSON.stringify(data),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.status === "success") {
          // Use the redirect from result, or if not available, construct URL from current path
          // window.location.href =
          //   result.redirect || window.location.pathname.replace("/edit", "");

          // Show success notification if available in the global scope
          if (typeof showNotification === "function") {
            showNotification("Order updated successfully", "success");
          }
        } else {
          // Handle validation errors
          const errors = result.errors || {};

          Object.keys(errors).forEach((field) => {
            const errorElement = document.querySelector(
              `[data-error-for="${field}"]`
            );
            if (errorElement) {
              errorElement.textContent = errors[field];
              errorElement.style.display = "block";

              const inputElement = document.getElementById(field);
              if (inputElement) {
                inputElement.classList.add("is-invalid");
              }
            }
          });

          // Show error notification if available
          if (typeof showNotification === "function") {
            showNotification("Please correct the errors in the form", "error");
          }
        }
      })
      .catch((error) => {
        console.error("Error:", error);

        if (typeof showNotification === "function") {
          showNotification("An error occurred while saving the order", "error");
        } else {
          alert("Произошла ошибка при сохранении заказа");
        }
      });
  });
}
