<?php

use app\models\Session;

global $SITE_URL;
$product = $data['product'] ?? null;
$title = $product ? htmlspecialchars($product->name) . ' | Админ-панель' : 'Товар | Админ-панель';

if (!$product) {
    header('Location: ' . Router::getRoute('/admin/products'));
    exit;
}

$header_actions = [
    [
        'label' => 'Редактировать',
        'url' => Router::getRoute('/admin/products/' . $product->id . '/edit'),
        'class' => 'btn-primary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>'
    ],
    [
        'label' => 'Вернуться к списку',
        'url' => Router::getRoute('/admin/products'),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="product-view">
    <div class="product-header">
        <div class="product-thumb">
            <img src="<?= $product->thumb_url ?>" alt="<?= htmlspecialchars($product->name) ?>" class="product-main-image">
        </div>
        
        <div class="product-summary">
            <h1 class="product-name"><?= htmlspecialchars($product->name) ?></h1>
            
            <div class="product-pricing">
                <?php if ($product->discount > 0): ?>
                    <div class="product-price"><?= $product->price_formatted ?> ₽</div>
                    <div class="product-price-original"><?= $product->base_price_formatted ?> ₽</div>
                    <div class="product-discount-tag">-<?= $product->discount ?>%</div>
                <?php else: ?>
                    <div class="product-price"><?= $product->price_formatted ?> ₽</div>
                <?php endif; ?>
            </div>
            
            <div class="product-sizes">
                <h3>Размеры</h3>
                <?php if (count($product->sizes) > 0): ?>
                    <div class="size-list">
                        <?php foreach ($product->sizes as $size): ?>
                            <span class="size-tag <?= $size->in_stock ? 'in-stock' : 'out-of-stock' ?>">
                                <?= htmlspecialchars($size->size) ?>
                                <?php if (!$size->in_stock): ?>
                                    <span class="out-of-stock-indicator"></span>
                                <?php endif; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-sizes">Размеры не указаны</p>
                <?php endif; ?>
            </div>
            
            <div class="product-meta">
                <div class="meta-item">
                    <span class="meta-label">ID:</span>
                    <span class="meta-value"><?= $product->id ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Создан:</span>
                    <span class="meta-value"><?= date('d.m.Y H:i', strtotime($product->created_at)) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Обновлен:</span>
                    <span class="meta-value"><?= date('d.m.Y H:i', strtotime($product->updated_at)) ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="product-sections">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Описание</h2>
            </div>
            <div class="card-body">
                <div class="product-description">
                    <?= nl2br(htmlspecialchars($product->description)) ?>
                </div>
            </div>
        </div>
        
        <?php if (count($product->characteristics) > 0): ?>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Характеристики</h2>
                </div>
                <div class="card-body">
                    <table class="table table-characteristics">
                        <tbody>
                            <?php foreach ($product->characteristics as $characteristic): ?>
                                <tr>
                                    <th><?= htmlspecialchars($characteristic->name) ?></th>
                                    <td><?= htmlspecialchars($characteristic->value) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (count($product->images) > 0): ?>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Галерея</h2>
                </div>
                <div class="card-body">
                    <div class="product-gallery">
                        <?php foreach ($product->images as $image): ?>
                            <div class="gallery-item">
                                <a href="<?= $image->image_url ?>" target="_blank" class="gallery-link">
                                    <img src="<?= $image->image_url ?>" alt="Product image" class="gallery-image">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include('views/admin/layout.php');
?>