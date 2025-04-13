<?php

use app\models\Session;

global $SITE_URL;
$news = $data['news'] ?? null;
$title = 'Редактирование новости | Админ-панель';

if (!$news) {
    header('Location: ' . Router::getRoute('/admin/news'));
    exit;
}

$header_actions = [
    [
        'label' => 'Просмотреть',
        'url' => Router::getRoute('/admin/news/' . $news->id),
        'class' => 'btn-info',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
    ],
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Редактирование новости</h2>
    </div>

    <div class="card-body">
        <form id="news-form" action="<?= Router::getRoute('/api/admin/news/' . $news->id) ?>" method="post" enctype="multipart/form-data" data-news-id="<?= $news->id ?>">
            <div class="form-group">
                <label for="title">Заголовок*</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($news->title) ?>" required>
                <div class="invalid-feedback" data-error-for="title"></div>
            </div>

            <div class="form-group">
                <label for="description">Краткое описание*</label>
                <textarea id="description" name="description" class="form-control" rows="3" required><?= htmlspecialchars($news->description) ?></textarea>
                <div class="invalid-feedback" data-error-for="description"></div>
                <small class="form-text text-muted">Краткое описание будет отображаться в списке новостей и в поисковых результатах</small>
            </div>

            <div class="form-group">
                <label for="content">Содержание*</label>
                <textarea id="content" name="content" class="form-control" rows="15" required><?= htmlspecialchars($news->content) ?></textarea>
                <div class="invalid-feedback" data-error-for="content"></div>
            </div>

            <div class="form-group">
                <label for="thumb">Главное изображение</label>
                <div class="current-thumb">
                    <img src="<?= $news->thumb_url ?>" alt="<?= htmlspecialchars($news->title) ?>" class="current-thumb-image">
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
                </div>
                <div class="invalid-feedback" data-error-for="thumb"></div>
                <small class="form-text text-muted">Оставьте пустым, чтобы не менять текущее изображение. Рекомендуемый размер изображения: 1200x630 пикселей</small>
            </div>

            <div class="form-group">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" <?= $news->is_published ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_published">Опубликовать</label>
                </div>
                <small class="form-text text-muted">Если не выбрано, новость будет сохранена как черновик</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                <a href="<?= Router::getRoute('/admin/news/' . $news->id) ?>" class="btn btn-outline-secondary">Отменить</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['news.js'];
include('views/admin/layout.php');
?>