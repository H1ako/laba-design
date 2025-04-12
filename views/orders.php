<?php

use app\models\Product;

global $SITE_URL, $session;

$status = $data['status'] ?? null;
$message = $data['message'] ?? null;
$orders = $data['orders'] ?? [];
$access = $data['access'] ?? null;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once('components/base-head.php'); ?>
    <title>Мои заказы</title>
    <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/orders.css">
    <script>
        const SITE_URL = '<?= $SITE_URL ?>';
    </script>
    <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/fetchHandlers.js"></script>
    <script type="module" defer src="<?= $SITE_URL ?>/assets/scripts/libs/marquee.js"></script>
    <script defer src="<?= $SITE_URL ?>/assets/scripts/orders.js"></script>
</head>

<body>
    <?php include_once('components/banner.php'); ?>
    <?php include_once('components/header.php'); ?>
    <main class="main-content">
        <div class="orders-container">
            <h1 class="orders__title">MY ORDERS</h1>

            <?php if ($status === 'form'): ?>
                <div class="orders__access-form">
                    <p class="form__description">
                        Для просмотра заказов, пожалуйста, введите email адрес, который вы использовали при оформлении заказа.
                        На этот адрес будет отправлена ссылка для доступа к вашим заказам.
                    </p>
                    <form class="form__content" id="access-form">
                        <div class="form__field">
                            <label for="email">Email*</label>
                            <input type="email" id="email" name="email" required placeholder="example@email.com">
                            <span class="field__error" data-error="email"></span>
                        </div>
                        <div class="form__actions">
                            <button type="submit" class="form__submit">Получить доступ</button>
                        </div>
                    </form>
                    <div class="form__result">
                        <div class="result__success" id="form-success" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <p class="success__message">Ссылка отправлена! Проверьте вашу электронную почту.</p>
                        </div>
                        <div class="result__error" id="form-error" style="display: none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <p class="error__message" id="error-message"></p>
                        </div>
                    </div>
                </div>
            <?php elseif ($status === 'error'): ?>
                <div class="orders__error-message">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <p class="error__text"><?= $message ?></p>
                    <a href="<?= Router::getRoute('/orders') ?>" class="error__link">Вернуться к форме доступа</a>
                </div>
            <?php elseif ($status === 'success'): ?>
                <?php if (count($orders) === 0): ?>
                    <div class="orders__empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        <h2>У вас пока нет заказов</h2>
                        <p>Посетите наш каталог, чтобы сделать первый заказ</p>
                        <a href="<?= Router::getRoute('/catalog') ?>" class="empty__link">Перейти в каталог</a>
                    </div>
                <?php else: ?>
                    <p class="orders__access-info">Доступ для: <strong><?= htmlspecialchars($access->email) ?></strong></p>
                    <ul class="orders__list">
                        <?php foreach ($orders as $order): ?>
                            <li class="list__item">
                                <a href="<?= Router::getRoute('/orders/' . $order->id, ['key' => $access->key, 'email' => $access->email]) ?>" class="item__content">
                                    <div class="item__header">
                                        <h3 class="header__title">Заказ #<?= $order->id ?></h3>
                                        <span class="header__date"><?= $order->created_at_formatted ?></span>
                                        <div class="header__status header__status--<?= $order->status ?>">
                                            <?php
                                            $statusText = '';
                                            switch ($order->status) {
                                                case 'initial':
                                                    $statusText = 'Создан';
                                                    break;
                                                case 'working':
                                                    $statusText = 'В работе';
                                                    break;
                                                case 'success':
                                                    $statusText = 'Выполнен';
                                                    break;
                                                case 'canceled':
                                                    $statusText = 'Отменен';
                                                    break;
                                            }
                                            echo $statusText;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="item__details">
                                        <div class="details__customer">
                                            <p class="customer__name"><?= htmlspecialchars($order->customer_full_name) ?></p>
                                            <p class="customer__contact"><?= htmlspecialchars($order->customer_phone_number) ?></p>
                                            <p class="customer__address truncate-text"><?= htmlspecialchars($order->customer_address) ?></p>
                                        </div>
                                        <div class="details__summary">
                                            <div class="summary__item">
                                                <span class="item__label">Товаров:</span>
                                                <span class="item__value"><?= $order->total_quantity ?> шт.</span>
                                            </div>
                                            <?php if ($order->total_discount_sum > 0): ?>
                                                <div class="summary__item summary__item--discount">
                                                    <span class="item__label">Скидка:</span>
                                                    <span class="item__value">-<?= $order->total_discount_sum_formatted ?> ₽</span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="summary__item summary__item--total">
                                                <span class="item__label">Итого:</span>
                                                <span class="item__value"><?= $order->total_price_formatted ?> ₽</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="item__products">
                                        <?php
                                        $items = $order->items;
                                        $displayCount = min($items->count(), 4);
                                        $remainder = $items->count() - $displayCount;

                                        for ($i = 0; $i < $displayCount; $i++):
                                            $item = $items[$i];
                                            $product = $item->product;
                                            if (!$product) continue;
                                        ?>
                                            <div class="products__thumbnail">
                                                <img src="<?= $product->thumb_url ?>" alt="<?= $product->name ?>">
                                            </div>
                                        <?php endfor; ?>

                                        <?php if ($remainder > 0): ?>
                                            <div class="products__remainder">
                                                <span>+<?= $remainder ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php include_once('components/brand-marquee.php'); ?>
    <?php include_once('components/footer.php'); ?>
</body>

</html>