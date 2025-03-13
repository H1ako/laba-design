<footer class="main-footer" aria-label="Подвал сайта">
    <div class="main-footer__container">
        <div class="container__content">
            <div class="content__col col_socials">
                <div class="content__socials">
                    <?php include('components/socials.php'); ?>
                </div>
                <div class="content__phones">
                    <p class="phones__tip">Звонок бесплатный</p>
                    <ul class="phones__list">
                        <li class="list__item">
                            <a href="tel:+79121234567" class="item__link">
                                +7 (912) 123-45-67
                            </a>
                        </li>
                        <li class="list__item">
                            <a href="tel:+79129876543" class="item__link">
                                +7 (912) 987-65-43
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="content__col col_nav">
                <p class="content__title">For Users</p>
                <nav class="content__nav" aria-label="Навигация пользователя">
                    <ul class="nav__list">
                        <li class="list__el">
                            <a href="#" class="el__link">Политика конфиденциальности</a>
                        </li>
                        <li class="list__el">
                            <a href="#" class="el__link">Как работаем</a>
                        </li>
                        <li class="list__el">
                            <a href="#" class="el__link">Обработка заказов</a>
                        </li>
                        <li class="list__el">
                            <a href="#" class="el__link">Скидки и цены</a>
                        </li>
                        <li class="list__el">
                            <a href="#" class="el__link">Контакты</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="content__col col_info">
                <p class="content__title">About Us</p>
                <p class="content__message">
                    Время приёма заказов: круглосуточно 7 дней в неделю.<br>
                    Время обработки заказов, прием звонков: пн-пт.<br>
                    c 9-00 до 21-00 (по Московскому времени).
                </p>
            </div>
        </div>
        <p class="container__rights">Bce права защищены © 2024</p>
    </div>
</footer>
<?php include_once('components/loading-screen.php'); ?>