<?php

use app\models\Session;

global $SITE_URL;
$news = $data['news'] ?? null;
$title = $news ? htmlspecialchars($news->title) . ' | Админ-панель' : 'Новость | Админ-панель';

if (!$news) {
    header('Location: ' . Router::getRoute('/admin/news'));
    exit;
}

$header_actions = [
    [
        'label' => 'Редактировать',
        'url' => Router::getRoute('/admin/news/' . $news->id . '/edit'),
        'class' => 'btn-primary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>'
    ],
    [
        'label' => 'Посмотреть на сайте',
        'url' => Router::getRoute('/news/' . $news->id),
        'class' => 'btn-info',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>',
        'target' => '_blank'
    ],
    [
        'label' => 'Вернуться к списку',
        'url' => Router::getRoute('/admin/news'),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="news-view">
    <div class="news-header">
        <div class="news-image">
            <img src="<?= $news->thumb_url ?>" alt="<?= htmlspecialchars($news->title) ?>" class="news-main-image">
            <?php if (!$news->is_published): ?>
                <div class="draft-badge">Черновик</div>
            <?php endif; ?>
        </div>

        <div class="news-info">
            <h1 class="news-title"><?= htmlspecialchars($news->title) ?></h1>

            <div class="news-meta">
                <div class="meta-item">
                    <span class="meta-label">Опубликовано:</span>
                    <span class="meta-value"><?= date('d.m.Y H:i', strtotime($news->created_at)) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Обновлено:</span>
                    <span class="meta-value"><?= date('d.m.Y H:i', strtotime($news->updated_at)) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Статус:</span>
                    <span class="meta-value status-<?= $news->is_published ? 'published' : 'draft' ?>">
                        <?= $news->is_published ? 'Опубликовано' : 'Черновик' ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Примерное время чтения:</span>
                    <span class="meta-value"><?= $news->reading_time ?> мин</span>
                </div>
            </div>

            <div class="news-description">
                <h2>Краткое описание</h2>
                <p><?= htmlspecialchars($news->description) ?></p>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h2 class="card-title">Полное содержание</h2>
        </div>
        <div class="card-body">
            <div class="news-content">
                <?= $news->content ?>
            </div>
        </div>
    </div>

    <!-- Delete News Button -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="danger-zone">
                <h3>Удаление новости</h3>
                <p>Это действие нельзя отменить. Будут удалены все данные, связанные с этой новостью.</p>
                <button class="btn btn-danger" data-news-delete data-news-id="<?= $news->id ?>" data-confirm="Вы уверены, что хотите удалить новость «<?= htmlspecialchars($news->title) ?>»?">Удалить новость</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['news.js'];
include('views/admin/layout.php');
?>