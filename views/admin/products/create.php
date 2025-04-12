<?php

use app\models\Session;

global $SITE_URL;
$title = 'Создание товара | Админ-панель';

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

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Создание нового товара</h2>
    </div>
    
    <div class="card-body">
        <form id="product-form" action="<?= Router::getRoute('/api/admin/products') ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Название товара*</label>
                <input type="text" id="name" name="name" class="form-control" required>
                <div class="invalid-feedback" data-error-for="name"></div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="base_price">Базовая цена*</label>
                    <div class="input-group">
                        <input type="number" id="base_price" name="base_price" class="form-control" min="0" step="0.01" required>
                        <div class="input-group-append">
                            <span class="input-group-text">₽</span>
                        </div>
                    </div>
                    <div class="invalid-feedback" data-error-for="base_price"></div>
                </div>
                
                <div class="form-group">
                    <label for="discount_price">Цена со скидкой</label>
                    <div class="input-group">
                        <input type="number" id="discount_price" name="discount_price" class="form-control" min="0" step="0.01">
                        <div class="input-group-append">
                            <span class="input-group-text">₽</span>
                        </div>
                    </div>
                    <div class="invalid-feedback" data-error-for="discount_price"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description" class="form-control" rows="5"></textarea>
                <div class="invalid-feedback" data-error-for="description"></div>
            </div>
            
            <div class="form-group">
                <label for="thumb">Главное изображение*</label>
                <div class="custom-file-upload">
                    <input type="file" id="thumb" name="thumb" class="file-input" accept="image/*" required>
                    <div class="file-preview">
                        <div class="file-preview-placeholder">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span>Выберите или перетащите файл</span>
                        </div>
                        <img src="#" class="file-preview-image" style="display: none;">
                    </div>
                </div>
                <div class="invalid-feedback" data-error-for="thumb"></div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Создать товар</button>
                <a href="<?= Router::getRoute('/admin/products') ?>" class="btn btn-outline-secondary">Отменить</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['products.js'];
include('views/admin/layout.php');
?>