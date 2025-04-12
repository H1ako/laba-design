<?php
ob_start();
?>

<div class="dashboard">
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </div>
            <div class="stat-card-content">
                <h3 class="stat-card-title">Заказы</h3>
                <p class="stat-card-value"><?= $orders_count ?></p>
                <a href="<?= Router::getRoute('/admin/orders') ?>" class="stat-card-link">Просмотреть все →</a>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
            </div>
            <div class="stat-card-content">
                <h3 class="stat-card-title">Товары</h3>
                <p class="stat-card-value"><?= $products_count ?></p>
                <a href="<?= Router::getRoute('/admin/products') ?>" class="stat-card-link">Просмотреть все →</a>
            </div>
        </div>
    </div>
    
    <div class="dashboard-row">
        <!-- Recent Orders -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Последние заказы</h2>
                <a href="<?= Router::getRoute('/admin/orders') ?>" class="btn btn-sm btn-outline">Все заказы</a>
            </div>
            <div class="card-content">
                <?php if (count($recent_orders) > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Покупатель</th>
                                <th>Дата</th>
                                <th>Статус</th>
                                <th>Сумма</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><a href="<?= Router::getRoute("/admin/orders/{$order->id}") ?>">#<?= $order->id ?></a></td>
                                    <td><?= htmlspecialchars($order->customer_full_name) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($order->created_at)) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $order->status ?>">
                                            <?php
                                            switch($order->status) {
                                                case 'initial': echo 'Создан'; break;
                                                case 'working': echo 'В работе'; break;
                                                case 'success': echo 'Выполнен'; break;
                                                case 'canceled': echo 'Отменен'; break;
                                                default: echo $order->status;
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td><?= $order->total_price_formatted ?> ₽</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Нет заказов в системе</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Low stock products -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Товары без остатков</h2>
                <a href="<?= Router::getRoute('/admin/products') ?>" class="btn btn-sm btn-outline">Все товары</a>
            </div>
            <div class="card-content">
                <?php if (count($low_stock_products) > 0): ?>
                    <div class="product-list">
                        <?php foreach ($low_stock_products as $product): ?>
                            <div class="product-item">
                                <div class="product-image">
                                    <img src="<?= $product->thumb_url ?>" alt="<?= htmlspecialchars($product->name) ?>">
                                </div>
                                <div class="product-details">
                                    <h3><a href="<?= Router::getRoute("/admin/products/{$product->id}") ?>"><?= htmlspecialchars($product->name) ?></a></h3>
                                    <p class="product-price"><?= $product->price_formatted ?> ₽</p>
                                </div>
                                <a href="<?= Router::getRoute("/admin/products/{$product->id}/edit") ?>" class="btn btn-sm">Редактировать</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Все товары в наличии</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include('views/admin/layout.php');
?>