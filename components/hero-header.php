<header class="hero-header">
    <div class="hero-header__bg">
        <div class="bg-part-wrapper">
            <?php include('components/bg-part-1.php'); ?>
        </div>
        <div class="bg-part-wrapper">
            <?php include('components/bg-part-2.php'); ?>
        </div>
        <div class="bg-part-wrapper">
            <?php include('components/bg-part-3.php'); ?>
        </div>
    </div>
    
    <div class="hero-header__top-section">
        <button class="menu-button" id="menu-button" aria-label="Open menu">
            <span class="menu-button__icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </span>
            <span class="menu-button__title">Меню</span>
        </button>
        
        <div class="top-section__control">
            <div class="control__my-orders">
                <a href="<?= Router::getRoute('/orders') ?>" class="my-orders__link">Мои заказы</a>
            </div>
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

    <!-- Fixed header elements that appear on scroll -->
    <div class="hero-header__fixed">
        <button class="fixed__menu-button" id="fixed-menu-button" aria-label="Open menu">
            <div class="menu-button__wrapper">
                <span class="menu-button__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </span>
                <span class="menu-button__title">Меню</span>
            </div>
        </button>
        
        <div class="fixed__basket" id="fixed-basket">
            <?php include('components/basket-link.php'); ?>
        </div>
    </div>
</header>

<?php include('components/fullscreen-menu.php'); ?>