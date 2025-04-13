<?php

use app\models\Session;

global $SITE_URL;
$title = 'Просмотр клиента | Админ-панель';
$customer = $data['customer'] ?? null;
$orders = $data['orders'] ?? [];
$total_spent = $data['total_spent'] ?? 0;

if (!$customer) {
    header('Location: ' . Router::getRoute('/admin/users'));
    exit;
}

$header_actions = [
    [
        'label' => 'Вернуться к списку',
        'url' => Router::getRoute('/admin/users'),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="customer-view">
    <!-- Customer Header -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Информация о клиенте</h2>
        </div>
        <div class="card-body">
            <div class="customer-info">
                <div class="info-row">
                    <div class="info-label">ФИО:</div>
                    <div class="info-value"><?= htmlspecialchars($customer->customer_full_name) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?= htmlspecialchars($customer->customer_email) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Телефон:</div>
                    <div class="info-value"><?= htmlspecialchars($customer->customer_phone_number) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Адрес:</div>
                    <div class="info-value"><?= htmlspecialchars($customer->customer_address) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Всего заказов:</div>
                    <div class="info-value"><?= count($orders) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Общая сумма заказов:</div>
                    <div class="info-value"><?= number_format($total_spent, 0, '', ' ') ?> ₽</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Orders -->
    <div class="card mt-4">
        <div class="card-header">
            <h2 class="card-title">История заказов</h2>
        </div>
        <div class="card-body">
            <?php if (count($orders) === 0): ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <p>У клиента нет заказов</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>№</th>
                                <th>Дата</th>
                                <th>Статус</th>
                                <th>Товаров</th>
                                <th>Сумма</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr data-order-id="<?= $order->id ?>">
                                    <td><a href="<?= Router::getRoute('/admin/orders/' . $order->id) ?>" class="link">#<?= $order->id ?></a></td>
                                    <td><?= date('d.m.Y H:i', strtotime($order->created_at)) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $order->status ?> order-status">
                                            <?php
                                            switch ($order->status) {
                                                case 'initial':
                                                    echo 'Новый';
                                                    break;
                                                case 'working':
                                                    echo 'В работе';
                                                    break;
                                                case 'success':
                                                    echo 'Выполнен';
                                                    break;
                                                case 'canceled':
                                                    echo 'Отменен';
                                                    break;
                                                default:
                                                    echo $order->status;
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= $order->total_quantity ?></td>
                                    <td><?= $order->total_price_formatted ?> ₽</td>
                                    <td>
                                        <div class="actions">
                                            <a href="<?= Router::getRoute('/admin/orders/' . $order->id) ?>" class="btn btn-sm btn-info" title="Просмотр">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </a>
                                            <a href="<?= Router::getRoute('/admin/orders/' . $order->id . '/edit') ?>" class="btn btn-sm btn-primary" title="Редактировать">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </a>
                                            <button class="btn btn-sm btn-danger" data-order-delete data-order-id="<?= $order->id ?>" title="Удалить" data-confirm="Вы уверены, что хотите удалить заказ #<?= $order->id ?>?">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Delete Customer Button -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="danger-zone">
                <h3>Удаление клиента</h3>
                <p>Это действие нельзя отменить. Будут удалены все данные клиента, в заказаз будет поставлен "Удаленный клиент".</p>
                <button class="btn btn-danger" data-user-delete data-user-id="<?= $customer->id ?>" data-confirm="Вы уверены, что хотите удалить клиента <?= htmlspecialchars($customer->customer_full_name) ?>?">Удалить клиента</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['users.js', 'orders.js'];
include('views/admin/layout.php');
?>