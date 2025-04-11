// var cartItem = {
//     product_id: 0,
//     quantity: 0,
// }
const cart = document.getElementById("cart");
const cartProductTemplate = document.getElementById("cart-product-template");
const openCartBtns = document.querySelectorAll("[open-cart]");
const closeCartBtns = document.querySelectorAll("[close-cart]");
const cartOverlay = document.querySelector("[cart-overlay]");

const productAddToCartBtns = document.querySelectorAll("[product-add-to-cart]");
const productQuantityMinusBtns = document.querySelectorAll(
  "[product-quantity-minus]"
);
const productQuantityPlusBtns = document.querySelectorAll(
  "[product-quantity-plus]"
);
const productQuantityInputs = document.querySelectorAll("[product-quantity]");

// Cart elements
const cartItemsContainer = document.querySelector("[cart-items]");
const cartEmptyState = document.querySelector("[cart-empty-state]");
const cartItemsCount = document.querySelector("[cart-items-count]");
const cartSubtotal = document.querySelector("[cart-subtotal]");
const cartDiscount = document.querySelector("[cart-discount]");
const cartTotal = document.querySelector("[cart-total]");
const cartCheckoutTotal = document.querySelector("[cart-checkout-total]");
const cartFooter = document.querySelector("[cart-footer]");

// Scene controls
const cartOrderBtn = document.querySelector("[cart-order-btn]");
const cartBackToCartBtn = document.querySelector("[cart-back-to-cart]");
const cartOrderForm = document.querySelector("[cart-order-form]");
const cartSubmitOrder = document.querySelector("[cart-submit-order]");
const cartResultSuccess = document.querySelector("[cart-result-success]");
const cartResultError = document.querySelector("[cart-result-error]");
const cartContinueShopping = document.querySelector("[cart-continue-shopping]");

function toggleCartHandler() {
  if (!cart) return;

  if (cart.classList.contains("active")) {
    closeCart();
  } else {
    openCart();
  }
}

function openCart() {
  if (!cart) return;

  cart.classList.add("active");
  document.body.style.overflow = "hidden";
}

function closeCart() {
  if (!cart) return;

  cart.classList.remove("active");
  document.body.style.overflow = "auto";

  // Reset to first scene after closing
  setTimeout(() => {
    showScene("cart");
  }, 300);
}

function showScene(sceneName) {
  const scenes = document.querySelectorAll("[data-cart-scene]");
  scenes.forEach((scene) => {
    if (scene.getAttribute("data-cart-scene") === sceneName) {
      scene.classList.add("active");
    } else {
      scene.classList.remove("active");
    }
  });
}

async function init() {
  initItemsInCart();
  initCart();
  initSceneControls();
  initFormValidation();
}

function initSceneControls() {
  // Scene 1 to Scene 2
  cartOrderBtn?.addEventListener("click", () => {
    showScene("form");
  });

  // Scene 2 back to Scene 1
  cartBackToCartBtn?.addEventListener("click", () => {
    showScene("cart");
  });

  // Scene 2 to Scene 3
  cartSubmitOrder?.addEventListener("click", (e) => {
    e.preventDefault();
    if (!validateForm()) return;

    handleOrderSubmit();
  });

  // Scene 3 to Scene 1 (reset and close cart)
  cartContinueShopping?.addEventListener("click", () => {
    closeCart();
  });
}

function validateForm() {
  const form = cartOrderForm;
  if (!form) return true;

  let isValid = true;

  // Validate full name
  const fullnameField = form.querySelector("#fullname");
  const fullnameError = form.querySelector('[data-error="fullname"]');
  if (fullnameField && !fullnameField.value.trim()) {
    showError(fullnameError, "Пожалуйста, введите ваше ФИО");
    isValid = false;
  } else {
    hideError(fullnameError);
  }

  // Validate phone
  const phoneField = form.querySelector("#phone");
  const phoneError = form.querySelector('[data-error="phone"]');
  if (phoneField && !phoneField.value.trim()) {
    showError(phoneError, "Пожалуйста, введите номер телефона");
    isValid = false;
  } else if (phoneField && !isValidPhone(phoneField.value)) {
    showError(phoneError, "Пожалуйста, введите корректный номер телефона");
    isValid = false;
  } else {
    hideError(phoneError);
  }

  // Validate email (only if provided)
  const emailField = form.querySelector("#email");
  const emailError = form.querySelector('[data-error="email"]');
  if (
    emailField &&
    emailField.value.trim() &&
    !isValidEmail(emailField.value)
  ) {
    showError(emailError, "Пожалуйста, введите корректный email");
    isValid = false;
  } else {
    hideError(emailError);
  }

  // Validate address
  const addressField = form.querySelector("#address");
  const addressError = form.querySelector('[data-error="address"]');
  if (addressField && !addressField.value.trim()) {
    showError(addressError, "Пожалуйста, введите адрес доставки");
    isValid = false;
  } else if (addressField && addressField.value.trim().length < 8) {
    showError(addressError, "Адрес должен содержать не менее 8 символов");
    isValid = false;
  } else {
    hideError(addressError);
  }

  return isValid;
}

function showError(element, message) {
  if (!element) return;
  element.textContent = message;
  element.classList.add("active");
}

function hideError(element) {
  if (!element) return;
  element.textContent = "";
  element.classList.remove("active");
}

function isValidEmail(email) {
  const re =
    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}

function isValidPhone(phone) {
  // Basic validation - should have at least 10 digits
  return phone.replace(/\D/g, "").length >= 10;
}

function initFormValidation() {
  // Phone field formatting
  const phoneFields = document.querySelectorAll("[phone-field]");
  phoneFields.forEach((field) => {
    field.addEventListener("input", (e) => {
      let value = e.target.value.replace(/\D/g, "");
      if (value.length > 0) {
        // Format for Russian numbers
        if (
          field
            .closest(".field--phone")
            .querySelector(".input-group__country-code").value === "+7"
        ) {
          if (value.length > 10) value = value.slice(0, 10);
          if (value.length >= 1)
            value = `(${value.slice(0, 3)}${
              value.length > 3 ? ") " : ""
            }${value.slice(3, 6)}${value.length > 6 ? "-" : ""}${value.slice(
              6,
              8
            )}${value.length > 8 ? "-" : ""}${value.slice(8, 10)}`;
        }
        // Format for Belarus numbers
        else if (
          field
            .closest(".field--phone")
            .querySelector(".input-group__country-code").value === "+375"
        ) {
          if (value.length > 9) value = value.slice(0, 9);
          if (value.length >= 1)
            value = `(${value.slice(0, 2)}${
              value.length > 2 ? ") " : ""
            }${value.slice(2, 5)}${value.length > 5 ? "-" : ""}${value.slice(
              5,
              7
            )}${value.length > 7 ? "-" : ""}${value.slice(7, 9)}`;
        }
      }
      e.target.value = value;
    });
  });

  // Country code change should clear and reformat phone
  const countryCodes = document.querySelectorAll(".input-group__country-code");
  countryCodes.forEach((select) => {
    select.addEventListener("change", (e) => {
      const phoneField = e.target
        .closest(".field--phone")
        .querySelector("[phone-field]");
      phoneField.value = "";
    });
  });
}

async function handleOrderSubmit() {
  const form = cartOrderForm;
  if (!form) return;

  const formData = new FormData(form);
  const cartData = await getCartData();

  const orderData = {
    cart: cartData,
    customer: {
      fullname: formData.get("fullname"),
      phone:
        formData.get("country_code") + formData.get("phone").replace(/\D/g, ""),
      email: formData.get("email"),
      address: formData.get("address"),
    },
  };

  try {
    const response = await fetch(
      `${window.location.origin}/api/cart/purchase`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-Token":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
        },
        body: JSON.stringify(orderData),
      }
    );

    const result = await response.json();

    showScene("result");

    if (response.ok && result.status === "success") {
      // Order success
      cartResultSuccess.classList.add("active");
      cartResultError.classList.remove("active");

      // Clear cart
      localStorage.removeItem("cartData");
    } else {
      // Order failed
      cartResultSuccess.classList.remove("active");
      cartResultError.classList.add("active");
    }
  } catch (error) {
    console.error("Error submitting order:", error);
    showScene("result");
    cartResultSuccess.classList.remove("active");
    cartResultError.classList.add("active");
  }
}

async function initItemsInCart() {
  const cartData = await getCartData();
  if (!cartData || cartData.length === 0) {
    console.log("Cart is empty or not initialized");
    return;
  }

  cartData.forEach((cartItem) => {
    const productId = cartItem.product_id;
    const quantity = cartItem.quantity;

    const productItem = document.querySelector(
      `[data-catalog-product-id="${productId}"]`
    );
    if (!productItem) {
      console.log(`Product item with ID ${productId} not found in page`);
      return;
    }

    const quantityElement = productItem.querySelector("[product-quantity]");
    if (!quantityElement) {
      console.log(`Quantity element not found for product ID ${productId}`);
      return;
    }

    changeQuantity(quantity, quantityElement);
  });
}

async function getCartData() {
  var cartData = localStorage.getItem("cartData");
  const cart = cartData ? JSON.parse(cartData) : [];
  return cart;
}

async function initCart() {
  var cartInfo = await getCart();
  if (!cartInfo) {
    updateEmptyCartState(true);
    return;
  }

  await updateCartUI(cartInfo);
}

async function getCart() {
  const cartData = await getCartData();

  if (!cartData || cartData.length === 0) {
    return null;
  }

  try {
    const response = await fetch(`${window.location.origin}/api/cart`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token":
          document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "",
      },
      body: JSON.stringify({
        data: cartData,
      }),
    });

    if (response.ok) {
      const data = await response.json();
      if (data.status == "success" && data.data.cart) {
        return data.data.cart;
      }
    }

    console.error("Error fetching cart:", response.statusText);
    return null;
  } catch (error) {
    console.error("Error fetching cart:", error);
    return null;
  }
}

function updateEmptyCartState(isEmpty) {
  if (isEmpty) {
    cartEmptyState?.classList.add("active");
    cartFooter?.classList.add("hidden");
  } else {
    cartEmptyState?.classList.remove("active");
    cartFooter?.classList.remove("hidden");
  }
}

function formatPrice(price) {
  return new Intl.NumberFormat("ru-RU", {
    style: "currency",
    currency: "RUB",
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price);
}

async function updateCartUI(cartInfo) {
  console.log(cartInfo);
  if (!cartInfo || !cartInfo.items || cartInfo.items.length === 0) {
    updateEmptyCartState(true);
    return;
  }

  updateEmptyCartState(false);

  const items = cartInfo.items;
  const totalPrice = cartInfo.total_price;
  const totalItems = cartInfo.total_items;
  const totalDiscountSum = cartInfo.total_discount_sum || 0;

  // Update cart items
  cartItemsContainer.innerHTML = "";
  items.forEach((item) => {
    const cartItem = createCartItemElement(item);
    cartItemsContainer.appendChild(cartItem);
  });

  // Update totals
  if (cartItemsCount) cartItemsCount.textContent = totalItems;
  if (cartSubtotal)
    cartSubtotal.textContent = formatPrice(totalPrice + totalDiscountSum);
  if (cartDiscount) {
    cartDiscount.textContent = `-${formatPrice(totalDiscountSum)}`;
    cartDiscount.parentElement.style.display =
      totalDiscountSum > 0 ? "flex" : "none";
  }
  if (cartTotal) cartTotal.textContent = formatPrice(totalPrice);
  if (cartCheckoutTotal)
    cartCheckoutTotal.textContent = formatPrice(totalPrice);
}

function createCartItemElement(item) {
  const product = item.product;
  const quantity = item.quantity;
  const totalPrice = item.total_price;
  const hasDiscount = product.discount > 0;
  const totalOriginalPrice = hasDiscount ? product.base_price * quantity : 0;

  const template = cartProductTemplate.content.cloneNode(true);
  const cartItem = template.querySelector(".cart-item");

  cartItem.setAttribute("data-cart-product-id", product.id);

  // Set image
  const image = cartItem.querySelector(".image__src");
  image.src =
    product.thumb_url || `${window.location.origin}/assets/images/product.png`;
  image.alt = product.name;

  // Set discount badge if applicable
  const discountBadge = cartItem.querySelector(".image__discount-badge");
  if (hasDiscount) {
    discountBadge.textContent = `-${product.discount}%`;
    discountBadge.style.display = "block";
  } else {
    discountBadge.style.display = "none";
  }

  // Set name
  cartItem.querySelector(".content__name").textContent = product.name;

  // Set price
  cartItem.querySelector(".price__value").textContent = formatPrice(
    product.price
  );

  // Set original price and discount if available
  const originalPriceContainer = cartItem.querySelector(
    ".price-info__original"
  );
  if (hasDiscount) {
    cartItem.querySelector(".original__value").textContent = formatPrice(
      product.base_price
    );
  } else {
    originalPriceContainer.style.display = "none";
  }

  // Set quantity
  const quantityInput = cartItem.querySelector("[cart-item-quantity]");
  quantityInput.value = quantity;
  quantityInput.setAttribute("data-product-id", product.id);

  // Set quantity buttons
  const minusBtn = cartItem.querySelector("[cart-item-quantity-minus]");
  const plusBtn = cartItem.querySelector("[cart-item-quantity-plus]");

  minusBtn.addEventListener("click", () => {
    changeCartItemQuantity(product.id, -1);
  });

  plusBtn.addEventListener("click", () => {
    changeCartItemQuantity(product.id, 1);
  });

  quantityInput.addEventListener("change", (e) => {
    const newQuantity = parseInt(e.target.value);
    if (newQuantity > 0) {
      updateCartItemQuantity(product.id, newQuantity);
    } else {
      removeFromCart(product.id);
    }
  });

  // Set remove button
  const removeBtn = cartItem.querySelector("[cart-remove-item]");
  removeBtn.addEventListener("click", () => {
    removeFromCart(product.id);
  });

  // Set total price
  cartItem.querySelector(".values__current").textContent =
    formatPrice(totalPrice);

  // Set original total price if discount
  const originalTotalElement = cartItem.querySelector(".values__original");
  if (hasDiscount) {
    originalTotalElement.textContent = formatPrice(totalOriginalPrice);
  } else {
    originalTotalElement.style.display = "none";
  }

  return cartItem;
}

async function changeCartItemQuantity(productId, delta) {
  const cartItem = document.querySelector(
    `.cart-item[data-cart-product-id="${productId}"]`
  );
  if (!cartItem) return;

  const quantityInput = cartItem.querySelector("[cart-item-quantity]");
  const currentQuantity = parseInt(quantityInput.value);
  const newQuantity = currentQuantity + delta;

  if (newQuantity <= 0) {
    removeFromCart(productId);
    return;
  }

  updateCartItemQuantity(productId, newQuantity);
}

function extractPriceFromElement(element) {
  if (!element || !element.textContent) return 0;
  return Number(element.textContent.replace(/[^\d]/g, ""));
}

// Calculate and update cart totals without server request
function updateCartTotalsLocally() {
  const cartItems = cartItemsContainer.querySelectorAll(".cart-item");

  // Calculate totals
  let totalItems = 0;
  let totalPrice = 0;
  let totalOriginalPrice = 0;

  cartItems.forEach((item) => {
    const quantity = parseInt(item.querySelector("[cart-item-quantity]").value);
    const itemCurrentPrice = extractPriceFromElement(
      item.querySelector(".values__current")
    );
    const itemOriginalPriceElement = item.querySelector(".values__original");

    totalItems += quantity;
    totalPrice += itemCurrentPrice;

    if (
      itemOriginalPriceElement &&
      itemOriginalPriceElement.style.display !== "none"
    ) {
      totalOriginalPrice += extractPriceFromElement(itemOriginalPriceElement);
    } else {
      totalOriginalPrice += itemCurrentPrice;
    }
  });

  const totalDiscountSum = Math.max(0, totalOriginalPrice - totalPrice);

  // Update cart UI
  if (cartItems.length === 0) {
    updateEmptyCartState(true);
    return;
  }

  updateEmptyCartState(false);

  // Update totals in UI
  if (cartItemsCount) cartItemsCount.textContent = totalItems;
  if (cartSubtotal) cartSubtotal.textContent = formatPrice(totalOriginalPrice);
  if (cartDiscount) {
    cartDiscount.textContent = `-${formatPrice(totalDiscountSum)}`;
    cartDiscount.parentElement.style.display =
      totalDiscountSum > 0 ? "flex" : "none";
  }
  if (cartTotal) cartTotal.textContent = formatPrice(totalPrice);
  if (cartCheckoutTotal)
    cartCheckoutTotal.textContent = formatPrice(totalPrice);
}

async function updateCartItemQuantity(productId, newQuantity) {
  // Update in localStorage
  await updateCartProduct(productId, newQuantity);

  // Update UI
  const cartItem = document.querySelector(
    `.cart-item[data-cart-product-id="${productId}"]`
  );
  if (cartItem) {
    // Update quantity input
    const quantityInput = cartItem.querySelector("[cart-item-quantity]");
    quantityInput.value = newQuantity;

    // Get product info from the existing cart item
    const product = {
      price: extractPriceFromElement(cartItem.querySelector(".price__value")),
      base_price:
        extractPriceFromElement(cartItem.querySelector(".original__value")) ||
        extractPriceFromElement(cartItem.querySelector(".price__value")),
      discount:
        cartItem
          .querySelector(".image__discount-badge")
          ?.textContent?.replace(/[^0-9]/g, "") || 0,
    };

    // Calculate new totals for this item
    const hasDiscount = product.discount > 0;
    const totalPrice = product.price * newQuantity;
    const totalOriginalPrice = hasDiscount
      ? product.base_price * newQuantity
      : 0;

    // Update total price for this item
    cartItem.querySelector(".values__current").textContent =
      formatPrice(totalPrice);

    const originalTotalElement = cartItem.querySelector(".values__original");
    if (hasDiscount) {
      originalTotalElement.textContent = formatPrice(totalOriginalPrice);
      originalTotalElement.style.display = "";
    } else {
      originalTotalElement.style.display = "none";
    }

    // Update overall cart totals by calculation
    updateCartTotalsLocally();
  }

  // Update catalog item if present
  const productElement = document.querySelector(
    `[data-catalog-product-id="${productId}"]`
  );
  if (productElement) {
    const quantityElement = productElement.querySelector("[product-quantity]");
    if (quantityElement) {
      changeQuantity(newQuantity, quantityElement);
    }
  }
}

async function removeFromCart(productId) {
  // Update localStorage
  await removeProductFromCart(productId);

  // Remove item from cart UI
  const cartItem = document.querySelector(
    `.cart-item[data-cart-product-id="${productId}"]`
  );
  if (cartItem) {
    cartItem.classList.add("removing");
    setTimeout(() => {
      cartItem.remove();

      // Update cart totals based on remaining items
      updateCartTotalsLocally();
    }, 300);
  }

  // Update catalog item if present
  const productElement = document.querySelector(
    `[data-catalog-product-id="${productId}"]`
  );
  if (productElement) {
    productElement.classList.remove("in-cart");
    productElement.removeAttribute("in-cart");

    const quantityElement = productElement.querySelector("[product-quantity]");
    if (quantityElement) {
      quantityElement.value = 1;
    }
  }
}

function addProductToCartUI(productId, quantity) {
  // This will be handled by updateCartUI
  initCart();
}

function removeProductFromCartUI(productId) {
  const cartItem = document.querySelector(
    `.cart-item[data-cart-product-id="${productId}"]`
  );
  if (cartItem) {
    cartItem.remove();
    updateCartTotalsLocally();
  }
}

function updateCartProductQuantityUI(productId, quantity) {
  const cartItem = document.querySelector(
    `.cart-item[data-cart-product-id="${productId}"]`
  );
  if (cartItem) {
    const quantityInput = cartItem.querySelector("[cart-item-quantity]");
    if (quantityInput) {
      // Just update the value without triggering the change event
      quantityInput.value = quantity;

      // Manually update the cart item pricing
      const product = {
        price: extractPriceFromElement(cartItem.querySelector(".price__value")),
        base_price:
          extractPriceFromElement(cartItem.querySelector(".original__value")) ||
          extractPriceFromElement(cartItem.querySelector(".price__value")),
        discount:
          cartItem
            .querySelector(".image__discount-badge")
            ?.textContent?.replace(/[^0-9]/g, "") || 0,
      };

      // Calculate and update totals directly
      const hasDiscount = product.discount > 0;
      const totalPrice = product.price * quantity;
      const totalOriginalPrice = hasDiscount
        ? product.base_price * quantity
        : 0;

      // Update the total price display
      cartItem.querySelector(".values__current").textContent =
        formatPrice(totalPrice);

      // Update original price if there's a discount
      const originalTotalElement = cartItem.querySelector(".values__original");
      if (hasDiscount) {
        originalTotalElement.textContent = formatPrice(totalOriginalPrice);
        originalTotalElement.style.display = "";
      } else {
        originalTotalElement.style.display = "none";
      }

      // Update overall cart totals
      updateCartTotalsLocally();
    }
  }
}

async function addProductToCartHandler(btn) {
  const productElement = btn.closest("[data-catalog-product-id]");
  const quantity =
    parseInt(productElement.querySelector("[product-quantity]").value) || 1;
  const productId = productElement.getAttribute("data-catalog-product-id");

  const quantityResult = await addProductToCart(productId, quantity);

  addProductToCartUI(productId, quantityResult);
  // Open cart after adding product
  // openCart();
}

async function addProductToCart(productId, quantity) {
  const cartData = await getCartData();
  const cartItemIndex = cartData.findIndex(
    (item) => item.product_id == productId
  );

  if (cartItemIndex !== -1) {
    quantity = cartData[cartItemIndex].quantity + quantity;
    cartData[cartItemIndex].quantity = quantity;
  } else {
    cartData.push({ product_id: productId, quantity: quantity });
  }

  localStorage.setItem("cartData", JSON.stringify(cartData));

  const productElement = document.querySelector(
    `[data-catalog-product-id="${productId}"]`
  );
  if (productElement) {
    productElement.classList.add("in-cart");
    productElement.setAttribute("in-cart", true);
  } else {
    console.log(`Product item with ID ${productId} not found in page`);
  }

  const quantityElement = productElement?.querySelector("[product-quantity]");
  if (quantityElement) {
    return changeQuantity(quantity, quantityElement);
  }

  return quantity;
}

function changeQuantityHandler(btn, delta = 1) {
  const quantityElement = btn.parentElement.querySelector("[product-quantity]");
  const currentQuantity = parseInt(quantityElement.value) || 1;
  const stepValue = parseInt(quantityElement.getAttribute("step")) || 1;

  changeQuantity(currentQuantity + stepValue * delta, quantityElement);
}

function changeQuantity(quantity, quantityElement) {
  const minValue = parseInt(quantityElement.getAttribute("min")) || 1;
  const maxValue = parseInt(quantityElement.getAttribute("max")) || 99;
  const productElement = quantityElement.closest("[data-catalog-product-id]");
  if (!productElement) return 0;

  const productId = productElement.getAttribute("data-catalog-product-id");
  const productQuantityMinusElement = productElement.querySelector(
    "[product-quantity-minus]"
  );
  const productQuantityPlusElement = productElement.querySelector(
    "[product-quantity-plus]"
  );

  if (quantity < minValue) {
    removeProductFromCartHandler(productId);
    return 0;
  }

  quantity = Math.max(minValue, Math.min(maxValue, quantity));
  quantityElement.value = quantity;
  productElement.classList.add("in-cart");
  productElement.setAttribute("in-cart", true);

  if (quantity == minValue) {
    productQuantityMinusElement?.classList.add("remove");
  } else if (quantity == maxValue) {
    productQuantityMinusElement?.classList.remove("remove");
    productQuantityPlusElement?.classList.add("disabled");
  } else {
    productQuantityMinusElement?.classList.remove("remove");
    productQuantityPlusElement?.classList.remove("disabled");
  }

  updateCartProductHandler(productId, quantity);
  return quantity;
}

async function removeProductFromCartHandler(productId) {
  removeProductFromCartUI(productId);
  await removeProductFromCart(productId);
}

async function removeProductFromCart(productId) {
  const cartData = await getCartData();
  const cartItemIndex = cartData.findIndex(
    (item) => item.product_id == productId
  );

  if (cartItemIndex !== -1) {
    cartData.splice(cartItemIndex, 1);
    localStorage.setItem("cartData", JSON.stringify(cartData));
  }

  const productElement = document.querySelector(
    `[data-catalog-product-id="${productId}"]`
  );
  if (productElement) {
    productElement.classList.remove("in-cart");
    productElement.removeAttribute("in-cart");
  }
}

async function updateCartProductHandler(productId, quantity) {
  await updateCartProduct(productId, quantity);
  updateCartProductQuantityUI(productId, quantity);
}

async function updateCartProduct(productId, quantity) {
  const cartData = await getCartData();
  const cartItemIndex = cartData.findIndex(
    (item) => item.product_id == productId
  );

  if (cartItemIndex !== -1) {
    cartData[cartItemIndex].quantity = quantity;
  } else {
    cartData.push({ product_id: productId, quantity: quantity });
  }

  localStorage.setItem("cartData", JSON.stringify(cartData));
}

// Event Listeners
openCartBtns.forEach((btn) => {
  btn.addEventListener("click", (e) => toggleCartHandler());
});

closeCartBtns.forEach((btn) => {
  btn.addEventListener("click", (e) => closeCart());
});

cartOverlay?.addEventListener("click", (e) => {
  closeCart();
});

productAddToCartBtns.forEach((btn) => {
  btn.addEventListener("click", (e) => {
    addProductToCartHandler(btn);
  });
});

productQuantityMinusBtns.forEach((btn) => {
  btn.addEventListener("click", (e) => changeQuantityHandler(btn, -1));
});

productQuantityPlusBtns.forEach((btn) => {
  btn.addEventListener("click", (e) => changeQuantityHandler(btn));
});

productQuantityInputs.forEach((input) => {
  input.addEventListener("change", (e) =>
    changeQuantity(parseInt(input.value), input)
  );
});

// Initialize cart
document.addEventListener("DOMContentLoaded", () => {
  init();
});
