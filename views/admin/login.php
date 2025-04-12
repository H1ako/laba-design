<?php

use app\models\Session;

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>ZoVisland</h1>
                <p>Вход в админ-панель</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form class="login-form" method="post" action="<?= Router::getRoute('/admin/login') ?>">
                <?php Session::get()->set_csrf(); ?>
                <div class="form-group">
                    <label for="username">Имя пользователя</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?= $username ?? '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">Войти</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>