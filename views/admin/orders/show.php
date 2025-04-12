<?php

use app\models\Session;

global $SITE_URL;
$title = 'Заказ | Админ-панель';
$order = $data['order'] ?? null;

if (!$order) {
    header('Location: ' . Router::getRoute('/admin/orders'));
    exit;
}

$header_actions = [
    [
        'label' => 'Редактировать',
        'url' => Router::getRoute('/admin/orders/' . $order->id . '/edit'),
        'class' => 'btn-primary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>'
    ],
    [
        'label' => 'Назад к списку',
        'url' => Router::getRoute('/admin/orders'),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="order-view">
    <!-- Order Header -->
    <div class="card">
        <div class="card-header">
            <div class="order-header">
                <div class="order-id">
                    <h2>Заказ #<?= $order->id ?></h2>
                    <span class="order-date"><?= date('d.m.Y H:i', strtotime($order->created_at)) ?></span>
                </div>
                <div class="order-status">
                    <div class="status-selector">
                        <div class="status-label">Статус:</div>
                        <div class="status-buttons" data-order-id="<?= $order->id ?>">
                            <button class="status-btn status-btn--initial <?= $order->status === 'initial' ? 'active' : '' ?>" 
                                    data-order-status-update data-order-id="<?= $order->id ?>" data-status="initial">
                                Новый
                            </button>
                            <button class="status-btn status-btn--working <?= $order->status === 'working' ? 'active' : '' ?>"
                                    data-order-status-update data-order-id="<?= $order->id ?>" data-status="working">
                                В работе
                            </button>
                            <button class="status-btn status-btn--success <?= $order->status === 'success' ? 'active' : '' ?>"
                                    data-order-status-update data-order-id="<?= $order->id ?>" data-status="success">
                                Выполнен
                            </button>
                            <button class="status-btn status-btn--canceled <?= $order->status === 'canceled' ? 'active' : '' ?>"
                                    data-order-status-update data-order-id="<?= $order->id ?>" data-status="canceled">
                                Отменен
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="order-sections">
        <!-- Customer Information -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Информация о покупателе</h2>
                <a href="<?= Router::getRoute('/admin/orders/' . $order->id . '/edit') ?>" class="btn btn-sm btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Редактировать
                </a>
            </div>
            <div class="card-body">
                <div class="customer-info">
                    <div class="info-row">
                        <div class="info-label">ФИО:</div>
                        <div class="info-value"><?= htmlspecialchars($order->customer_full_name) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value"><?= htmlspecialchars($order->customer_email) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Телефон:</div>
                        <div class="info-value"><?= htmlspecialchars($order->customer_phone_number) ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Адрес:</div>
                        <div class="info-value"><?= htmlspecialchars($order->customer_address) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Сводка заказа</h2>
            </div>
            <div class="card-body">
                <div class="order-summary">
                    <div class="summary-row">
                        <div class="summary-label">Количество товаров:</div>
                        <div class="summary-value"><?= $order->total_quantity ?> шт.</div>
                    </div>
                    <?php if ($order->total_discount_sum > 0): ?>
                    <div class="summary-row">
                        <div class="summary-label">Скидка:</div>
                        <div class="summary-value discount"><?= $order->total_discount_sum_formatted ?> ₽</div>
                    </div>
                    <?php endif; ?>
                    <?php if ($order->discount_value > 0): ?>
                    <div class="summary-row">
                        <div class="summary-label">Доп. скидка:</div>
                        <div class="summary-value discount"><?= static::format_price($order->discount_value) ?> ₽</div>
                    </div>
                    <?php endif; ?>
                    <div class="summary-row total">
                        <div class="summary-label">Итого:</div>
                        <div class="summary-value"><?= $order->total_price_formatted ?> ₽</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Товары в заказе</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Товар</th>
                            <th>Размер</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Сумма</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order->items as $item):
                            $product = $item->product;
                            if (!$product) continue;
                        ?>
                        <tr>
                            <td>
                                <div class="product-cell">
                                    <img src="<?= $product->thumb_url ?>" alt="<?= htmlspecialchars($product->name) ?>" class="product-thumbnail">
                                    <a href="<?= Router::getRoute('/admin/products/' . $product->id) ?>"><?= htmlspecialchars($product->name) ?></a>
                                </div>
                            </td>
                            <td>
                                <?= $item->size ? htmlspecialchars($item->size) : '<span class="text-muted">-</span>' ?>
                            </td>
                            <td>
                                <?php if ($item->discount_sum > 0): ?>
                                    <div class="price-with-discount">
                                        <span class="current-price"><?= $item->price_formatted ?> ₽</span>
                                        <span class="original-price"><?= $item->price_original_formatted ?> ₽</span>
                                    </div>
                                <?php else: ?>
                                    <?= $item->price_formatted ?> ₽
                                <?php endif; ?>
                            </td>
                            <td><?= $item->quantity ?></td>
                            <td><?= $item->total_price_formatted ?> ₽</td>
                            <td>
                                <button class="btn btn-sm btn-danger" 
                                        data-order-item-remove 
                                        data-order-id="<?= $order->id ?>" 
                                        data-item-id="<?= $item->id ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($order->items) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center">В заказе нет товаров</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Order Button -->
    <div class="card">
        <div class="card-body">
            <div class="danger-zone">
                <h3>Удаление заказа</h3>
                <p>Это действие нельзя отменить. Будут удалены все данные, связанные с этим заказом.</p>
                <button class="btn btn-danger" data-order-delete data-order-id="<?= $order->id ?>">Удалить заказ</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['orders.js'];
include('views/admin/layout.php');
?>