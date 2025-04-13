<?php

use app\models\Session;

global $SITE_URL;
$page = $data['page'] ?? null;
$title = 'Редактирование страницы | Админ-панель';

if (!$page) {
    header('Location: ' . Router::getRoute('/admin/pages'));
    exit;
}

$header_actions = [
    [
        'label' => 'Просмотреть',
        'url' => Router::getRoute('/admin/pages/' . $page->id),
        'class' => 'btn-info',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
    ],
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
        <h2 class="card-title">Редактирование страницы</h2>
    </div>

    <div class="card-body">
        <form id="page-form" action="<?= Router::getRoute('/api/admin/pages/' . $page->id) ?>" method="post" data-page-id="<?= $page->id ?>">
            <div class="form-row">
                <div class="form-group flex-grow-1">
                    <label for="title">Заголовок*</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($page->title) ?>" required>
                    <div class="invalid-feedback" data-error-for="title"></div>
                </div>

                <div class="form-group" style="width: 150px;">
                    <label for="sort_order">Порядок сортировки</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= $page->sort_order ?>" min="1" step="1">
                    <div class="invalid-feedback" data-error-for="sort_order"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="slug">URL (slug)*</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">/page/</span>
                    </div>
                    <input type="text" id="slug" name="slug" class="form-control" value="<?= htmlspecialchars($page->slug) ?>">
                </div>
                <div class="invalid-feedback" data-error-for="slug"></div>
                <small class="form-text text-muted">Будьте осторожны при изменении URL существующей страницы</small>
            </div>

            <div class="form-group">
                <label for="meta_description">Meta-описание</label>
                <textarea id="meta_description" name="meta_description" class="form-control" rows="2"><?= htmlspecialchars($page->meta_description) ?></textarea>
                <div class="invalid-feedback" data-error-for="meta_description"></div>
                <small class="form-text text-muted">Краткое описание для поисковых систем (до 160 символов)</small>
            </div>

            <div class="form-group">
                <label for="content">Содержание*</label>
                <div id="editor-container">
                    <div id="quill-editor" style="height: 350px;"></div>
                </div>
                <textarea id="content" name="content" class="form-control" rows="15" style="display: none;"><?= htmlspecialchars($page->content) ?></textarea>
                <div class="invalid-feedback" data-error-for="content"></div>
            </div>

            <div class="form-group">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" <?= $page->is_published ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_published">Опубликовать</label>
                </div>
                <small class="form-text text-muted">Если не выбрано, страница будет сохранена как черновик</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                <a href="<?= Router::getRoute('/admin/pages/' . $page->id) ?>" class="btn btn-outline-secondary">Отменить</a>
                <a href="<?= Router::getRoute('/admin/pages/' . $page->id . '/preview') ?>" target="_blank" class="btn btn-info ms-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Предпросмотр
                </a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['pages.js'];
include('views/admin/layout.php');
?>