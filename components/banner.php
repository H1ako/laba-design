<div class="banner" data-scene="1">
    <div class="banner__logo">
        <?php include_once('components/logo.php'); ?>
    </div>
    <div class="banner__content">
        <div class="content__scene scene_1">
            <img class="scene__image" src="<?= $SITE_URL ?>/assets/images/military.png" alt="Military">
            <p class="scene__text">Подпиши контракт по нашему рефералу и получи промокод на скидку 50%</p>
            <a href="" class="scene__button">подробнее</a>
        </div>
        <div class="content__scene scene_2">
            <p class="scene__text">Время приёма заказов: круглосуточно 7 дней в неделю.</p>
            <div class="scene__phone">+7 (912) 123-45-67</div>
        </div>
        <div class="content__scene scene_3">
            <p class="scene__text">Скидка на весь ассортимент 30%</p>
            <a href="<?= Router::getRoute('/catalog') ?>" class="scene__button">перейти в каталог</a>
        </div>
    </div>
</div>