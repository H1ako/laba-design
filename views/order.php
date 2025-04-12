<?php

global $SITE_URL, $session;

$items = $order->items;
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once('components/base-head.php'); ?>
  <title>Заказ #<?= $order->id ?></title>
  <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/order.css">
  <script>
    const SITE_URL = '<?= $SITE_URL ?>';
  </script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/fetchHandlers.js"></script>
  <script type="module" defer src="<?= $SITE_URL ?>/assets/scripts/libs/marquee.js"></script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/order.js"></script>
</head>

<body>
  <?php include_once('components/banner.php'); ?>
  <?php include_once('components/header.php'); ?>
  <main class="main-content">
    <div class="order-container">
      <div class="order__navigation">
        <a href="<?= Router::getRoute('/orders', ['key' => $access->key, 'email' => $access->email]) ?>" class="navigation__back">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          <span>Вернуться к списку заказов</span>
        </a>
      </div>

      <div class="order__header">
        <h1 class="header__title">Заказ #<?= $order->id ?></h1>
        <div class="header__date">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
          </svg>
          <span><?= $order->created_at_formatted ?></span>
        </div>
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

      <div class="order__details">
        <div class="details__section details__customer">
          <h2 class="section__title">Информация о покупателе</h2>
          <div class="customer__info">
            <div class="info__group">
              <div class="info__item">
                <span class="item__label">ФИО:</span>
                <span class="item__value"><?= htmlspecialchars($order->customer_full_name) ?></span>
              </div>
              <div class="info__item">
                <span class="item__label">Email:</span>
                <span class="item__value"><?= htmlspecialchars($order->customer_email) ?></span>
              </div>
              <div class="info__item">
                <span class="item__label">Телефон:</span>
                <span class="item__value"><?= htmlspecialchars($order->customer_phone_number) ?></span>
              </div>
            </div>
            <div class="info__group">
              <div class="info__item">
                <span class="item__label">Адрес доставки:</span>
                <span class="item__value address-value"><?= htmlspecialchars($order->customer_address) ?></span>
              </div>
            </div>
          </div>
        </div>

        <div class="details__section details__summary">
          <h2 class="section__title">Сводка заказа</h2>
          <div class="summary__info">
            <div class="info__item">
              <span class="item__label">Количество товаров:</span>
              <span class="item__value"><?= $order->total_quantity ?> шт.</span>
            </div>
            <?php if ($order->total_discount_sum > 0): ?>
              <div class="info__item">
                <span class="item__label">Общая скидка:</span>
                <span class="item__value discount-value">-<?= $order->total_discount_sum_formatted ?> ₽</span>
              </div>
            <?php endif; ?>
            <?php if ($order->discount_value > 0): ?>
              <div class="info__item">
                <span class="item__label">Дополнительная скидка:</span>
                <span class="item__value discount-value">-<?= static::format_price($order->discount_value) ?> ₽</span>
              </div>
            <?php endif; ?>
            <div class="info__item total-item">
              <span class="item__label">Итоговая стоимость:</span>
              <span class="item__value total-value"><?= $order->total_price_formatted ?> ₽</span>
            </div>
          </div>
        </div>
      </div>

      <h2 class="products__title">Товары в заказе</h2>
      <div class="order__products">
        <ul class="products__list">
          <?php foreach ($items as $item):
            $product = $item->product;
            if (!$product) continue;
          ?>
            <li class="list__item">
              <div class="item__image">
                <?php if ($item->discount_sum > 0): ?>
                  <div class="image__discount-badge">-<?= $item->discount ?>%</div>
                <?php endif; ?>
                <img src="<?= $product->thumb_url ?>" alt="<?= htmlspecialchars($product->name) ?>" class="image__src">
              </div>
              <div class="item__content">
                <h3 class="content__name"><?= htmlspecialchars($product->name) ?></h3>

                <!-- Add size display -->
                <div class="content__size">
                  <span class="size__label">Размер:</span>
                  <span class="size__value <?= $item->size ? '' : 'size__value--not-selected' ?>">
                    <?= $item->size ? htmlspecialchars($item->size) : 'Размер не выбран' ?>
                  </span>
                </div>

                <p class="content__description"><?= substr(htmlspecialchars($product->description), 0, 100) ?>...</p>

                <div class="content__info">
                  <div class="info__pricing">
                    <div class="pricing__item">
                      <span class="item__label">Цена за шт:</span>
                      <span class="item__value">
                        <?php if ($item->discount_sum > 0): ?>
                          <span class="value__discounted"><?= $item->price_formatted ?> ₽</span>
                          <span class="value__original"><?= $item->price_original_formatted ?> ₽</span>
                        <?php else: ?>
                          <?= $item->price_formatted ?> ₽
                        <?php endif; ?>
                      </span>
                    </div>
                    <div class="pricing__item">
                      <span class="item__label">Количество:</span>
                      <span class="item__value"><?= $item->quantity ?> шт.</span>
                    </div>
                    <?php if ($item->discount_sum > 0): ?>
                      <div class="pricing__item">
                        <span class="item__label">Скидка:</span>
                        <span class="item__value discount-value">-<?= $item->discount_sum_formatted ?> ₽</span>
                      </div>
                    <?php endif; ?>
                    <div class="pricing__item total-item">
                      <span class="item__label">Итого:</span>
                      <span class="item__value total-value"><?= $item->total_price_formatted ?> ₽</span>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </main>
  <?php include_once('components/brand-marquee.php'); ?>
  <?php include_once('components/footer.php'); ?>
</body>

</html>