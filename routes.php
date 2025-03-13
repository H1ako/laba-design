<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/admin-settings.php';
require_once __DIR__ . '/core/router.php';


use app\models\Session;

global $SITE_URL;

$session = Session::get();


if ($session->is_authed) {
  Router::get('/', 'views/index.php');
  Router::get('/login', function () {
    Router::redirect_to('/');
  });
} else {
  Router::get('/login', 'views/login.php');
  Router::redirect_to('/login');
};

if ($session->user->is_admin) {
  Router::get('/admin', 'views/admin.php');
}

Router::not_found();
