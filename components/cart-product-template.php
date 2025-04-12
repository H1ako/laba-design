<template id="cart-product-template">
    <div class="cart-item" data-cart-product-id="" data-cart-product-size="">
        <div class="cart-item__image">
            <img src="" alt="" class="image__src">
            <div class="image__discount-badge" style="display: none;"></div>
        </div>
        <div class="cart-item__content">
            <div class="content__top">
                <h3 class="content__name"></h3>
                <button class="content__remove" cart-remove-item>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </button>
            </div>
            <div class="content__size">
                <span class="size__label">Размер:</span>
                <span class="size__value"></span>
            </div>
            <div class="content__price-info">
                <div class="price-info__current">
                    <span class="price__value"></span>
                </div>
                <div class="price-info__original">
                    <span class="original__value"></span>
                </div>
            </div>
            <div class="content__quantity">
                <button class="quantity__btn quantity__minus" cart-item-quantity-minus>-</button>
                <input type="number" class="quantity__input" value="1" min="1" max="99" cart-item-quantity>
                <button class="quantity__btn quantity__plus" cart-item-quantity-plus>+</button>
            </div>
            <div class="content__total">
                <span class="total__label">Итого:</span>
                <div class="total__values">
                    <span class="values__current"></span>
                    <span class="values__original"></span>
                </div>
            </div>
        </div>
    </div>
</template>