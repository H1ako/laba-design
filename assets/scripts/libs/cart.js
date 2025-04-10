// var cartItem = {
//     id: 0,
//     quantity: 0,
// }

async function init() {
    initCart()
    cart = await getCart();
}

async function initCart() {
    var cart = await getCart();
    if (!cart || cart.length === 0) {
        console.error('Cart is empty or not initialized');
        return;
    }


}

async function getCart() {
    const cartData = await getCartData();

    const response = await fetch('/api/cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify(cartData),
    });
    if (response.ok) {
        const data = await response.json();
        if (data.success) {
            return data;
        }

        console.error('Error fetching cart:', data.message);
        return null;
    } else {
        console.error('Error fetching cart:', response.statusText);
        return null;
    }
}

async function getCartData() {
    var cartData = localStorage.getItem('cartData');
    cart = cartData ? JSON.parse(cartData) : [];
    return cart;
}



init()