document.addEventListener("DOMContentLoaded", function () {
  initUserSearch();
  initPagination();
  initUserDelete();
  initAdminDelete();
});

/**
 * Initialize user search functionality
 */
function initUserSearch() {
  const searchForm = document.getElementById("user-search-form");

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
 * Initialize pagination functionality
 */
function initPagination() {
  const pagination = document.querySelector(".pagination");

  if (pagination) {
    pagination.addEventListener("click", function (e) {
      if (e.target.tagName === "A" && e.target.dataset.page) {
        e.preventDefault();

        const page = e.target.dataset.page;
        const currentUrl = new URL(window.location.href);

        currentUrl.searchParams.set("page", page);
        window.location.href = currentUrl.toString();
      }
    });
  }
}

/**
 * Initialize customer user deletion
 */
function initUserDelete() {
  const deleteButtons = document.querySelectorAll("[data-user-delete]");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", async function (e) {
      e.preventDefault();

      const userId = this.getAttribute("data-user-id");
      const confirmMessage =
        this.getAttribute("data-confirm") ||
        "Are you sure you want to delete this user?";

      if (!confirm(confirmMessage)) {
        return;
      }

      try {
        const response = await fetch(`/api/admin/users/${userId}`, {
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
          // Remove the user from the table
          const userRow = document.querySelector(
            `tr[data-user-id="${userId}"]`
          );
          if (userRow) {
            userRow.remove();
          }

          // Show success notification
          if (typeof showNotification === "function") {
            showNotification(
              result.message || "User deleted successfully",
              "success"
            );
          } else {
            alert("User deleted successfully");
          }
        } else {
          // Show error notification
          if (typeof showNotification === "function") {
            showNotification(
              result.message || "Failed to delete user",
              "error"
            );
          } else {
            alert(
              "Failed to delete user: " + (result.message || "Unknown error")
            );
          }
        }
      } catch (error) {
        console.error("Error deleting user:", error);

        if (typeof showNotification === "function") {
          showNotification(
            "An error occurred while deleting the user",
            "error"
          );
        } else {
          alert("An error occurred while deleting the user");
        }
      }
    });
  });
}

/**
 * Initialize admin user deletion
 */
function initAdminDelete() {
  const deleteButtons = document.querySelectorAll("[data-admin-delete]");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", async function (e) {
      e.preventDefault();

      const adminId = this.getAttribute("data-admin-id");
      const confirmMessage =
        this.getAttribute("data-confirm") ||
        "Are you sure you want to delete this administrator?";

      if (!confirm(confirmMessage)) {
        return;
      }

      try {
        const response = await fetch(`/api/admin/users/admin/${adminId}`, {
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
          // Remove the admin from the table
          const adminRow = document.querySelector(
            `tr[data-admin-id="${adminId}"]`
          );
          if (adminRow) {
            adminRow.remove();
          }

          // Show success notification
          if (typeof showNotification === "function") {
            showNotification(
              result.message || "Administrator deleted successfully",
              "success"
            );
          } else {
            alert("Administrator deleted successfully");
          }
        } else {
          // Show error notification
          if (typeof showNotification === "function") {
            showNotification(
              result.message || "Failed to delete administrator",
              "error"
            );
          } else {
            alert(
              "Failed to delete administrator: " +
                (result.message || "Unknown error")
            );
          }
        }
      } catch (error) {
        console.error("Error deleting administrator:", error);

        if (typeof showNotification === "function") {
          showNotification(
            "An error occurred while deleting the administrator",
            "error"
          );
        } else {
          alert("An error occurred while deleting the administrator");
        }
      }
    });
  });
}
