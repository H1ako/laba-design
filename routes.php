<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/admin-settings.php';
require_once __DIR__ . '/core/router.php';

use app\controllers\OrderController;
use app\controllers\ProductController;
use app\controllers\admin\AdminAuthController;
use app\controllers\admin\AdminDashboardController;
use app\controllers\admin\AdminNewsController;
use app\controllers\admin\AdminOrdersController;
use app\controllers\admin\AdminPagesController;
use app\controllers\admin\AdminProductsController;
use app\controllers\admin\AdminUsersController;
use app\controllers\NewsController;
use app\controllers\PageController;
use app\models\Session;

global $SITE_URL;

$session = Session::get();

Router::get('/', 'views/landing.php');
// Router::get('/landing', 'views/landing.php');

// Catalog routes
Router::get('/catalog', 'views/catalog.php');
Router::get('/catalog/%s', [ProductController::class, 'show']);

// Orders routes
Router::get('/orders', [OrderController::class, 'index']);
Router::get('/orders/%s', [OrderController::class, 'show_order']);

// News routes
Router::get('/news', [NewsController::class, 'index']);
Router::get('/news/%s', [NewsController::class, 'show']);

// Pages routes
Router::get('/page/%s', [PageController::class, 'show']);
Router::get('/page/%s/preview', [PageController::class, 'preview']);

// Admin routes
Router::get('/admin/login', [AdminAuthController::class, 'login_page']);
Router::post('/admin/login', [AdminAuthController::class, 'login']);
Router::get('/admin/logout', [AdminAuthController::class, 'logout']);

// Protected admin routes
Router::get('/admin', [AdminDashboardController::class, 'index']);
Router::get('/admin/orders', [AdminOrdersController::class, 'index']);
Router::get('/admin/orders/%s', [AdminOrdersController::class, 'show']);
Router::get('/admin/orders/%s/edit', [AdminOrdersController::class, 'edit']);

Router::get('/admin/products', [AdminProductsController::class, 'index']);
Router::get('/admin/products/create', [AdminProductsController::class, 'create']);
Router::get('/admin/products/%s', [AdminProductsController::class, 'show']);
Router::get('/admin/products/%s/edit', [AdminProductsController::class, 'edit']);

Router::get('/admin/users', [AdminUsersController::class, 'index']);
Router::get('/admin/users/%s', [AdminUsersController::class, 'show']);
Router::get('/admin/users/%s/edit', [AdminUsersController::class, 'edit_admin']);

Router::get('/admin/news', [AdminNewsController::class, 'index']);
Router::get('/admin/news/create', [AdminNewsController::class, 'create']);
Router::get('/admin/news/%s', [AdminNewsController::class, 'show']);
Router::get('/admin/news/%s/edit', [AdminNewsController::class, 'edit']);

// Admin pages routes
Router::get('/admin/pages', [AdminPagesController::class, 'index']);
Router::get('/admin/pages/create', [AdminPagesController::class, 'create']);
Router::get('/admin/pages/%s', [AdminPagesController::class, 'show']);
Router::get('/admin/pages/%s/edit', [AdminPagesController::class, 'edit']);
Router::get('/admin/pages/%s/preview', [AdminPagesController::class, 'preview']);


// if ($session->user->is_admin) {
//   Router::get('/admin', 'views/admin.php');
// }

Router::not_found();
