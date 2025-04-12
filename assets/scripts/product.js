document.addEventListener("DOMContentLoaded", function () {
  initProductSlider();
  initSizeSelector();
  checkProductInCart();
  initAddToCartButton();
  initQuantityControls()
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

      // Check if this size is in cart and update UI accordingly
      checkProductInCart();
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

  // Get the selected size
  const selectedSizeElement = document.querySelector(
    "#product-sizes .list__item.active"
  );
  // Normalize size value
  let selectedSize = selectedSizeElement
    ? selectedSizeElement.getAttribute("data-size")
    : null;

  // Find item in cart matching this product ID and size specifically
  // Handle null/undefined/empty sizes consistently
  const inCart = cartItems.find(
    (item) =>
      item.product_id == productId &&
      ((selectedSize === null &&
        (item.size === null || item.size === undefined || item.size === "")) ||
        selectedSize === item.size)
  );

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

    if (minusBtn) {
      if (inCart.quantity <= 1) {
        minusBtn.classList.add("remove");
      } else {
        minusBtn.classList.remove("remove");
      }
    }

    if (plusBtn) {
      if (inCart.quantity >= 99) {
        plusBtn.classList.add("disabled");
      } else {
        plusBtn.classList.remove("disabled");
      }
    }
  } else {
    // If not in cart with this size, reset the UI
    productElement.classList.remove("in-cart");
    productElement.removeAttribute("in-cart");

    const quantityInput = productElement.querySelector("[product-quantity]");
    if (quantityInput) {
      quantityInput.value = 1;
    }

    const minusBtn = productElement.querySelector("[product-quantity-minus]");
    if (minusBtn) {
      minusBtn.classList.remove("remove");
    }

    const plusBtn = productElement.querySelector("[product-quantity-plus]");
    if (plusBtn) {
      plusBtn.classList.remove("disabled");
    }
  }
}

function initAddToCartButton() {
  const addToCartBtn = document.querySelector(".actions__add-to-cart");
  if (addToCartBtn) {
    addToCartBtn.addEventListener("click", function (e) {
      // Stop event propagation to prevent the global handler from firing too
      e.stopPropagation();

      // Prevent the default cart handler execution by marking this as handled
      if (typeof e.target.dataset === "object") {
        e.target.dataset.handled = true;
      }

      const productElement = this.closest("[data-catalog-product-id]");
      if (!productElement) return;

      const productId = productElement.getAttribute("data-catalog-product-id");

      // Get the selected size from the active size element
      const selectedSizeElement = document.querySelector(
        "#product-sizes .list__item.active"
      );

      // Normalize size value to prevent undefined/null inconsistency
      let selectedSize = selectedSizeElement
        ? selectedSizeElement.getAttribute("data-size")
        : null;

      // Add product to cart with selected size
      const event = new CustomEvent("add-to-cart", {
        detail: {
          productId: productId,
          size: selectedSize,
          quantity: 1,
        },
      });

      document.dispatchEvent(event);

      // Update the UI immediately
      setTimeout(() => {
        checkProductInCart();
      }, 100);
    });
  }
}


function initQuantityControls() {
    const productElement = document.querySelector("[data-catalog-product-id]");
    if (!productElement) return;
    
    const minusBtn = productElement.querySelector("[product-quantity-minus]");
    const plusBtn = productElement.querySelector("[product-quantity-plus]");
    const quantityInput = productElement.querySelector("[product-quantity]");
    
    if (!minusBtn || !plusBtn || !quantityInput) return;
    
    // Override existing event listeners with our custom ones
    minusBtn.addEventListener("click", function(e) {
      e.stopPropagation(); // Stop propagation to prevent the global handler
      
      // Get selected size
      const selectedSizeElement = document.querySelector("#product-sizes .list__item.active");
      const selectedSize = selectedSizeElement ? selectedSizeElement.getAttribute("data-size") : null;
      
      const productId = productElement.getAttribute("data-catalog-product-id");
      const currentQuantity = parseInt(quantityInput.value) || 1;
      const newQuantity = Math.max(0, currentQuantity - 1);
      
      if (newQuantity === 0) {
        // If quantity becomes zero, remove the item from cart
        removeFromCartWithSize(productId, selectedSize);
      } else {
        // Otherwise update the quantity
        updateCartWithSize(productId, newQuantity, selectedSize);
      }
      
      // Update UI
      setTimeout(() => {
        checkProductInCart();
      }, 100);
    });
    
    plusBtn.addEventListener("click", function(e) {
      e.stopPropagation(); // Stop propagation to prevent the global handler
      
      // Get selected size
      const selectedSizeElement = document.querySelector("#product-sizes .list__item.active");
      const selectedSize = selectedSizeElement ? selectedSizeElement.getAttribute("data-size") : null;
      
      const productId = productElement.getAttribute("data-catalog-product-id");
      const currentQuantity = parseInt(quantityInput.value) || 1;
      const newQuantity = Math.min(99, currentQuantity + 1);
      
      updateCartWithSize(productId, newQuantity, selectedSize);
      
      // Update UI
      setTimeout(() => {
        checkProductInCart();
      }, 100);
    });
    
    quantityInput.addEventListener("change", function(e) {
      e.stopPropagation(); // Stop propagation to prevent the global handler
      
      // Get selected size
      const selectedSizeElement = document.querySelector("#product-sizes .list__item.active");
      const selectedSize = selectedSizeElement ? selectedSizeElement.getAttribute("data-size") : null;
      
      const productId = productElement.getAttribute("data-catalog-product-id");
      const newQuantity = parseInt(this.value) || 1;
      
      if (newQuantity <= 0) {
        // If quantity becomes zero or negative, remove the item from cart
        removeFromCartWithSize(productId, selectedSize);
      } else {
        // Otherwise update the quantity
        updateCartWithSize(productId, newQuantity, selectedSize);
      }
      
      // Update UI
      setTimeout(() => {
        checkProductInCart();
      }, 100);
    });
  }

  function updateCartWithSize(productId, quantity, size) {
    // Get cart data
    const cartData = JSON.parse(localStorage.getItem("cartData") || "[]");
    
    // Normalize size for comparison
    size = (size === undefined || size === '') ? null : size;
    
    // Find matching item
    const itemIndex = cartData.findIndex(
      (item) => 
        item.product_id == productId && 
        ((size === null && (item.size === null || item.size === undefined || item.size === '')) ||
          size === item.size)
    );
    
    if (itemIndex !== -1) {
      // Update quantity
      cartData[itemIndex].quantity = quantity;
    } else {
      // Add new item
      cartData.push({
        product_id: productId,
        size: size,
        quantity: quantity
      });
    }
    
    // Save updated cart
    localStorage.setItem("cartData", JSON.stringify(cartData));
    
    // Update cart UI if needed
    if (typeof initCart === 'function') {
      initCart();
    }
  }

  function removeFromCartWithSize(productId, size) {
    // Get cart data
    const cartData = JSON.parse(localStorage.getItem("cartData") || "[]");
    
    // Normalize size for comparison
    size = (size === undefined || size === '') ? null : size;
    
    // Find matching item
    const itemIndex = cartData.findIndex(
      (item) => 
        item.product_id == productId && 
        ((size === null && (item.size === null || item.size === undefined || item.size === '')) ||
          size === item.size)
    );
    
    if (itemIndex !== -1) {
      // Remove the item
      cartData.splice(itemIndex, 1);
      
      // Save updated cart
      localStorage.setItem("cartData", JSON.stringify(cartData));
      
      // Update cart UI if needed
      if (typeof initCart === 'function') {
        initCart();
      }
    }
  }