// var cartItem = {
//     product_id: 0,
//     quantity: 0,
// }

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

        const product_item = document.querySelector(`[data-catalog-product-id="${productId}"]`);
        if (!product_item) {
            console.error(`Product item with ID ${productId} not found`);
            return;
        }

        const quantityElement = product_item.querySelector('[product-quantity]');
        if (!quantityElement) {
            console.error(`Quantity element not found for product ID ${productId}`);
            return;
        }

        product_item.classList.add('in-cart');
        product_item.setAttribute('in-cart', true);
        quantityElement.value = quantity;
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
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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



init()