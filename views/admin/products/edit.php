<?php

use app\models\Session;

global $SITE_URL;
$product = $data['product'] ?? null;
$title = 'Редактирование товара | Админ-панель';

if (!$product) {
    header('Location: ' . Router::getRoute('/admin/products'));
    exit;
}

$header_actions = [
    [
        'label' => 'Назад к списку',
        'url' => Router::getRoute('/admin/products'),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="tabs">
    <div class="tabs-header">
        <button class="tab-btn active" data-tab="general">Основная информация</button>
        <button class="tab-btn" data-tab="characteristics">Характеристики</button>
        <button class="tab-btn" data-tab="sizes">Размеры</button>
        <button class="tab-btn" data-tab="images">Изображения</button>
    </div>
    
    <div class="tabs-content">
        <!-- General Tab -->
        <div class="tab-pane active" id="general-tab">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Основная информация</h2>
                </div>
                
                <div class="card-body">
                    <form id="product-form" action="<?= Router::getRoute('/api/admin/products/' . $product->id) ?>" method="post" enctype="multipart/form-data" data-product-id="<?= $product->id ?>">
                        <div class="form-group">
                            <label for="name">Название товара*</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($product->name) ?>" required>
                            <div class="invalid-feedback" data-error-for="name"></div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="base_price">Базовая цена*</label>
                                <div class="input-group">
                                    <input type="number" id="base_price" name="base_price" class="form-control" value="<?= $product->base_price ?>" min="0" step="0.01" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">₽</span>
                                    </div>
                                </div>
                                <div class="invalid-feedback" data-error-for="base_price"></div>
                            </div>
                            
                            <div class="form-group">
                                <label for="discount_price">Цена со скидкой</label>
                                <div class="input-group">
                                    <input type="number" id="discount_price" name="discount_price" class="form-control" value="<?= $product->discount_price ?: '' ?>" min="0" step="0.01">
                                    <div class="input-group-append">
                                        <span class="input-group-text">₽</span>
                                    </div>
                                </div>
                                <div class="invalid-feedback" data-error-for="discount_price"></div>
                                <small class="form-text text-muted">Оставьте пустым, если скидки нет</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Описание</label>
                            <textarea id="description" name="description" class="form-control" rows="5"><?= htmlspecialchars($product->description) ?></textarea>
                            <div class="invalid-feedback" data-error-for="description"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="thumb">Главное изображение</label>
                            <div class="current-thumb">
                                <img src="<?= $product->thumb_url ?>" alt="<?= htmlspecialchars($product->name) ?>" class="current-thumb-image">
                            </div>
                            <div class="custom-file-upload">
                                <input type="file" id="thumb" name="thumb" class="file-input" accept="image/*">
                                <div class="file-preview">
                                    <div class="file-preview-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                        <span>Выберите или перетащите новое изображение</span>
                                    </div>
                                    <img src="#" class="file-preview-image" style="display: none;">
                                </div>
                                <small class="form-text text-muted">Оставьте пустым, чтобы не менять текущее изображение</small>
                            </div>
                            <div class="invalid-feedback" data-error-for="thumb"></div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            <a href="<?= Router::getRoute('/admin/products/' . $product->id) ?>" class="btn btn-outline-secondary">Отменить</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Characteristics Tab -->
        <div class="tab-pane" id="characteristics-tab">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Характеристики товара</h2>
                </div>
                
                <div class="card-body">
                    <div class="characteristics">
                        <ul class="list-group" id="characteristics-list">
                            <?php foreach ($product->characteristics as $characteristic): ?>
                                <li class="list-group-item characteristic-item">
                                    <div class="characteristic-name"><?= htmlspecialchars($characteristic->name) ?></div>
                                    <div class="characteristic-value"><?= htmlspecialchars($characteristic->value) ?></div>
                                    <div class="characteristic-actions">
                                        <button class="btn btn-sm btn-primary" data-characteristic-edit data-characteristic-id="<?= $characteristic->id ?>" data-product-id="<?= $product->id ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-characteristic-delete data-characteristic-id="<?= $characteristic->id ?>" data-product-id="<?= $product->id ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="add-characteristic mt-3">
                            <h3>Добавить характеристику</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="new-characteristic-name">Название</label>
                                    <input type="text" id="new-characteristic-name" class="form-control" placeholder="Материал">
                                </div>
                                <div class="form-group">
                                    <label for="new-characteristic-value">Значение</label>
                                    <input type="text" id="new-characteristic-value" class="form-control" placeholder="Хлопок 100%">
                                </div>
                                <div class="form-group align-self-end">
                                    <button id="add-characteristic-btn" class="btn btn-primary" data-product-id="<?= $product->id ?>">Добавить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sizes Tab -->
        <div class="tab-pane" id="sizes-tab">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Размеры и наличие</h2>
                </div>
                
                <div class="card-body">
                    <div class="sizes">
                        <ul class="list-group" id="sizes-list">
                            <?php foreach ($product->sizes as $size): ?>
                                <li class="list-group-item size-item">
                                    <div class="size-name"><?= htmlspecialchars($size->size) ?></div>
                                    <div class="size-controls">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="size-in-stock-<?= $size->id ?>" 
                                                data-size-in-stock data-size-id="<?= $size->id ?>" data-product-id="<?= $product->id ?>" 
                                                <?= $size->in_stock ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="size-in-stock-<?= $size->id ?>">В наличии</label>
                                        </div>
                                        <button class="btn btn-sm btn-danger ms-2" data-size-delete data-size-id="<?= $size->id ?>" data-product-id="<?= $product->id ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="add-size mt-3">
                            <h3>Добавить размер</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="new-size-name">Размер</label>
                                    <input type="text" id="new-size-name" class="form-control" placeholder="XL">
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="new-size-in-stock" checked>
                                        <label class="form-check-label" for="new-size-in-stock">
                                            В наличии
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group align-self-end">
                                    <button id="add-size-btn" class="btn btn-primary" data-product-id="<?= $product->id ?>">Добавить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Images Tab -->
        <div class="tab-pane" id="images-tab">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Галерея изображений</h2>
                </div>
                
                <div class="card-body">
                    <form id="image-upload-form" action="<?= Router::getRoute('/api/admin/products/' . $product->id . '/images') ?>" method="post" enctype="multipart/form-data" data-product-id="<?= $product->id ?>">
                        <h3>Загрузить новое изображение</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <div class="custom-file-upload">
                                    <input type="file" id="product-image" name="image" class="file-input" accept="image/*" required>
                                    <div class="file-preview" id="image-preview">
                                        <div class="file-preview-placeholder">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                                <polyline points="21 15 16 10 5 21"></polyline>
                                            </svg>
                                            <span>Выберите или перетащите файл</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group align-self-end">
                                <button type="submit" class="btn btn-primary">Загрузить</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="product-images mt-4">
                        <h3>Текущие изображения</h3>
                        <p class="text-muted">Перетаскивайте изображения, чтобы изменить их порядок отображения</p>
                        
                        <div class="product-image-gallery" id="product-images-gallery" data-product-id="<?= $product->id ?>">
                            <?php foreach ($product->images as $image): ?>
                                <div class="product-image-item" data-image-id="<?= $image->id ?>" data-sort-order="<?= $image->sort_order ?>">
                                    <div class="product-image" style="background-image: url('<?= $image->image_url ?>')"></div>
                                    <div class="product-image-actions">
                                        <button type="button" class="btn btn-sm btn-danger" data-image-delete data-image-id="<?= $image->id ?>" data-product-id="<?= $product->id ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['products.js'];
include('views/admin/layout.php');
?>