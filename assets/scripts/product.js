document.addEventListener("DOMContentLoaded", function () {
  initProductSlider();
  initSizeSelector();
  checkProductInCart();
});

function initProductSlider() {
  const mainSlider = document.getElementById("main-slider");
  const thumbnailSlider = document.getElementById("thumbnail-slider");

  if (!mainSlider) return;

  const slides = mainSlider.querySelectorAll(".slider__slide");
  if (slides.length <= 1) return; // Don't initialize slider if only one image

  const thumbnails = thumbnailSlider?.querySelectorAll(".thumbnail__slide");
  let currentIndex = 0;
  let sliderInterval;

  // Initialize slider
  function showSlide(index) {
    // Hide all slides
    slides.forEach((slide) => {
      slide.classList.remove("active");
    });

    // Show current slide
    slides[index].classList.add("active");

    // Update thumbnails if they exist
    if (thumbnails) {
      thumbnails.forEach((thumb) => {
        thumb.classList.remove("active");
      });
      thumbnails[index].classList.add("active");
    }

    currentIndex = index;
  }

  // Auto rotation
  function startAutoRotation() {
    sliderInterval = setInterval(() => {
      let nextIndex = (currentIndex + 1) % slides.length;
      showSlide(nextIndex);
    }, 2000);
  }

  // Stop auto rotation on user interaction
  function stopAutoRotation() {
    clearInterval(sliderInterval);
  }

  // Restart auto rotation after some time of inactivity
  function restartAutoRotation() {
    stopAutoRotation();
    startAutoRotation();
  }

  // Add click event listeners to thumbnails if they exist
  if (thumbnails) {
    thumbnails.forEach((thumbnail, index) => {
      thumbnail.addEventListener("click", () => {
        stopAutoRotation();
        showSlide(index);
        setTimeout(restartAutoRotation, 5000); // Restart auto rotation after 5 seconds of inactivity
      });
    });

    // Stop auto rotation when mouse enters the slider area
    mainSlider.addEventListener("mouseenter", stopAutoRotation);
    thumbnailSlider.addEventListener("mouseenter", stopAutoRotation);

    // Restart auto rotation when mouse leaves the slider area
    mainSlider.addEventListener("mouseleave", restartAutoRotation);
    thumbnailSlider.addEventListener("mouseleave", restartAutoRotation);
  }

  // Touch events for mobile
  let touchStartX = 0;
  let touchEndX = 0;

  mainSlider.addEventListener("touchstart", (e) => {
    stopAutoRotation();
    touchStartX = e.changedTouches[0].screenX;
  });

  mainSlider.addEventListener("touchend", (e) => {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
    setTimeout(restartAutoRotation, 5000);
  });

  function handleSwipe() {
    if (touchEndX < touchStartX - 50) {
      // Swipe left, show next slide
      showSlide((currentIndex + 1) % slides.length);
    } else if (touchEndX > touchStartX + 50) {
      // Swipe right, show previous slide
      showSlide((currentIndex - 1 + slides.length) % slides.length);
    }
  }

  // Start auto rotation
  startAutoRotation();
}

function initSizeSelector() {
  const sizesList = document.getElementById("product-sizes");

  if (!sizesList) return;

  const sizeItems = sizesList.querySelectorAll(
    ".list__item:not(.out-of-stock)"
  );

  sizeItems.forEach((item) => {
    item.addEventListener("click", () => {
      // Remove active class from all sizes
      sizeItems.forEach((s) => s.classList.remove("active"));

      // Add active class to the selected size
      item.classList.add("active");

      // Store selected size in local storage
      const productId = document
        .querySelector("[data-catalog-product-id]")
        ?.getAttribute("data-catalog-product-id");
      if (productId) {
        localStorage.setItem(
          `product_${productId}_selected_size`,
          item.getAttribute("data-size")
        );
      }
    });
  });

  // Check if there's a previously selected size
  const productId = document
    .querySelector("[data-catalog-product-id]")
    ?.getAttribute("data-catalog-product-id");
  if (productId) {
    const selectedSize = localStorage.getItem(
      `product_${productId}_selected_size`
    );
    if (selectedSize) {
      const sizeItem = Array.from(sizeItems).find(
        (item) => item.getAttribute("data-size") === selectedSize
      );
      if (sizeItem) {
        sizeItem.click();
      } else {
        // If stored size is no longer available, select the first one
        sizeItems[0]?.click();
      }
    } else if (sizeItems.length > 0) {
      // If no stored size, select the first available one
      sizeItems[0].click();
    }
  }
}

function checkProductInCart() {
  // This function checks if the current product is in the cart
  const cartItemsData = localStorage.getItem("cartData");
  if (!cartItemsData) return;

  const cartItems = JSON.parse(cartItemsData);
  const productElement = document.querySelector("[data-catalog-product-id]");

  if (!productElement) return;

  const productId = productElement.getAttribute("data-catalog-product-id");

  const inCart = cartItems.find((item) => item.product_id == productId);

  if (inCart) {
    productElement.classList.add("in-cart");
    productElement.setAttribute("in-cart", "");

    // Update quantity input
    const quantityInput = productElement.querySelector("[product-quantity]");
    if (quantityInput) {
      quantityInput.value = inCart.quantity;
    }

    // Update minus/plus button states
    const minusBtn = productElement.querySelector("[product-quantity-minus]");
    const plusBtn = productElement.querySelector("[product-quantity-plus]");

    if (inCart.quantity <= 1 && minusBtn) {
      minusBtn.classList.add("remove");
    }

    if (inCart.quantity >= 99 && plusBtn) {
      plusBtn.classList.add("disabled");
    }
  }
}
