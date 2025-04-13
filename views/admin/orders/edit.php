<?php

use app\models\Session;

global $SITE_URL;
$title = 'Редактирование заказа | Админ-панель';
$order = $data['order'] ?? null;
$statuses = $data['statuses'] ?? ['initial', 'working', 'success', 'canceled'];

if (!$order) {
    header('Location: ' . Router::getRoute('/admin/orders'));
    exit;
}

$header_actions = [
    [
        'label' => 'Назад к заказу',
        'url' => Router::getRoute('/admin/orders/' . $order->id),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Редактирование заказа #<?= $order->id ?></h2>
    </div>
    
    <div class="card-body">
        <form id="order-form" action="<?= Router::getRoute('/api/admin/orders/' . $order->id) ?>" method="post">
            <h3 class="form-section-title">Информация о покупателе</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="customer_full_name">ФИО*</label>
                    <input type="text" id="customer_full_name" name="customer_full_name" class="form-control" value="<?= htmlspecialchars($order->customer_full_name) ?>" required>
                    <div class="invalid-feedback" data-error-for="customer_full_name"></div>
                </div>
                
                <div class="form-group">
                    <label for="customer_email">Email*</label>
                    <input type="email" id="customer_email" name="customer_email" class="form-control" value="<?= htmlspecialchars($order->customer_email) ?>" required>
                    <div class="invalid-feedback" data-error-for="customer_email"></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="customer_phone_number">Телефон*</label>
                    <input type="text" id="customer_phone_number" name="customer_phone_number" class="form-control" value="<?= htmlspecialchars($order->customer_phone_number) ?>" required>
                    <div class="invalid-feedback" data-error-for="customer_phone_number"></div>
                </div>
                
                <div class="form-group">
                    <label for="status">Статус заказа*</label>
                    <select id="status" name="status" class="form-control" required>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= $order->status === $status ? 'selected' : '' ?>>
                                <?php
                                switch ($status) {
                                    case 'initial': echo 'Новый'; break;
                                    case 'working': echo 'В работе'; break;
                                    case 'success': echo 'Выполнен'; break;
                                    case 'canceled': echo 'Отменен'; break;
                                    default: echo ucfirst($status);
                                }
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback" data-error-for="status"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="customer_address">Адрес доставки*</label>
                <textarea id="customer_address" name="customer_address" class="form-control" rows="3" required><?= htmlspecialchars($order->customer_address) ?></textarea>
                <div class="invalid-feedback" data-error-for="customer_address"></div>
            </div>
            
            <h3 class="form-section-title">Информация о заказе</h3>
            
            <div class="form-group">
                <label for="discount_value">Дополнительная скидка (₽)</label>
                <input type="number" id="discount_value" name="discount_value" class="form-control" value="<?= $order->discount_value ?>" min="0" step="1">
                <div class="invalid-feedback" data-error-for="discount_value"></div>
                <small class="form-text text-muted">Дополнительная скидка, которая будет применена ко всему заказу.</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                <a href="<?= Router::getRoute('/admin/orders/' . $order->id) ?>" class="btn btn-outline-secondary">Отменить</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['orders.js'];
include('views/admin/layout.php');
?>