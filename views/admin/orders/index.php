<?php

use app\models\Session;

global $SITE_URL;
$title = 'Заказы | Админ-панель';
$orders = $data['orders'] ?? [];
$total_orders = $data['total_orders'] ?? 0;
$current_page = $data['current_page'] ?? 1;
$total_pages = $data['total_pages'] ?? 1;
$status_filter = $data['status_filter'] ?? '';
$search = $data['search'] ?? '';

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Список заказов</h2>
        <div class="header-actions">
            <div class="filters">
                <form action="<?= Router::getRoute('/admin/orders') ?>" method="get" class="filters-form" id="order-filters-form">
                    <div class="filter-group">
                        <select name="status" id="status-filter" class="form-control">
                            <option value="">Все статусы</option>
                            <option value="initial" <?= $status_filter === 'initial' ? 'selected' : '' ?>>Новый</option>
                            <option value="working" <?= $status_filter === 'working' ? 'selected' : '' ?>>В работе</option>
                            <option value="success" <?= $status_filter === 'success' ? 'selected' : '' ?>>Выполнен</option>
                            <option value="canceled" <?= $status_filter === 'canceled' ? 'selected' : '' ?>>Отменен</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Поиск по имени, email..." value="<?= htmlspecialchars($search) ?>" class="form-control">
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="btn btn-primary">Применить</button>
                        <?php if ($status_filter || $search): ?>
                            <a href="<?= Router::getRoute('/admin/orders') ?>" class="btn btn-outline-secondary">Сбросить</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <?php if ($orders->count() === 0): ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <p>Заказы не найдены</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Клиент</th>
                            <th>Контакты</th>
                            <th>Статус</th>
                            <th>Товары</th>
                            <th>Сумма</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr data-order-id="<?= $order->id ?>">
                                <td><a href="<?= Router::getRoute('/admin/orders/' . $order->id) ?>" class="link">#<?= $order->id ?></a></td>
                                <td><?= htmlspecialchars($order->customer_full_name) ?></td>
                                <td>
                                    <div><?= htmlspecialchars($order->customer_email) ?></div>
                                    <div><?= htmlspecialchars($order->customer_phone_number) ?></div>
                                </td>
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
                                <td><?= date('d.m.Y H:i', strtotime($order->created_at)) ?></td>
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
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Показано <?= ($current_page - 1) * 20 + 1 ?>-<?= min($current_page * 20, $total_orders) ?> из <?= $total_orders ?> заказов
                    </div>
                    <div class="pagination-controls">
                        <?php if ($current_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/orders', ['page' => $current_page - 1, 'status' => $status_filter, 'search' => $search]) ?>" class="pagination-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        
                        if ($start_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/orders', ['page' => 1, 'status' => $status_filter, 'search' => $search]) ?>" class="pagination-btn">1</a>
                            <?php if ($start_page > 2): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="<?= Router::getRoute('/admin/orders', ['page' => $i, 'status' => $status_filter, 'search' => $search]) ?>" class="pagination-btn <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                            <a href="<?= Router::getRoute('/admin/orders', ['page' => $total_pages, 'status' => $status_filter, 'search' => $search]) ?>" class="pagination-btn"><?= $total_pages ?></a>
                        <?php endif; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?= Router::getRoute('/admin/orders', ['page' => $current_page + 1, 'status' => $status_filter, 'search' => $search]) ?>" class="pagination-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['orders.js'];
include('views/admin/layout.php');
?>