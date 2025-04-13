<?php
global $SITE_URL;
$title = 'Страницы | Админ-панель';
$pages = $data['pages'] ?? [];
$total_pages = $data['total_pages'] ?? 0;
$current_page = $data['current_page'] ?? 1;
$total_pagination_pages = $data['total_pagination_pages'] ?? 1;
$search = $data['search'] ?? '';

use app\models\Session;

$header_actions = [
    [
        'label' => 'Добавить страницу',
        'url' => Router::getRoute('/admin/pages/create'),
        'class' => 'btn-primary',
        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>'
    ]
];

ob_start();
?>

<meta name="csrf-token" content="<?= Session::get()->get_csrf_token() ?>">

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Список страниц</h2>
        <div class="header-actions">
            <div class="filters">
                <form action="<?= Router::getRoute('/admin/pages') ?>" method="get" class="filters-form" id="page-search-form">
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Поиск по заголовку..." value="<?= htmlspecialchars($search) ?>" class="form-control">
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="btn btn-primary">Найти</button>
                        <?php if ($search): ?>
                            <a href="<?= Router::getRoute('/admin/pages') ?>" class="btn btn-outline-secondary">Сбросить</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php if (count($pages) === 0): ?>
            <div class="empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <p>Страницы не найдены</p>
                <a href="<?= Router::getRoute('/admin/pages/create') ?>" class="btn btn-primary">Добавить страницу</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px">Порядок</th>
                            <th>Заголовок</th>
                            <th>URL (slug)</th>
                            <th>Статус</th>
                            <th>Дата создания</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody class="page-list-sortable">
                        <?php foreach ($pages as $page): ?>
                            <tr data-page-id="<?= $page->id ?>">
                                <td>
                                    <div class="drag-handle">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="8" y1="6" x2="21" y2="6"></line>
                                            <line x1="8" y1="12" x2="21" y2="12"></line>
                                            <line x1="8" y1="18" x2="21" y2="18"></line>
                                            <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                            <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                            <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                        </svg>
                                    </div>
                                </td>
                                <td><a href="<?= Router::getRoute('/admin/pages/' . $page->id) ?>" class="link"><?= htmlspecialchars($page->title) ?></a></td>
                                <td><code><?= htmlspecialchars($page->slug) ?></code></td>
                                <td>
                                    <span class="badge <?= $page->is_published ? 'badge-success' : 'badge-secondary' ?>">
                                        <?= $page->is_published ? 'Опубликовано' : 'Черновик' ?>
                                    </span>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($page->created_at)) ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="<?= Router::getRoute('/admin/pages/' . $page->id) ?>" class="btn btn-sm btn-info" title="Просмотр">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                        <a href="<?= Router::getRoute('/admin/pages/' . $page->id . '/preview') ?>" class="btn btn-sm btn-warning" title="Предпросмотр с адаптивным режимом">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                                <line x1="12" y1="17" x2="12" y2="21"></line>
                                            </svg>
                                        </a>
                                        <a href="<?= Router::getRoute('/page/' . $page->slug) ?>" target="_blank" class="btn btn-sm btn-secondary" title="Просмотр на сайте">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                <polyline points="15 3 21 3 21 9"></polyline>
                                                <line x1="10" y1="14" x2="21" y2="3"></line>
                                            </svg>
                                        </a>
                                        <a href="<?= Router::getRoute('/admin/pages/' . $page->id . '/edit') ?>" class="btn btn-sm btn-primary" title="Редактировать">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>
                                        <button class="btn btn-sm btn-danger" data-page-delete data-page-id="<?= $page->id ?>" title="Удалить" data-confirm="Вы уверены, что хотите удалить страницу «<?= htmlspecialchars($page->title) ?>»?">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pagination_pages > 1): ?>
                <div class="pagination">
                    <div class="pagination-info">
                        Показано <?= ($current_page - 1) * 10 + 1 ?>-<?= min($current_page * 10, $total_pages) ?> из <?= $total_pages ?> страниц
                    </div>
                    <div class="pagination-controls">
                        <?php if ($current_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/pages', ['page' => $current_page - 1, 'search' => $search]) ?>" class="pagination-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </a>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pagination_pages, $current_page + 2);

                        if ($start_page > 1): ?>
                            <a href="<?= Router::getRoute('/admin/pages', ['page' => 1, 'search' => $search]) ?>" class="pagination-btn">1</a>
                            <?php if ($start_page > 2): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <a href="<?= Router::getRoute('/admin/pages', ['page' => $i, 'search' => $search]) ?>" class="pagination-btn <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pagination_pages): ?>
                            <?php if ($end_page < $total_pagination_pages - 1): ?>
                                <span class="pagination-ellipsis">...</span>
                            <?php endif; ?>
                            <a href="<?= Router::getRoute('/admin/pages', ['page' => $total_pagination_pages, 'search' => $search]) ?>" class="pagination-btn"><?= $total_pagination_pages ?></a>
                        <?php endif; ?>

                        <?php if ($current_page < $total_pagination_pages): ?>
                            <a href="<?= Router::getRoute('/admin/pages', ['page' => $current_page + 1, 'search' => $search]) ?>" class="pagination-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="sort-info alert alert-info mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>Вы можете перетаскивать страницы для изменения их порядка отображения в меню</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = ['pages.js'];
include('views/admin/layout.php');
?>