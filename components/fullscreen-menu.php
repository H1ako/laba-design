<div class="fullscreen-menu" id="fullscreen-menu">
    <div class="fullscreen-menu__overlay"></div>
    <div class="fullscreen-menu__container">
        <button class="fullscreen-menu__close">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="fullscreen-menu__content">
            <div class="content__column column-main">
                <h3 class="column__title">Main</h3>
                <nav class="column__nav">
                    <ul class="nav__list">
                        <li class="list__item">
                            <a href="<?= Router::getRoute('/') ?>" class="item__link">Главная</a>
                        </li>
                        <li class="list__item">
                            <a href="<?= Router::getRoute('/orders') ?>" class="item__link">Мои заказы</a>
                        </li>
                        <li class="list__item">
                            <a href="<?= Router::getRoute('/catalog') ?>" class="item__link">Каталог</a>
                        </li>
                        <li class="list__item">
                            <a href="<?= Router::getRoute('/news') ?>" class="item__link">Новости</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="content__column column-company">
                <h3 class="column__title">Company</h3>
                <nav class="column__nav">
                    <ul class="nav__list">
                        <li class="list__item">
                            <a href="#" class="item__link">Политика конфиденциальности</a>
                        </li>
                        <li class="list__item">
                            <a href="#" class="item__link">Как работаем</a>
                        </li>
                        <li class="list__item">
                            <a href="#" class="item__link">Обработка заказов</a>
                        </li>
                        <li class="list__item">
                            <a href="#" class="item__link">Скидки и цены</a>
                        </li>
                        <li class="list__item">
                            <a href="#" class="item__link">Контакты</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="content__column column-contacts">
                <h3 class="column__title">Contact Us</h3>
                <div class="column__phones">
                    <p class="phones__tip">Звонок бесплатный</p>
                    <ul class="phones__list">
                        <li class="list__item">
                            <a href="tel:+79121234567" class="item__link">
                                +7 (912) 123-45-67
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="column__socials">
                    <?php include('components/socials.php'); ?>
                </div>
            </div>
        </div>

        <div class="fullscreen-menu__about">
            <div class="about__container">
                <h3 class="about__title">About Us</h3>
                <p class="about__text">
                    Время приёма заказов: круглосуточно 7 дней в неделю.
                    Время обработки заказов, прием звонков: пн-пт.
                    c 9-00 до 21-00 (по Московскому времени).
                </p>
            </div>
        </div>
    </div>
</div>