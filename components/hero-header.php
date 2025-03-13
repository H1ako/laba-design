<header class="hero-header">
    <div class="hero-header__bg">
        <?php include('components/bg-part-1.php'); ?>
        <?php include('components/bg-part-2.php'); ?>
        <?php include('components/bg-part-3.php'); ?>
    </div>
    <div class="hero-header__top-section">
        <nav class="top-section__nav">
            <ul class="nav__list">
                <li class="list__item">
                    <a href="<?= Router::getRoute('/') ?>" class="item__link">Home</a>
                </li>
                <li class="list__item">
                    <a href="<?= Router::getRoute('/catalog') ?>" class="item__link">Catalog</a>
                </li>
                <li class="list__item">
                    <a href="<?= Router::getRoute('/news') ?>" class="item__link">News</a>
                </li>
            </ul>
        </nav>
        <div class="top-section__control">
            <div class="control__basket">
                <?php include('components/basket-link.php'); ?>
            </div>
        </div>
    </div>
    <div class="hero-header__main-section">
        <div class="main-section__logo">
            <?php include('components/logo.php'); ?>
        </div>
    </div>
</header>