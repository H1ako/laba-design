<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Админ-панель' ?></title>
    <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/admin.css">
    <script defer src="<?= $SITE_URL ?>/assets/scripts/admin/main.js"></script>
    <?php if (isset($scripts) && is_array($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script defer src="<?= $SITE_URL ?>/assets/scripts/admin/<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h1 class="sidebar-logo">ZovIsland</h1>
                <button class="sidebar-toggle" id="sidebar-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin') === 0 && strpos($_SERVER['REQUEST_URI'], '/admin/') === false ? 'active' : '' ?>">
                        <a href="<?= Router::getRoute('/admin') ?>" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>Главная</span>
                        </a>
                    </li>
                    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/orders') === 0 ? 'active' : '' ?>">
                        <a href="<?= Router::getRoute('/admin/orders') ?>" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span>Заказы</span>
                        </a>
                    </li>
                    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/products') === 0 ? 'active' : '' ?>">
                        <a href="<?= Router::getRoute('/admin/products') ?>" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                            <span>Товары</span>
                        </a>
                    </li>
                    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/users') === 0 ? 'active' : '' ?>">
                        <a href="<?= Router::getRoute('/admin/users') ?>" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>Пользователи</span>
                        </a>
                    </li>
                    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/news') === 0 ? 'active' : '' ?>">
                        <a href="<?= Router::getRoute('/admin/news') ?>" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            <span>Новости</span>
                        </a>
                    </li>
                    <li class="nav-item <?= strpos($_SERVER['REQUEST_URI'], '/admin/pages') === 0 ? 'active' : '' ?>">
                        <a href="<?= Router::getRoute('/admin/pages') ?>" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <line x1="10" y1="9" x2="8" y2="9"></line>
                            </svg>
                            <span>Страницы</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user->full_name ?? $user->username, 0, 1)) ?>
                    </div>
                    <div class="user-details">
                        <p class="user-name"><?= htmlspecialchars($user->full_name ?? $user->username) ?></p>
                        <p class="user-role"><?= $user->role === 'super_admin' ? 'Администратор' : 'Менеджер' ?></p>
                    </div>
                </div>
                <a href="<?= Router::getRoute('/admin/logout') ?>" class="logout-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Выход</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="main-header">
                <h2 class="page-title"><?= $title ?? 'Админ-панель' ?></h2>
                <div class="header-actions">
                    <?php if (isset($header_actions) && is_array($header_actions)): ?>
                        <?php foreach ($header_actions as $action): ?>
                            <a href="<?= $action['url'] ?>" class="btn <?= $action['class'] ?? 'btn-primary' ?>">
                                <?php if (isset($action['icon'])): ?>
                                    <span class="btn-icon"><?= $action['icon'] ?></span>
                                <?php endif; ?>
                                <?= $action['label'] ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </header>

            <div class="main-content">
                <?= $content ?>
            </div>
        </main>
    </div>
</body>

</html>