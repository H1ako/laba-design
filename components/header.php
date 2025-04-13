<header class="main-header">
    <div class="main-header__bg">
        <?php include('components/bg-part-1.php'); ?>
        <?php include('components/bg-part-3.php'); ?>
    </div>
    
    <div class="main-header__top-section">
        <button class="top-section__menu-button" id="menu-button" aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
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
    
    <div class="main-header__main-section">
        <div class="main-section__logo">
            <?php include('components/logo.php'); ?>
        </div>
    </div>

    <!-- Fixed header elements that appear on scroll -->
    <div class="main-header__fixed">
        <button class="fixed__menu-button" id="fixed-menu-button" aria-label="Open menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        
        <div class="fixed__basket" id="fixed-basket">
            <?php include('components/basket-link.php'); ?>
        </div>
    </div>
</header>

<?php include('components/fullscreen-menu.php'); ?>