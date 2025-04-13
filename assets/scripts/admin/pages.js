document.addEventListener("DOMContentLoaded", function () {
  initRichTextEditor();
  initPageForm();
  initPageDelete();
  initSlugGenerator();
  initSortable();
});

/**
 * Initialize rich text editor for content field
 */
function initRichTextEditor() {
  const contentField = document.getElementById("content");
  const quillEditor = document.getElementById("quill-editor");

  if (!contentField || !quillEditor) return;

  // Create Quill instance
  const quill = new Quill("#quill-editor", {
    modules: {
      toolbar: [
        [{ header: [1, 2, 3, 4, 5, 6, false] }],
        ["bold", "italic", "underline", "strike"],
        [{ color: [] }, { background: [] }],
        [{ align: [] }],
        [{ list: "ordered" }, { list: "bullet" }],
        ["link", "image", "video"],
        ["clean"],
      ],
    },
    placeholder: "Введите содержание страницы...",
    theme: "snow",
  });

  // Set initial content if exists
  if (contentField.value) {
    quill.root.innerHTML = contentField.value;
  }

  // Listen for editor changes
  quill.on("text-change", function () {
    contentField.value = quill.root.innerHTML;
  });

  // Attach quill instance to the form for later access
  contentField._editor = quill;
}

/**
 * Initialize page form handling
 */
function initPageForm() {
  const pageForm = document.getElementById("page-form");
  if (!pageForm) return;

  pageForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    // Reset validation errors
    pageForm.querySelectorAll(".invalid-feedback").forEach((el) => {
      el.textContent = "";
      el.style.display = "none";
    });

    pageForm.querySelectorAll(".is-invalid").forEach((el) => {
      el.classList.remove("is-invalid");
    });

    // Get Quill content if editor exists
    const contentField = document.getElementById("content");
    if (contentField && contentField._editor) {
      contentField.value = contentField._editor.root.innerHTML;
    }

    // Prepare form data
    const formData = new FormData(pageForm);

    // Set publish status if checkbox not checked
    if (!formData.has("is_published")) {
      formData.append("is_published", "0");
    }

    // Disable submit button and show loading indicator
    const submitButton = pageForm.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML =
      '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...';

    try {
      // Determine if this is create or edit mode
      const pageId = pageForm.getAttribute("data-page-id");
      const isEdit = !!pageId;
      const apiUrl = isEdit ? `/api/admin/pages/${pageId}` : "/api/admin/pages";

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
            isEdit ? "Страница успешно обновлена" : "Страница успешно создана",
            "success"
          );
        } else {
          alert(
            isEdit ? "Страница успешно обновлена" : "Страница успешно создана"
          );
        }

        // Redirect to the new page
        if (result.redirect) {
          window.location.href = result.redirect.startsWith("/")
            ? Router.getRoute(result.redirect)
            : result.redirect;
        }
      } else {
        // Handle validation errors
        const errors = result.errors || {};

        Object.keys(errors).forEach((field) => {
          const feedbackElement = pageForm.querySelector(
            `[data-error-for="${field}"]`
          );
          const inputElement = pageForm.querySelector(`[name="${field}"]`);

          if (feedbackElement && inputElement) {
            feedbackElement.textContent = Array.isArray(errors[field])
              ? errors[field][0]
              : errors[field];
            feedbackElement.style.display = "block";
            inputElement.classList.add("is-invalid");
          }
        });

        // Show error notification
        if (typeof showNotification === "function") {
          showNotification(
            result.message || "Ошибка сохранения страницы",
            "error"
          );
        } else {
          alert(result.message || "Ошибка сохранения страницы");
        }
      }
    } catch (error) {
      console.error("Error submitting form:", error);

      if (typeof showNotification === "function") {
        showNotification("Произошла ошибка при сохранении страницы", "error");
      } else {
        alert("Произошла ошибка при сохранении страницы");
      }
    } finally {
      // Re-enable submit button
      submitButton.disabled = false;
      submitButton.innerHTML = originalButtonText;
    }
  });
}

/**
 * Initialize page deletion functionality
 */
function initPageDelete() {
  const deleteButtons = document.querySelectorAll("[data-page-delete]");

  deleteButtons.forEach((button) => {
    button.addEventListener("click", async function () {
      const pageId = this.getAttribute("data-page-id");
      const confirmMessage =
        this.getAttribute("data-confirm") ||
        "Вы уверены, что хотите удалить эту страницу?";

      if (!confirm(confirmMessage)) return;

      try {
        const response = await fetch(`/api/admin/pages/${pageId}`, {
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
            showNotification("Страница успешно удалена", "success");
          } else {
            alert("Страница успешно удалена");
          }

          // Redirect to pages list or remove element from list
          if (result.redirect) {
            window.location.href = result.redirect.startsWith("/")
              ? Router.getRoute(result.redirect)
              : result.redirect;
          } else {
            const pageRow = document.querySelector(
              `tr[data-page-id="${pageId}"]`
            );
            if (pageRow) {
              pageRow.remove();
            }
          }
        } else {
          // Show error notification
          if (typeof showNotification === "function") {
            showNotification(
              result.message || "Не удалось удалить страницу",
              "error"
            );
          } else {
            alert(result.message || "Не удалось удалить страницу");
          }
        }
      } catch (error) {
        console.error("Error deleting page:", error);

        if (typeof showNotification === "function") {
          showNotification("Произошла ошибка при удалении страницы", "error");
        } else {
          alert("Произошла ошибка при удалении страницы");
        }
      }
    });
  });
}

/**
 * Initialize slug generator from title
 */
function initSlugGenerator() {
  const titleInput = document.getElementById("title");
  const slugInput = document.getElementById("slug");

  if (!titleInput || !slugInput) return;

  // Only generate slug if slug field is empty
  titleInput.addEventListener("input", function () {
    if (slugInput.value.trim() === "") {
      slugInput.value = generateSlug(titleInput.value);
    }
  });

  // Function to generate a slug from text
  function generateSlug(text) {
    return text
      .toLowerCase()
      .replace(/[^\wа-яё]/gi, "-") // Replace non-word chars with dash
      .replace(/[а-яё]/gi, function (match) {
        // Simple transliteration for Russian characters
        const translitMap = {
          а: "a",
          б: "b",
          в: "v",
          г: "g",
          д: "d",
          е: "e",
          ё: "yo",
          ж: "zh",
          з: "z",
          и: "i",
          й: "y",
          к: "k",
          л: "l",
          м: "m",
          н: "n",
          о: "o",
          п: "p",
          р: "r",
          с: "s",
          т: "t",
          у: "u",
          ф: "f",
          х: "h",
          ц: "ts",
          ч: "ch",
          ш: "sh",
          щ: "sch",
          ъ: "",
          ы: "y",
          ь: "",
          э: "e",
          ю: "yu",
          я: "ya",
        };
        return translitMap[match.toLowerCase()] || match;
      })
      .replace(/-+/g, "-") // Replace multiple dashes with single dash
      .replace(/^-+|-+$/g, ""); // Trim dashes from start and end
  }
}

/**
 * Initialize sortable functionality for page list
 */
function initSortable() {
  const sortableList = document.querySelector(".page-list-sortable");

  if (!sortableList) return;

  // Check if Sortable.js is loaded
  if (typeof Sortable === "undefined") {
    // Load Sortable.js
    const script = document.createElement("script");
    script.src =
      "https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js";
    script.onload = function () {
      createSortable(sortableList);
    };
    document.head.appendChild(script);
  } else {
    createSortable(sortableList);
  }

  function createSortable(element) {
    const sortable = Sortable.create(element, {
      handle: ".drag-handle",
      animation: 150,
      ghostClass: "sortable-ghost",
      chosenClass: "sortable-chosen",
      dragClass: "sortable-drag",
      onEnd: function () {
        // Get the new order of pages
        const pageIds = Array.from(element.querySelectorAll("tr")).map((row) =>
          parseInt(row.getAttribute("data-page-id"))
        );

        // Send the new order to the server
        fetch("/api/admin/pages/order", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-Token":
              document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "",
          },
          body: JSON.stringify({ pages: pageIds }),
        })
          .then((response) => response.json())
          .then((result) => {
            if (result.status === "success") {
              if (typeof showNotification === "function") {
                showNotification("Порядок страниц обновлен", "success");
              }
            } else {
              if (typeof showNotification === "function") {
                showNotification(
                  "Ошибка при обновлении порядка страниц",
                  "error"
                );
              }
            }
          })
          .catch((error) => {
            console.error("Error updating page order:", error);
            if (typeof showNotification === "function") {
              showNotification(
                "Произошла ошибка при обновлении порядка страниц",
                "error"
              );
            }
          });
      },
    });
  }
}
