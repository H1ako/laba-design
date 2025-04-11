<div class="cart" id="cart">
    <div class="cart__overlay" cart-overlay></div>
    <div class="cart__wrapper">
        <button class="wrapper__close-cart" close-cart>
            <?php include_once('icons/close.php'); ?>
        </button>
        
        <!-- Scene 1: Cart Items -->
        <div class="wrapper__scene scene_cart active" data-cart-scene="cart">
            <div class="scene__header">
                <h2 class="header__title">Корзина</h2>
                <span class="header__count" cart-items-count>0</span>
            </div>
            
            <div class="scene__content">
                <div class="content__empty-state" cart-empty-state>
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8" cy="21" r="1"></circle>
                        <circle cx="19" cy="21" r="1"></circle>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                    </svg>
                    <p>Ваша корзина пуста</p>
                    <a href="<?= Router::getRoute('/catalog') ?>" class="empty-state__btn">Перейти в каталог</a>
                </div>
                
                <div class="content__items" cart-items></div>
            </div>
            
            <div class="scene__footer" cart-footer>
                <div class="footer__totals">
                    <div class="totals__row">
                        <span class="row__label">Сумма:</span>
                        <span class="row__value" cart-subtotal>0 ₽</span>
                    </div>
                    <div class="totals__row">
                        <span class="row__label">Скидка:</span>
                        <span class="row__value row__value--discount" cart-discount>0 ₽</span>
                    </div>
                    <div class="totals__row totals__row--main">
                        <span class="row__label">Итого:</span>
                        <span class="row__value row__value--total" cart-total>0 ₽</span>
                    </div>
                </div>
                <button class="footer__order-btn" cart-order-btn>Оформить заказ</button>
            </div>
        </div>
        
        <!-- Scene 2: Order Form -->
        <div class="wrapper__scene scene_form" data-cart-scene="form">
            <div class="scene__header">
                <button class="header__back-btn" cart-back-to-cart>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </button>
                <h2 class="header__title">Оформление заказа</h2>
            </div>
            
            <div class="scene__content">
                <form class="content__form" cart-order-form>
                    <div class="form__field">
                        <label for="fullname">ФИО*</label>
                        <input type="text" id="fullname" name="fullname" required placeholder="Иванов Иван Иванович">
                        <span class="field__error" data-error="fullname"></span>
                    </div>
                    
                    <div class="form__field field--phone">
                        <label for="phone">Телефон*</label>
                        <div class="phone__input-group">
                            <select class="input-group__country-code" name="country_code">
                                <option value="+7">🇷🇺 +7</option>
                                <option value="+375">🇧🇾 +375</option>
                            </select>
                            <input type="tel" id="phone" name="phone" required placeholder="(999) 123-45-67" phone-field>
                        </div>
                        <span class="field__error" data-error="phone"></span>
                    </div>
                    
                    <div class="form__field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="example@email.com">
                        <span class="field__error" data-error="email"></span>
                    </div>
                    
                    <div class="form__field">
                        <label for="address">Адрес доставки*</label>
                        <textarea id="address" name="address" required placeholder="Город, улица, дом, квартира"></textarea>
                        <span class="field__error" data-error="address"></span>
                    </div>
                </form>
            </div>
            
            <div class="scene__footer">
                <div class="footer__total">
                    <span class="total__label">Итого к оплате:</span>
                    <span class="total__value" cart-checkout-total>0 ₽</span>
                </div>
                <button class="footer__submit-btn" cart-submit-order>Оформить заказ</button>
            </div>
        </div>
        
        <!-- Scene 3: Result -->
        <div class="wrapper__scene scene_result" data-cart-scene="result">
            <div class="scene__content">
                <div class="content__result content__result--success" cart-result-success>
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <h2>Заказ оформлен!</h2>
                    <p>Спасибо за ваш заказ. В ближайшее время с вами свяжется наш менеджер.</p>
                </div>
                
                <div class="content__result content__result--error" cart-result-error>
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h2>Что-то пошло не так</h2>
                    <p>К сожалению, не удалось оформить заказ. Пожалуйста, попробуйте позже.</p>
                </div>
                
                <button class="content__continue-btn" cart-continue-shopping>Вернуться к покупкам</button>
            </div>
        </div>
    </div>
</div>
<?php include_once('components/cart-product-template.php'); ?>