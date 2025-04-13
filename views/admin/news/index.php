<?php
global $SITE_URL;
$title = 'Новости | Админ-панель';
$news = $data['news'] ?? [];
$total_news = $data['total_news'] ?? 0;
$current_page = $data['current_page'] ?? 1;
$total_pages = $data['total_pages'] ?? 1;
$search = $data['search'] ?? '';

use app\models\Session;

$header_actions = [
    [
        'label' => 'Добавить новость',
        'url' => Router::getRoute('/admin/news/create'),
        'class' => 'btn-primary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Список новостей</h2>
        <div class="header-actions">
            <div class="filters">
                <form action="<?= Router::getRoute('/admin/news') ?>" method="get" class="filters-form" id="news-search-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Поиск по заголовку..." value="<?= htmlspecialchars($search) ?>" class="form-control">
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="btn btn-primary">Найти</button>
                        <?php if ($search): ?>
                            <a href="<?= Router::getRoute('/admin/news') ?>" class="btn btn-outline-secondary">Сбросить</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php if (count($news) === 0): ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"></path>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <p>Новости не найдены</p>
                <a href="<?= Router::getRoute('/admin/news/create') ?>" class="btn btn-primary">Добавить новость</a>
            </div>
        <?php else: ?>
            <div class="news-grid">
                <?php foreach ($news as $item): ?>
                    <div class="news-card" data-news-id="<?= $item->id ?>">
                        <div class="news-card-image">
                            <img src="<?= $item->thumb_url ?>" alt="<?= htmlspecialchars($item->title) ?>" class="news-image">
                            <?php if (!$item->is_published): ?>
                                <div class="news-status">Черновик</div>
                            <?php endif; ?>
                        </div>
                        <div class="news-card-body">
                            <h3 class="news-title"><?= htmlspecialchars($item->title) ?></h3>
                            <div class="news-date"><?= date('d.m.Y', strtotime($item->created_at)) ?></div>
                            <div class="news-description"><?= mb_substr(htmlspecialchars($item->description), 0, 100) ?>...</div>

                            <div class="news-actions">
                                <a href="<?= Router::getRoute('/admin/news/' . $item->id) ?>" class="btn btn-sm btn-info" title="Просмотр">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="<?= Router::getRoute('/news/' . $item->id) ?>" target="_blank" class="btn btn-sm btn-secondary" title="Просмотр на сайте">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        <polyline points="15 3 21 3 21 9"></polyline>
                                        <line x1="10" y1="14" x2="21" y2="3"></line>
                                    </svg>
                                </a>
                                <a href="<?= Router::getRoute('/admin/news/' . $item->id . '/edit') ?>" class="btn btn-sm btn-primary" title="Редактировать">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <button class="btn btn-sm btn-danger" data-news-delete data-news-id="<?= $item->id ?>" title="Удалить" data-confirm="Вы уверены, что хотите удалить новость «<?= htmlspecialchars($item->title) ?>»?">
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
                        Показано <?= ($current_page - 1) * 10 + 1 ?>-<?= min($current_page * 10, $total_news) ?> из <?= $total_news ?> новостей
                    </div>
                    <div class="pagination-controls">
                        <?php if ($current_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/news', ['page' => $current_page - 1, 'search' => $search]) ?>" class="pagination-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </a>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        if ($start_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/news', ['page' => 1, 'search' => $search]) ?>" class="pagination-btn">1</a>
                            <?php if ($start_page > 2): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="<?= Router::getRoute('/admin/news', ['page' => $i, 'search' => $search]) ?>" class="pagination-btn <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                            <a href="<?= Router::getRoute('/admin/news', ['page' => $total_pages, 'search' => $search]) ?>" class="pagination-btn"><?= $total_pages ?></a>
                        <?php endif; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?= Router::getRoute('/admin/news', ['page' => $current_page + 1, 'search' => $search]) ?>" class="pagination-btn">
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
$scripts = ['news.js'];
include('views/admin/layout.php');
?>