<?php

use app\models\Session;

global $SITE_URL;
$page = $data['page'] ?? null;
$title = $page ? htmlspecialchars($page->title) . ' | Админ-панель' : 'Страница | Админ-панель';

if (!$page) {
    header('Location: ' . Router::getRoute('/admin/pages'));
    exit;
}

$header_actions = [
    [
        'label' => 'Редактировать',
        'url' => Router::getRoute('/admin/pages/' . $page->id . '/edit'),
        'class' => 'btn-primary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>'
    ],
    [
        'label' => 'Предпросмотр',
        'url' => Router::getRoute('/admin/pages/' . $page->id . '/preview'),
        'class' => 'btn-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
    ],
    [
        'label' => 'Посмотреть на сайте',
        'url' => Router::getRoute('/page/' . $page->slug),
        'class' => 'btn-info',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>',
        'target' => '_blank'
    ],
    [
        'label' => 'Вернуться к списку',
        'url' => Router::getRoute('/admin/pages'),
        'class' => 'btn-outline-secondary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="page-view">
    <div class="page-header">
        <div class="page-info">
            <h1 class="page-title"><?= htmlspecialchars($page->title) ?></h1>

            <div class="page-meta">
                <div class="meta-item">
                    <span class="meta-label">URL:</span>
                    <span class="meta-value">
                        <a href="<?= Router::getRoute('/page/' . $page->slug) ?>" target="_blank" class="url-link">
                            /page/<?= htmlspecialchars($page->slug) ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                <polyline points="15 3 21 3 21 9"></polyline>
                                <line x1="10" y1="14" x2="21" y2="3"></line>
                            </svg>
                        </a>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Создана:</span>
                    <span class="meta-value"><?= date('d.m.Y H:i', strtotime($page->created_at)) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Обновлена:</span>
                    <span class="meta-value"><?= date('d.m.Y H:i', strtotime($page->updated_at)) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Статус:</span>
                    <span class="meta-value status-<?= $page->is_published ? 'published' : 'draft' ?>">
                        <?= $page->is_published ? 'Опубликована' : 'Черновик' ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Порядок:</span>
                    <span class="meta-value"><?= $page->sort_order ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Примерное время чтения:</span>
                    <span class="meta-value"><?= $page->reading_time ?> мин</span>
                </div>
            </div>

            <?php if ($page->meta_description): ?>
            <div class="page-description">
                <h3>Meta-описание</h3>
                <p><?= htmlspecialchars($page->meta_description) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h2 class="card-title">Содержание страницы</h2>
        </div>
        <div class="card-body">
            <div class="page-content">
                <?= $page->content ?>
            </div>
        </div>
    </div>

    <!-- Delete Page Button -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="danger-zone">
                <h3>Удаление страницы</h3>
                <p>Это действие нельзя отменить. Страница будет удалена безвозвратно.</p>
                <button class="btn btn-danger" data-page-delete data-page-id="<?= $page->id ?>" data-confirm="Вы уверены, что хотите удалить страницу «<?= htmlspecialchars($page->title) ?>»?">Удалить страницу</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['pages.js'];
include('views/admin/layout.php');
?>