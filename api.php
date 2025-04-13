<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/admin-settings.php';
require_once __DIR__ . '/core/router.php';

use app\controllers\admin\AdminNewsController;
use app\models\Session;

use app\controllers\ServiceHistoryController;
use app\controllers\UserController;

use app\controllers\admin\AdminServiceController;
use app\controllers\admin\AdminServiceHistoryController;
use app\controllers\admin\AdminOrdersController;
use app\controllers\admin\AdminPagesController;
use app\controllers\admin\AdminProductsController;
use app\controllers\admin\AdminUsersController;
use app\controllers\OrderController;

global $SITE_URL;

$session = Session::get();


Router::set_route_prefix('api');

Router::post('/orders/generate-access', [OrderController::class, 'genererate_access']);

Router::post('/cart', [UserController::class, 'get_cart']);

Router::post('/cart/purchase', [UserController::class, 'purchase_cart']);

// Router::post('/auth/signin', [UserController::class, 'sign_in']);
// Router::post('/auth/signup', [UserController::class, 'sign_up']);
// Router::get('/auth/logout', [UserController::class, 'logout']);

// if (!$session->is_authed) {
//     return Router::not_found();
// }
// Router::post('/user/settings/edit', [UserController::class, 'edit_settings']);

// Router::post('/service-history/create', [ServiceHistoryController::class, 'create']);
// Router::post('/service-history/%s/edit', [ServiceHistoryController::class, 'edit']);


Router::set_route_prefix('api/admin');

// Orders management
Router::post('/orders/%s', [AdminOrdersController::class, 'update']);
Router::post('/orders/%s/status', [AdminOrdersController::class, 'update_status']);
Router::delete('/orders/%s', [AdminOrdersController::class, 'delete']);
Router::delete('/orders/%s/items/%s', [AdminOrdersController::class, 'remove_item']);

// Products management
Router::post('/products', [AdminProductsController::class, 'store']);
Router::post('/products/%s', [AdminProductsController::class, 'update']);
Router::delete('/products/%s', [AdminProductsController::class, 'delete']);

// Product characteristics
Router::post('/products/%s/characteristics', [AdminProductsController::class, 'add_characteristic']);
Router::post('/products/%s/characteristics/%s', [AdminProductsController::class, 'update_characteristic']);
Router::delete('/products/%s/characteristics/%s', [AdminProductsController::class, 'remove_characteristic']);

// Product sizes
Router::post('/products/%s/sizes', [AdminProductsController::class, 'add_size']);
Router::post('/products/%s/sizes/%s', [AdminProductsController::class, 'update_size']);
Router::delete('/products/%s/sizes/%s', [AdminProductsController::class, 'remove_size']);

// Product images
Router::post('/products/%s/images', [AdminProductsController::class, 'add_image']);
Router::post('/products/%s/images/%s/sort', [AdminProductsController::class, 'update_image_sort']);
Router::delete('/products/%s/images/%s', [AdminProductsController::class, 'remove_image']);

// User management
Router::delete('/users/%s', [AdminUsersController::class, 'delete']);
Router::post('/users/admin/%s', [AdminUsersController::class, 'update_admin']);
Router::delete('/users/admin/%s', [AdminUsersController::class, 'delete_admin']);

// News management
Router::post('/news', [AdminNewsController::class, 'store']);
Router::post('/news/%s', [AdminNewsController::class, 'update']);
Router::delete('/news/%s', [AdminNewsController::class, 'destroy']);
Router::post('/upload/image', [AdminNewsController::class, 'upload_image']);

// Pages management
Router::post('/pages', [AdminPagesController::class, 'store']);
Router::post('/pages/order', [AdminPagesController::class, 'updateOrder']);
Router::post('/pages/%s', [AdminPagesController::class, 'update']);
Router::delete('/pages/%s', [AdminPagesController::class, 'delete']);

Router::not_found();
