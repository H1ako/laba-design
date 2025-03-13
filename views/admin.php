<?php

use app\models\Service;
use app\models\ServiceHistory;
use app\models\User;

global $SITE_URL, $session;

$users = User::get_all();
$services = Service::get_all();
$orders = ServiceHistory::get_all();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once('components/base-head.php'); ?>
    <title>Главная</title>
    <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/admin.css">
    <script>
        const SITE_URL = '<?= $SITE_URL ?>';
    </script>
    <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/phoneFieldFormatter.js"></script>
    <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/notiflix-notify-aio-3.2.7.min.js"></script>
    <script defer src="<?= $SITE_URL ?>/assets/scripts/admin/index.js"></script>
</head>

<body>
    <nav>
        <h1>Админ панель</h1>
        <ul>
            <li>
                <label data-tab="users">
                    <input type="radio" name="nav" checked>
                    Пользователи
                </label>
            </li>
            <li>
                <label data-tab="services">
                    <input type="radio" name="nav">Услуги
                </label>
            </li>
            <li>
                <label data-tab="orders">
                    <input type="radio" name="nav">
                    Заказы
                </label>
            </li>
            <li>
                <a href="<?= $SITE_URL ?>/">На сайт</a>
            </li>
        </ul>
    </nav>
    <main data-state="users">
        <section class="users">
            <h1>Пользователи</h1>
            <form action="">
                <h2>Создать пользователя</h2>
                <?= $session->set_csrf(); ?>
                <input placeholder="Почта" type="email" name="email" required>
                <input placeholder="Пароль" type="text" name="password" required>
                <input placeholder="ФИО" type="text" name="full_name" required>
                <input placeholder="Номер телефона" type="tel" name="phone_number">
                <input placeholder="Адрес" type="text" name="address">
                <button type="submit" edit-action>Создать</button>
            </form>
            <ul class="list">
                <?php foreach ($users as $user) : ?>
                    <li data-id="<?= $user->get_id() ?>">
                        <form action="">
                            <h3>#<?= $user->get_id() ?></h3>
                            <?= $session->set_csrf(); ?>
                            <input type="hidden" name="user_id" value="<?= $user->get_id() ?>">
                            <input placeholder="Почта" type="email" name="email" value="<?= $user->email ?>" required>
                            <input placeholder="Пароль" type="text" name="password" value="">
                            <input placeholder="ФИО" type="text" name="full_name" value="<?= $user->full_name ?>" required>
                            <input placeholder="Номер телефона" type="tel" name="phone_number" value="<?= $user->phone_number ?>">
                            <input placeholder="Адрес" type="text" name="address" value="<?= $user->address ?>">
                            <button type="submit" edit-action>Изменить</button>
                        </form>
                        <button delete-action>Удалить</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <section class="services">
            <h1>Услуги</h1>
            <form action="" type="multipart/form-data">
                <h2>Создать услугу</h2>
                <?= $session->set_csrf(); ?>
                <input type="hidden" name="service_id">
                <input placeholder="Превью" type="file" name="preview_image">
                <input placeholder="Название" type="text" name="name" required>
                <input placeholder="Цена" type="number" name="base_price" required>
                <input placeholder="Время выполнения в секундах" type="number" name="base_completion_time" required>
                <input placeholder="Количество работников" type="number" name="base_workers_amount" required>
                <button type="submit" edit-action>Создать</button>
            </form>
            <ul class="list">
                <?php foreach ($services as $service) : ?>
                    <li data-id="<?= $service->get_id() ?>">
                        <form action="" type="multipart/form-data">
                            <h3>#<?= $service->get_id() ?></h3>
                            <?= $session->set_csrf(); ?>
                            <input type="hidden" name="service_id" value="<?= $service->get_id() ?>">
                            <label>
                                <img alt="" src="<?= $service->preview_image_url ?>">
                                <input placeholder="Превью" type="file" name="preview_image">
                            </label>
                            <input placeholder="Название" type="text" name="name" value="<?= $service->name ?>" required>
                            <input placeholder="Цена" type="number" name="base_price" value="<?= $service->base_price ?>" required>
                            <input placeholder="Время выполнения в секундах" type="number" name="base_completion_time" value="<?= $service->base_completion_time ?>" required>
                            <input placeholder="Количество работников" type="number" name="base_workers_amount" value="<?= $service->base_workers_amount ?>" required>
                            <button type="submit" edit-action>Изменить</button>
                        </form>
                        <button delete-action>Удалить</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <section class="orders">
            <h1>Заказы</h1>
            <form action="">
                <h2>Создать заказ</h2>
                <?= $session->set_csrf(); ?>
                <input type="number" name="service_id" placeholder="ID услуги" required>
                <input type="number" name="initial_price" placeholder="Изнчалаьная цена" required>
                <input type="number" name="total_price" placeholder="Итоговая цена" required>
                <select name="status">
                    <option value="initial" selected>Новый</option>
                    <option value="working">В процессе</option>
                    <option value="success">Выполнен</option>
                    <option value="canceled">Отменен</option>
                </select>
                <input type="tel" name="phone_number" placeholder="Номер телефона для связи" required>
                <input type="text" name="address" placeholder="Адрес">
                <input type="date" name="final_date" placeholder="Дата завершения">
                <input type="date" name="contact_date" placeholder="Дата созвона">
                <button type="submit" edit-action>Создать</button>
            </form>
            <ul class="list">
                <?php foreach ($orders as $order) : ?>
                    <li data-id="<?= $order->get_id() ?>">
                        <form action="">
                            <h3>#<?= $order->get_id() ?></h3>
                            <?= $session->set_csrf(); ?>
                            <input type="hidden" name="order_id" value="<?= $order->get_id() ?>">
                            <input placeholder="ID услуги" type="number" name="service_id" value="<?= $order->service_id ?>" required>
                            <input placeholder="Изнчалаьная цена" type="number" name="initial_price" value="<?= $order->initial_price ?>" required>
                            <input placeholder="Итоговая цена" type="number" name="total_price" value="<?= $order->total_price ?>" required>
                            <select name="status">
                                <option value="initial" <?= $order->is_initial ? 'selected' : '' ?>>Новый</option>
                                <option value="working" <?= $order->is_in_work ? 'selected' : '' ?>>В процессе</option>
                                <option value="success" <?= $order->is_finished ? 'selected' : '' ?>>Выполнен</option>
                                <option value="canceled" <?= $order->is_canceled ? 'selected' : '' ?>>Отменен</option>
                            </select>
                            <input placeholder="Номер телефона для связи" type="tel" name="phone_number" value="<?= $order->phone_number ?>" required>
                            <input placeholder="Адрес" type="text" name="address" value="<?= $order->address ?>">
                            <label>
                                <h5>Дата завершения</h5>
                                <input placeholder="Дата завершения" type="date" name="final_date" value="<?= $order->final_date ?>">
                            </label>
                            <label>
                                <h5>Дата созвона</h5>
                                <input placeholder="Дата созвона" type="date" name="contact_date" value="<?= $order->contact_datetime ?>">
                            </label>
                            <button type="submit" edit-action>Изменить</button>
                        </form>
                        <button delete-action>Удалить</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
</body>

</html>