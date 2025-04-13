<?php

use app\models\Session;

global $SITE_URL;
$title = 'Создание новости | Админ-панель';

$header_actions = [
    [
        'label' => 'Назад к списку',
        'url' => Router::getRoute('/admin/news'),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Создание новой новости</h2>
    </div>

    <div class="card-body">
        <form id="news-form" action="<?= Router::getRoute('/api/admin/news') ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Заголовок*</label>
                <input type="text" id="title" name="title" class="form-control" required>
                <div class="invalid-feedback" data-error-for="title"></div>
            </div>

            <div class="form-group">
                <label for="description">Краткое описание*</label>
                <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                <div class="invalid-feedback" data-error-for="description"></div>
                <small class="form-text text-muted">Краткое описание будет отображаться в списке новостей и в поисковых результатах</small>
            </div>

            <div class="form-group">
                <label for="content">Содержание*</label>
                <textarea id="content" name="content" class="form-control" rows="15" style="display: none;"></textarea>
                <div class="invalid-feedback" data-error-for="content"></div>
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
                <small class="form-text text-muted">Рекомендуемый размер изображения: 1200x630 пикселей</small>
            </div>

            <div class="form-group">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" checked>
                    <label class="form-check-label" for="is_published">Опубликовать сразу</label>
                </div>
                <small class="form-text text-muted">Если не выбрано, новость будет сохранена как черновик</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Создать новость</button>
                <a href="<?= Router::getRoute('/admin/news') ?>" class="btn btn-outline-secondary">Отменить</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['news.js'];
include('views/admin/layout.php');
?>