document.addEventListener("DOMContentLoaded", function () {
  // Add any necessary order page functionality here

  // For example, you could add functionality to collapse/expand product descriptions
  const productDescriptions = document.querySelectorAll(
    ".content__description"
  );
  productDescriptions.forEach((desc) => {
    desc.addEventListener("click", function () {
      desc.classList.toggle("expanded");
    });
  });
});
