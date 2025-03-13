<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/admin-settings.php';
require_once __DIR__ . '/core/router.php';


use app\models\Session;

global $SITE_URL;

$session = Session::get();

Router::get('/', 'views/index.php');
Router::get('/catalog', 'views/catalog.php');
Router::get('/landing', 'views/landing.php');


// if ($session->user->is_admin) {
//   Router::get('/admin', 'views/admin.php');
// }

Router::not_found();
