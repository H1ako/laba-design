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

  // Create a container for Quill
  const quillContainer = document.createElement("div");
  quillContainer.id = "quill-editor";
  quillContainer.style.backgroundColor = "white";
  quillContainer.style.minHeight = "400px";
  quillContainer.innerHTML = contentField.value; // Transfer any existing content

  // Insert the container before the textarea and hide the textarea
  contentField.parentNode.insertBefore(quillContainer, contentField);
  contentField.style.display = "none";

  // Load Quill if not already loaded
  if (typeof Quill === "undefined") {
    // Load Quill CSS
    const quillCSS = document.createElement("link");
    quillCSS.rel = "stylesheet";
    quillCSS.href = "https://cdn.quilljs.com/1.3.7/quill.snow.css";
    document.head.appendChild(quillCSS);

    // Load Quill JS
    const script = document.createElement("script");
    script.src = "https://cdn.quilljs.com/1.3.7/quill.min.js";
    script.onload = function () {
      createEditor(contentField, quillContainer);
    };
    document.head.appendChild(script);
  } else {
    createEditor(contentField, quillContainer);
  }

  function createEditor(textarea, container) {
    // Get CSRF token
    const csrfToken =
      document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content") || "";

    // Setup image upload handler
    const imageHandler = function () {
      const input = document.createElement("input");
      input.setAttribute("type", "file");
      input.setAttribute("accept", "image/*");
      input.click();

      input.onchange = async () => {
        const file = input.files[0];
        if (file) {
          // Create FormData
          const formData = new FormData();
          formData.append("image", file);

          // Show loading state in editor
          const range = this.quill.getSelection(true);
          this.quill.insertText(
            range.index,
            "Загрузка изображения...",
            { italic: true },
            true
          );

          try {
            // Upload image
            const response = await fetch("/api/admin/upload/image", {
              method: "POST",
              headers: {
                "X-CSRF-Token": csrfToken,
              },
              body: formData,
            });

            const result = await response.json();

            // Remove loading text
            this.quill.deleteText(
              range.index,
              "Загрузка изображения...".length
            );

            if (result.status === "success") {
              // Insert the image
              this.quill.insertEmbed(range.index, "image", result.data.url);
            } else {
              // Show error message
              this.quill.insertText(
                range.index,
                "Ошибка загрузки изображения",
                { color: "red" },
                true
              );
            }
          } catch (error) {
            console.error("Error uploading image:", error);
            this.quill.deleteText(
              range.index,
              "Загрузка изображения...".length
            );
            this.quill.insertText(
              range.index,
              "Ошибка загрузки изображения",
              { color: "red" },
              true
            );
          }
        }
      };
    };

    // Initialize Quill
    const toolbarOptions = [
      ["bold", "italic", "underline", "strike"],
      ["blockquote", "code-block"],
      [{ header: 1 }, { header: 2 }],
      [{ list: "ordered" }, { list: "bullet" }],
      [{ script: "sub" }, { script: "super" }],
      [{ indent: "-1" }, { indent: "+1" }],
      [{ direction: "rtl" }],
      [{ size: ["small", false, "large", "huge"] }],
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      [{ color: [] }, { background: [] }],
      [{ font: [] }],
      [{ align: [] }],
      ["clean"],
      ["link", "image", "video"],
    ];

    const quill = new Quill(container, {
      modules: {
        toolbar: {
          container: toolbarOptions,
          handlers: {
            image: imageHandler,
          },
        },
      },
      placeholder: "Введите содержание...",
      theme: "snow",
    });

    // If there's existing content in HTML format
    if (textarea.value) {
      quill.root.innerHTML = textarea.value;
    }

    // Save editor reference for form submission
    textarea._editor = quill;
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

    // Get Quill content if editor exists
    const contentField = document.getElementById("content");
    if (contentField && contentField._editor) {
      contentField.value = contentField._editor.root.innerHTML;
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
