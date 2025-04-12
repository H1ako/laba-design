<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/admin-settings.php';
require_once __DIR__ . '/core/router.php';

use app\controllers\OrderController;
use app\controllers\ProductController;
use app\controllers\admin\AdminAuthController;
use app\controllers\admin\AdminDashboardController;
use app\controllers\admin\AdminOrdersController;
use app\controllers\admin\AdminProductsController;
use app\controllers\admin\AdminUsersController;
use app\models\Session;

global $SITE_URL;

$session = Session::get();

Router::get('/', 'views/landing.php');
Router::get('/catalog', 'views/catalog.php');
Router::get('/catalog/%s', [ProductController::class, 'show']);
// Router::get('/landing', 'views/landing.php');
Router::get('/orders', [OrderController::class, 'index']);
Router::get('/orders/%s', [OrderController::class, 'show_order']);

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


// if ($session->user->is_admin) {
//   Router::get('/admin', 'views/admin.php');
// }

Router::not_found();
