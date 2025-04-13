document.addEventListener("DOMContentLoaded", function () {
  initRichTextEditor();
  initFileInputPreview();
  initNewsForm();
  initNewsDelete();
  initPagination();
  initNewsSearch();
});

/**
 * Initialize rich text editor for content field
 */
function initRichTextEditor() {
  const contentField = document.getElementById("content");
  if (!contentField) return;

  // Load EasyMDE if not already loaded
  if (typeof EasyMDE === "undefined") {
    const script = document.createElement("script");
    script.src = "https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js";
    script.onload = function () {
      createEditor(contentField);
    };
    document.head.appendChild(script);
  } else {
    createEditor(contentField);
  }

  function createEditor(textarea) {
    const easyMDE = new EasyMDE({
      element: textarea,
      autofocus: true,
      spellChecker: false,
      autoDownloadFontAwesome: true,
      status: ["lines", "words"],
      uploadImage: true,
      toolbar: [
        "bold",
        "italic",
        "heading",
        "|",
        "quote",
        "unordered-list",
        "ordered-list",
        "|",
        "link",
        "image",
        "table",
        "|",
        "preview",
        "side-by-side",
        "fullscreen",
        "|",
        "guide",
      ],
      imageUploadFunction: function (file, onSuccess, onError) {
        // Create FormData for file upload
        const formData = new FormData();
        formData.append("image", file);

        // Get CSRF token
        const csrfToken =
          document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

        // Upload image
        fetch("/api/admin/upload/image", {
          method: "POST",
          headers: {
            "X-CSRF-Token": csrfToken,
          },
          body: formData,
        })
          .then((response) => response.json())
          .then((result) => {
            if (result.status === "success") {
              onSuccess(result.url);
            } else {
              onError(result.message || "Image upload failed");
            }
          })
          .catch((error) => {
            console.error("Error uploading image:", error);
            onError("Upload failed");
          });
      },
    });

    // Save editor reference for form submission
    textarea._editor = easyMDE;
  }
}

/**
 * Initialize file input preview functionality
 */
function initFileInputPreview() {
  const thumbInput = document.getElementById("thumb");
  if (!thumbInput) return;

  const previewImage = document.querySelector(".file-preview-image");
  const placeholder = document.querySelector(".file-preview-placeholder");

  thumbInput.addEventListener("change", function () {
    if (this.files && this.files[0]) {
      const reader = new FileReader();

      reader.onload = function (e) {
        if (previewImage) {
          previewImage.src = e.target.result;
          previewImage.style.display = "block";
        }

        if (placeholder) {
          placeholder.style.display = "none";
        }
      };

      reader.readAsDataURL(this.files[0]);
    }
  });
}

/**
 * Initialize news form submission
 */
function initNewsForm() {
  const newsForm = document.getElementById("news-form");
  if (!newsForm) return;

  newsForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    // Reset validation errors
    newsForm.querySelectorAll(".invalid-feedback").forEach((el) => {
      el.textContent = "";
      el.style.display = "none";
    });

    newsForm.querySelectorAll(".is-invalid").forEach((el) => {
      el.classList.remove("is-invalid");
    });

    // Get EasyMDE content if exists
    const contentField = document.getElementById("content");
    if (contentField && contentField._editor) {
      contentField.value = contentField._editor.value();
    }

    // Prepare form data
    const formData = new FormData(newsForm);

    // Set publish status if checkbox not checked
    if (!formData.has("is_published")) {
      formData.append("is_published", "0");
    }

    // Disable submit button and show loading indicator
    const submitButton = newsForm.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...';

    try {
      // Determine if this is create or edit mode
      const newsId = newsForm.getAttribute("data-news-id");
      const isEdit = !!newsId;
      const apiUrl = isEdit ? `/api/admin/news/${newsId}` : "/api/admin/news";

      // Send the data
      const response = await fetch(apiUrl, {
        method: "POST",
        headers: {
          "X-CSRF-Token":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        body: formData,
      });

      const result = await response.json();

      if (result.status === "success") {
        // Show success notification
        if (typeof showNotification === "function") {
          showNotification(
            isEdit ? "Новость успешно обновлена" : "Новость успешно создана",
            "success"
          );
        } else {
          alert(
            isEdit ? "Новость успешно обновлена" : "Новость успешно создана"
          );
        }

        // Redirect to the new page
        if (result.redirect) {
          window.location.href = result.redirect;
        }
      } else {
        // Handle validation errors
        const errors = result.errors || {};

        Object.keys(errors).forEach((field) => {
          const errorElement = document.querySelector(
            `[data-error-for="${field}"]`
          );
          const inputElement = document.getElementById(field);

          if (errorElement) {
            errorElement.textContent = errors[field];
            errorElement.style.display = "block";
          }

          if (inputElement) {
            inputElement.classList.add("is-invalid");
          }
        });

        // Show error notification
        if (typeof showNotification === "function") {
          showNotification(
            result.message || "Пожалуйста, исправьте ошибки в форме",
            "error"
          );
        }
      }
    } catch (error) {
      console.error("Error submitting form:", error);

      if (typeof showNotification === "function") {
        showNotification("Произошла ошибка при сохранении новости", "error");
      } else {
        alert("Произошла ошибка при сохранении новости");
      }
    } finally {
      // Re-enable submit button
      submitButton.disabled = false;
      submitButton.innerHTML = originalButtonText;
    }
  });
}

/**
 * Initialize news deletion functionality
 */
function initNewsDelete() {
  const deleteButtons = document.querySelectorAll("[data-news-delete]");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", async function () {
      const newsId = this.getAttribute("data-news-id");
      const confirmMessage =
        this.getAttribute("data-confirm") ||
        "Вы уверены, что хотите удалить эту новость?";

      if (!confirm(confirmMessage)) return;

      try {
        const response = await fetch(`/api/admin/news/${newsId}`, {
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
          // Show success notification
          if (typeof showNotification === "function") {
            showNotification("Новость успешно удалена", "success");
          } else {
            alert("Новость успешно удалена");
          }

          // Redirect or remove element from page
          if (window.location.pathname.includes(`/admin/news/${newsId}`)) {
            window.location.href = "/admin/news";
          } else {
            const newsElement = document.querySelector(
              `.news-card[data-news-id="${newsId}"]`
            );
            if (newsElement) {
              newsElement.remove();
            }
          }
        } else {
          // Show error notification
          if (typeof showNotification === "function") {
            showNotification(
              result.message || "Ошибка при удалении новости",
              "error"
            );
          } else {
            alert(result.message || "Ошибка при удалении новости");
          }
        }
      } catch (error) {
        console.error("Error deleting news:", error);

        if (typeof showNotification === "function") {
          showNotification("Произошла ошибка при удалении новости", "error");
        } else {
          alert("Произошла ошибка при удалении новости");
        }
      }
    });
  });
}

/**
 * Initialize pagination functionality
 */
function initPagination() {
  const pagination = document.querySelector(".pagination");

  if (pagination) {
    pagination.addEventListener("click", function (e) {
      if (e.target.tagName === "A" && e.target.getAttribute("href")) {
        // The links already have the proper href with pagination params
        // Let the browser handle the navigation
      }
    });
  }
}

/**
 * Initialize news search functionality
 */
function initNewsSearch() {
  const searchForm = document.getElementById("news-search-form");

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
