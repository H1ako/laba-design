document.addEventListener("DOMContentLoaded", function () {
  initPagination();
});

/**
 * Initialize pagination functionality
 */
function initPagination() {
  const pagination = document.querySelector(".pagination-controls");

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
