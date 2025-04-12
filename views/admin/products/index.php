<?php
global $SITE_URL;
$title = 'Товары | Админ-панель';
$products = $data['products'] ?? [];
$total_products = $data['total_products'] ?? 0;
$current_page = $data['current_page'] ?? 1;
$total_pages = $data['total_pages'] ?? 1;
$search = $data['search'] ?? '';

$header_actions = [
    [
        'label' => 'Добавить товар',
        'url' => Router::getRoute('/admin/products/create'),
        'class' => 'btn-primary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>'
    ]
];

ob_start();
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Список товаров</h2>
        <div class="header-actions">
            <div class="filters">
                <form action="<?= Router::getRoute('/admin/products') ?>" method="get" class="filters-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Поиск по названию..." value="<?= htmlspecialchars($search) ?>" class="form-control">
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="btn btn-primary">Найти</button>
                        <?php if ($search): ?>
                            <a href="<?= Router::getRoute('/admin/products') ?>" class="btn btn-outline-secondary">Сбросить</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (count($products) === 0): ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <p>Товары не найдены</p>
                <a href="<?= Router::getRoute('/admin/products/create') ?>" class="btn btn-primary">Добавить товар</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-card-image">
                            <img src="<?= $product->thumb_url ?>" alt="<?= htmlspecialchars($product->name) ?>" class="product-image">
                        </div>
                        <div class="product-card-body">
                            <h3 class="product-title"><?= htmlspecialchars($product->name) ?></h3>
                            <div class="product-prices">
                                <?php if ($product->discount > 0): ?>
                                    <span class="product-price"><?= $product->price_formatted ?> ₽</span>
                                    <span class="product-price-old"><?= $product->base_price_formatted ?> ₽</span>
                                    <span class="product-discount">-<?= $product->discount ?>%</span>
                                <?php else: ?>
                                    <span class="product-price"><?= $product->price_formatted ?> ₽</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-sizes">
                                <?php if (count($product->sizes) > 0): ?>
                                    <div class="size-badges">
                                        <?php foreach ($product->sizes as $size): ?>
                                            <span class="size-badge <?= $size->in_stock ? 'in-stock' : 'out-of-stock' ?>"><?= $size->size ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="no-sizes">Нет размеров</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <a href="<?= Router::getRoute('/admin/products/' . $product->id) ?>" class="btn btn-sm btn-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="<?= Router::getRoute('/admin/products/' . $product->id . '/edit') ?>" class="btn btn-sm btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <button class="btn btn-sm btn-danger" data-product-delete data-product-id="<?= $product->id ?>" data-confirm="Вы уверены, что хотите удалить этот товар?">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Показано <?= ($current_page - 1) * 20 + 1 ?>-<?= min($current_page * 20, $total_products) ?> из <?= $total_products ?> товаров
                    </div>
                    <div class="pagination-controls">
                        <?php if ($current_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/products', ['page' => $current_page - 1, 'search' => $search]) ?>" class="pagination-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </a>
                        <?php endif; ?>
                        
                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        
                        if ($start_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/products', ['page' => 1, 'search' => $search]) ?>" class="pagination-btn">1</a>
                            <?php if ($start_page > 2): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="<?= Router::getRoute('/admin/products', ['page' => $i, 'search' => $search]) ?>" class="pagination-btn <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                            <a href="<?= Router::getRoute('/admin/products', ['page' => $total_pages, 'search' => $search]) ?>" class="pagination-btn"><?= $total_pages ?></a>
                        <?php endif; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?= Router::getRoute('/admin/products', ['page' => $current_page + 1, 'search' => $search]) ?>" class="pagination-btn">
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
$scripts = ['products.js'];
include('views/admin/layout.php');
?>