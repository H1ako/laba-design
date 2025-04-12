document.addEventListener("DOMContentLoaded", function () {
  // Handle access form submission
  const accessForm = document.getElementById("access-form");
  if (accessForm) {
    accessForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      // Get form data
      const formData = new FormData(accessForm);
      const email = formData.get("email");

      // Hide any previous messages
      document.getElementById("form-success").style.display = "none";
      document.getElementById("form-error").style.display = "none";

      try {
        const response = await fetch(
          `${window.location.origin}/api/orders/generate-access`,
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-Token":
                document
                  .querySelector('meta[name="csrf-token"]')
                  ?.getAttribute("content") || "",
            },
            body: JSON.stringify({ email }),
          }
        );

        const result = await response.json();

        if (result.status === "success") {
          // Show success message
          document.getElementById("form-success").style.display = "flex";
          accessForm.reset();
        } else {
          // Show error message
          const errorElement = document.getElementById("form-error");
          errorElement.style.display = "flex";
          document.getElementById("error-message").textContent =
            result.message || "Произошла ошибка при обработке запроса";
        }
      } catch (error) {
        console.error("Error:", error);
        const errorElement = document.getElementById("form-error");
        errorElement.style.display = "flex";
        document.getElementById("error-message").textContent =
          "Произошла ошибка при подключении к серверу";
      }
    });
  }

  // Handle truncated text hover
  const truncatedElements = document.querySelectorAll(".truncate-text");
  truncatedElements.forEach((element) => {
    element.setAttribute("title", element.textContent);
  });
});
