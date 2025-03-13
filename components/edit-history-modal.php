<?php global $SITE_URL, $session; ?>
<dialog id="edit-history-modal" class="edit-history-modal">
    <div class="edit-history-modal__container">
        <button class="container__close" close-btn>
            <?php include('icons/close.php'); ?>
            <span>Отмена</span>
        </button>
        <header class="container__service-card">
            <div class="service-card__img">
                <img src="<?= $SITE_URL ?>/assets/images/service.webp" service-img alt="Утепление дома">
            </div>
            <div class="service-card__description">
                <h3 class="description__title" service-name>Утепление дома</h3>
                <p class="description__about"><span service-time>1 неделя</span> | <span service-workers>10 сотрудников</span></p>
                <p class="description__price"><span service-price>20 000</span>руб.</p>
            </div>
        </header>
        <form id="edit-history-form" class="container__form">
            <?php $session->set_csrf(); ?>
            <input type="hidden" name="history_id" value="1" history-id>
            <div class="container__group">
                <label class="container__input">
                    <?php include('icons/phone.php'); ?>
                    <input type="phone" name="phone" phone-field placeholder="Номер телефона" required history-phone>
                </label>
                <label class="container__input">
                    <?php include('icons/address.php'); ?>
                    <input type="text" name="address" address-field placeholder="Адрес" required history-address>
                </label>
            </div>
            <div class="container__row">
                <label class="container__input">
                    <?php include('icons/date.php'); ?>
                    <input type="date" name="date" date-field placeholder="Дата" required history-date>
                </label>
                <label class="container__input">
                    <?php include('icons/time.php'); ?>
                    <input type="time" name="time" time-field placeholder="Время" required history-time>
                </label>
            </div>
            <div class="container__group">
                <p class="container__message">Мы перезвоним вам в течение 24 часов для подтвреждения</p>
                <button class="container__submit" type="submit">
                    <span class="submit__icon-container">
                        <svg
                            viewBox="0 0 14 15"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            class="button__icon-svg"
                            width="10">
                            <path
                                d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                                fill="currentColor"></path>
                        </svg>
                        <svg
                            viewBox="0 0 14 15"
                            fill="none"
                            width="10"
                            xmlns="http://www.w3.org/2000/svg"
                            class="button__icon-svg button__icon-svg--copy">
                            <path
                                d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                                fill="currentColor"></path>
                        </svg>
                    </span>
                    <span class="submit__text">Отредактировать</span>
                </button>
                <div class="container__status status_error status_400">
                    <?php include('icons/error.php'); ?>
                    <span class="status__description">
                        <p class="description__title">Попробуйте еще раз</p>
                        <p class="description__message">Введенные данные не соответствуют требованиям:</p>
                        <ul class="description__list">
                            <li>
                                <p>Все поля обязательны</p>
                            </li>
                            <li>
                                <p>Мин. длина Адреса 8 символов, макс. - 160</p>
                            </li>
                        </ul>
                    </span>
                </div>
            </div>
        </form>
    </div>
</dialog>