<?php
global $SITE_URL;
$title = 'Пользователи | Админ-панель';
$users = $data['users'] ?? [];
$admin_users = $data['admin_users'] ?? [];
$total_users = $data['total_users'] ?? 0;
$current_page = $data['current_page'] ?? 1;
$total_pages = $data['total_pages'] ?? 1;
$search = $data['search'] ?? '';

use app\models\Session;

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Клиенты</h2>
        <div class="header-actions">
            <div class="filters">
                <form action="<?= Router::getRoute('/admin/users') ?>" method="get" class="filters-form" id="user-search-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Поиск по имени или email..." value="<?= htmlspecialchars($search) ?>" class="form-control">
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="btn btn-primary">Найти</button>
                        <?php if ($search): ?>
                            <a href="<?= Router::getRoute('/admin/users') ?>" class="btn btn-outline-secondary">Сбросить</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <?php if ($users->count() === 0): ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <p>Клиенты не найдены</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>Адрес</th>
                            <th>Заказов</th>
                            <th>Сумма заказов</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $order): ?>
                            <tr data-user-id="<?= $order->id ?>">
                                <td><?= htmlspecialchars($order->customer_full_name) ?></td>
                                <td><?= htmlspecialchars($order->customer_email) ?></td>
                                <td><?= htmlspecialchars($order->customer_phone_number) ?></td>
                                <td class="truncate-text"><?= htmlspecialchars($order->customer_address) ?></td>
                                <td><a href="<?= Router::getRoute('/admin/orders', ['search' => $order->customer_email]) ?>"><?= $order->orders_count ?></a></td>
                                <td><?= isset($order->total_spent) ? $order->total_spent . ' ₽' : '-' ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="<?= Router::getRoute('/admin/users/' . $order->id) ?>" class="btn btn-sm btn-info" title="Просмотр">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                        <button class="btn btn-sm btn-danger" data-user-delete data-user-id="<?= $order->id ?>" title="Удалить" data-confirm="Вы уверены, что хотите удалить пользователя <?= htmlspecialchars($order->customer_full_name) ?>?">
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
                        Показано <?= ($current_page - 1) * 20 + 1 ?>-<?= min($current_page * 20, $total_users) ?> из <?= $total_users ?> клиентов
                    </div>
                    <div class="pagination-controls">
                        <?php if ($current_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/users', ['page' => $current_page - 1, 'search' => $search]) ?>" class="pagination-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        
                        if ($start_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/users', ['page' => 1, 'search' => $search]) ?>" class="pagination-btn">1</a>
                            <?php if ($start_page > 2): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="<?= Router::getRoute('/admin/users', ['page' => $i, 'search' => $search]) ?>" class="pagination-btn <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                            <a href="<?= Router::getRoute('/admin/users', ['page' => $total_pages, 'search' => $search]) ?>" class="pagination-btn"><?= $total_pages ?></a>
                        <?php endif; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?= Router::getRoute('/admin/users', ['page' => $current_page + 1, 'search' => $search]) ?>" class="pagination-btn">
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

<?php if (!empty($admin_users)): ?>
<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Администраторы</h2>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Имя пользователя</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admin_users as $admin): ?>
                        <tr data-admin-id="<?= $admin->id ?>">
                            <td><?= $admin->username ?></td>
                            <td><?= $admin->email ?></td>
                            <td>
                                <span class="badge badge-secondary">Администратор</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="<?= Router::getRoute('/admin/users/' . $admin->id . '/edit') ?>" class="btn btn-sm btn-primary" title="Редактировать">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
                                    <button class="btn btn-sm btn-danger" data-admin-delete data-admin-id="<?= $admin->id ?>" title="Удалить" data-confirm="Вы уверены, что хотите удалить администратора <?= $admin->username ?>?">
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
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
$scripts = ['users.js'];
include('views/admin/layout.php');
?>