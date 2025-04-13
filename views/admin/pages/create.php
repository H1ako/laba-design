<?php

use app\models\Session;

global $SITE_URL;
$title = 'Создание страницы | Админ-панель';

$header_actions = [
    [
        'label' => 'Назад к списку',
        'url' => Router::getRoute('/admin/pages'),
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
        <h2 class="card-title">Создание новой страницы</h2>
    </div>

    <div class="card-body">
        <form id="page-form" action="<?= Router::getRoute('/api/admin/pages') ?>" method="post">
            <div class="form-row">
                <div class="form-group flex-grow-1">
                    <label for="title">Заголовок*</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                    <div class="invalid-feedback" data-error-for="title"></div>
                </div>

                <div class="form-group" style="width: 150px;">
                    <label for="sort_order">Порядок сортировки</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="10" min="1" step="1">
                    <div class="invalid-feedback" data-error-for="sort_order"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="slug">URL (slug)*</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">/page/</span>
                    </div>
                    <input type="text" id="slug" name="slug" class="form-control" placeholder="example-page">
                </div>
                <div class="invalid-feedback" data-error-for="slug"></div>
                <small class="form-text text-muted">Если оставить пустым, slug будет сгенерирован автоматически из заголовка</small>
            </div>

            <div class="form-group">
                <label for="meta_description">Meta-описание</label>
                <textarea id="meta_description" name="meta_description" class="form-control" rows="2"></textarea>
                <div class="invalid-feedback" data-error-for="meta_description"></div>
                <small class="form-text text-muted">Краткое описание для поисковых систем (до 160 символов)</small>
            </div>

            <div class="form-group">
                <label for="content">Содержание*</label>
                <div id="editor-container">
                    <div id="quill-editor" style="height: 350px;"></div>
                </div>
                <textarea id="content" name="content" class="form-control" rows="15" style="display: none;"></textarea>
                <div class="invalid-feedback" data-error-for="content"></div>
            </div>

            <div class="form-group">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1">
                    <label class="form-check-label" for="is_published">Опубликовать сразу</label>
                </div>
                <small class="form-text text-muted">Если не выбрано, страница будет сохранена как черновик</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Создать страницу</button>
                <a href="<?= Router::getRoute('/admin/pages') ?>" class="btn btn-outline-secondary">Отменить</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['pages.js'];
include('views/admin/layout.php');
?>