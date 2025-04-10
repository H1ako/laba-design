<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/admin-settings.php';
require_once __DIR__ . '/core/router.php';

use app\models\Session;

use app\controllers\ServiceHistoryController;
use app\controllers\UserController;

use app\controllers\admin\AdminServiceController;
use app\controllers\admin\AdminServiceHistoryController;
use app\controllers\admin\AdminUserController;

global $SITE_URL;

$session = Session::get();


Router::set_route_prefix('api');

Router::post('/cart', [UserController::class, 'get_cart']);

Router::post('/cart/purchase', [UserController::class, 'purchase_cart']);

// Router::post('/auth/signin', [UserController::class, 'sign_in']);
// Router::post('/auth/signup', [UserController::class, 'sign_up']);
// Router::get('/auth/logout', [UserController::class, 'logout']);

// if (!$session->is_authed) {
//     return Router::not_found();
// }
// Router::put('/user/settings/edit', [UserController::class, 'edit_settings']);

// Router::post('/service-history/create', [ServiceHistoryController::class, 'create']);
// Router::put('/service-history/%s/edit', [ServiceHistoryController::class, 'edit']);

if (!$session->user->is_admin) {
    return Router::not_found();
}

Router::set_route_prefix('api/admin');

Router::post('/users/create', [AdminUserController::class, 'create']);
Router::put('/users/%s/edit', [AdminUserController::class, 'edit']);
Router::delete('/users/%s/delete', [AdminUserController::class, 'delete']);

// Router::post('/services/create', [AdminServiceController::class, 'create']);
// Router::post('/services/%s/edit', [AdminServiceController::class, 'edit']);
// Router::delete('/services/%s/delete', [AdminServiceController::class, 'delete']);

// Router::post('/service_history/create', [AdminServiceHistoryController::class, 'create']);
// Router::put('/service_history/%s/edit', [AdminServiceHistoryController::class, 'edit']);
// Router::delete('/service_history/%s/delete', [AdminServiceHistoryController::class, 'delete']);

Router::not_found();
