// var cartItem = {
//     product_id: 0,
//     quantity: 0,
// }
const productAddToCartBtns = document.querySelectorAll('[product-add-to-cart]');
const productQuantityMinusBtns = document.querySelectorAll('[product-quantity-minus]');
const productQuantityPlusBtns = document.querySelectorAll('[product-quantity-plus]');
const productQuantityInputs = document.querySelectorAll('[product-quantity]');

async function init() {
    initItemsInCart()
    initCart()
}

async function initItemsInCart() {
    const cartData = await getCartData();
    if (!cartData || cartData.length === 0) {
        console.error('Cart is empty or not initialized');
        return;
    }

    cartData.forEach(cartItem => {
        const productId = cartItem.product_id;
        const quantity = cartItem.quantity;

        const productItem = document.querySelector(`[data-catalog-product-id="${productId}"]`);
        if (!productItem) {
            console.error(`Product item with ID ${productId} not found`);
            return;
        }

        const quantityElement = productItem.querySelector('[product-quantity]');
        if (!quantityElement) {
            console.error(`Quantity element not found for product ID ${productId}`);
            return;
        }
        
        changeQuantity(quantity, quantityElement);
    })
}

async function getCartData() {
    var cartData = localStorage.getItem('cartData');
    cart = cartData ? JSON.parse(cartData) : [];
    return cart;
}

async function initCart() {
    var cart = await getCart();
    if (!cart || cart.items?.length === 0) {
        console.error('Cart is empty or not initialized');
        return;
    }

    await updateCartUI(cart);
}

async function getCart() {
    const cartData = await getCartData();

    const response = await fetch('/api/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
            cart: cartData
        }),
    });
    if (response.ok) {
        const data = await response.json();
        if (data.status == 'success') {
            return data.cart;
        }

        console.error('Error fetching cart:', data.message);
        return null;
    } else {
        console.error('Error fetching cart:', response.statusText);
        return null;
    }
}

async function updateCartUI(cart) {
    const items = cart.items;
    const total_price = cart.total_price;
    const total_items = cart.total_items;
    const total_discount_sum = cart.total_discount_sum;
}

function addProductToCartHandler(btn) {
    const productElement = btn.closest('[data-catalog-product-id]');
    const quantity = parseInt(productElement.querySelector('[product-quantity]').value) || 1;
    const productId = productElement.getAttribute('data-catalog-product-id');

    const quantityResult = addProductToCart(productId, quantity)
    addProductToCartUI(productId, quantityResult);
}

async function addProductToCart(productId, quantity) {
    const cartData = await getCartData();
    const cartItemIndex = cartData.findIndex(item => item.product_id == productId);

    if (cartItemIndex !== -1) {
        quantity = cartData[cartItemIndex].quantity + quantity
        cartData[cartItemIndex].quantity = quantity;
    } else {
        cartData.push({ product_id: productId, quantity: quantity });
    }

    localStorage.setItem('cartData', JSON.stringify(cartData));

    const productElement = document.querySelector(`[data-catalog-product-id="${productId}"]`);
    if (productElement) {
        productElement.classList.add('in-cart');
        productElement.setAttribute('in-cart', true);
    } else {
        console.error(`Product item with ID ${productId} not found`);
    }
    const quantityElement = productElement.querySelector('[product-quantity]');

    return changeQuantity(quantity, quantityElement);
}

function addProductToCartUI(productId, quantity) {
    

}

function changeQuantityHandler(btn, delta=1) {
    const quantityElement = btn.parentElement.querySelector('[product-quantity]');
    const currentQuantity = parseInt(quantityElement.value) || 1;
    const stepValue = parseInt(quantityElement.getAttribute('step')) || 1;

    changeQuantity(currentQuantity + stepValue * delta, quantityElement);
}

function changeQuantity(quantity, quantityElement) {
    const minValue = quantityElement.getAttribute('min') || 1;
    const maxValue = quantityElement.getAttribute('max') || 99;
    const productElement = quantityElement.closest('[data-catalog-product-id]');
    const productId = productElement.getAttribute('data-catalog-product-id');
    const productQuantityMinusElement = productElement.querySelector('[product-quantity-minus]');
    const productQuantityPlusElement = productElement.querySelector('[product-quantity-plus]');

    if (quantity < minValue) {
        removeProductFromCartHandler(productId);
        return 0;
    }
    
    quantity = Math.max(minValue, Math.min(maxValue, quantity));
    quantityElement.value = quantity;
    productElement.classList.add('in-cart');
    productElement.setAttribute('in-cart', true);

    if (quantity == minValue) {
        productQuantityMinusElement.classList.add('remove')
    } else if (quantity == maxValue) {
        productQuantityMinusElement.classList.remove('remove')
        productQuantityPlusElement.classList.add('disabled')
    } else {
        productQuantityMinusElement.classList.remove('remove')
        productQuantityPlusElement.classList.remove('disabled')
    }
    
    updateCartProductHandler(productId, quantity);
    return quantity;
}

async function removeProductFromCartHandler(productId) {
    removeProductFromCartUI(productId);
    removeProductFromCart(productId);
}

async function removeProductFromCart(productId) {
    const cartData = await getCartData();
    const cartItemIndex = cartData.findIndex(item => item.product_id == productId);

    if (cartItemIndex !== -1) {
        cartData.splice(cartItemIndex, 1);
        localStorage.setItem('cartData', JSON.stringify(cartData));
    }

    const productElement = document.querySelector(`[data-catalog-product-id="${productId}"]`);
    if (productElement) {
        productElement.classList.remove('in-cart');
        productElement.removeAttribute('in-cart');
    }
}

function removeProductFromCartUI(productId) {

}

async function updateCartProductHandler(productId, quantity) {
    updateCartProduct(productId, quantity);
    updateCartProductQuantityUI(productId, quantity);
}

async function updateCartProduct(productId, quantity) {
    const cartData = await getCartData();
    const cartItemIndex = cartData.findIndex(item => item.product_id == productId);

    if (cartItemIndex !== -1) {
        cartData[cartItemIndex].quantity = quantity;
    } else {
        cartData.push({ product_id: productId, quantity: quantity });
    }

    localStorage.setItem('cartData', JSON.stringify(cartData));
}

function updateCartProductQuantityUI() {

}

productAddToCartBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => {
        addProductToCartHandler(btn)
    })
})

productQuantityMinusBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => changeQuantityHandler(btn, -1))
})

productQuantityPlusBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => changeQuantityHandler(btn))
})

productQuantityInputs.forEach((input) => {
    input.addEventListener('change', (e) => changeQuantity(input.value, input))
})

init()