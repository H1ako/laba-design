document.addEventListener("DOMContentLoaded", function () {
  initUserSearch();
  initPagination();
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
