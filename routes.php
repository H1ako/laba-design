<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/admin-settings.php';
require_once __DIR__ . '/core/router.php';

use app\controllers\OrderController;
use app\models\Session;

global $SITE_URL;

$session = Session::get();

Router::get('/', 'views/landing.php');
Router::get('/catalog', 'views/catalog.php');
// Router::get('/landing', 'views/landing.php');
Router::get('/orders', [OrderController::class, 'index']);
Router::get('/orders/%s', [OrderController::class, 'show_order']);


// if ($session->user->is_admin) {
//   Router::get('/admin', 'views/admin.php');
// }

Router::not_found();
