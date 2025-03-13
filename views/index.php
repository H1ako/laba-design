<?php

use app\models\Service;

global $SITE_URL, $session;

$services = Service::order_by('name', 'ASC')->get();
$service_history = $session->user->service_history->order_by('created_at', 'DESC')->get();
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
  <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/index.css">

  <script>
    const SITE_URL = '<?= $SITE_URL ?>';
  </script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/controlTabs.js"></script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/fetchHandlers.js"></script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/home.js"></script>
</head>

<body>
  <?php include_once('components/header.php'); ?>
  <nav class="main-nav">
    <div class="main-nav__container">
      <ul class="container__list">
        <li class="list__el">
          <label class="el__wrapper">
            <input type="radio" data-tabs-control="tabs" name="tabs" value="services" checked>
            <span class="wrapper__title">Услуги</span>
          </label>
        </li>
        <li class="list__el">
          <label class="el__wrapper">
            <input type="radio" data-tabs-control="tabs" name="tabs" value="history">
            <span class="wrapper__title">История работ</span>
          </label>
        </li>
        <li class="list__el">
          <label class="el__wrapper">
            <input type="radio" data-tabs-control="tabs" name="tabs" value="settings">
            <span class="wrapper__title">Настройки</span>
          </label>
        </li>
        <span class="list__glider"></span>
      </ul>
    </div>
  </nav>
  <main class="main-content" data-tabs-id="tabs" data-tabs-state="services">
    <section class="main-content__tab-content tab-content_services">
      <ul class="tab-content__services">
        <?php foreach ($services as $key => $service): ?>
          <li class="services__el appear-animation" style="--animation-step: <?= $key + 1 ?>">
            <div class="el__content">
              <div class="content__back">
                <div class="back__wrapper">
                  <span></span>
                  <div class="group">
                    <h3><?= $service->name ?></h3>
                    <p><?= $service->base_price_formatted ?>руб.</p>
                  </div>
                </div>
              </div>
              <div class="content__front">
                <div class="front__background">
                  <?php include('icons/circle.php'); ?>
                  <?php include('icons/circle.php'); ?>
                  <?php include('icons/circle.php'); ?>
                </div>
                <div class="front__wrapper">
                  <img src="<?= $SITE_URL ?>/assets/images/service.webp" alt="Утепление дома" class="wrapper__img">
                  <div class="wrapper__description">
                    <p class="description__title">
                      <?= $service->name ?>
                    </p>
                    <p class="description__footer">
                      <?= $service->base_completion_time_formatted ?> &nbsp; | &nbsp; <?= $service->base_workers_amount_formatted ?>
                    </p>
                    <button
                      class="description__action-btn"
                      open-buy-service-modal
                      data-service-preview-image="<?= $service->preview_image_url ?>"
                      data-service-id="<?= $service->get_id() ?>"
                      data-service-name="<?= $service->name ?>"
                      data-service-price="<?= $service->base_price_formatted ?>"
                      data-service-workers="<?= $service->base_workers_amount_formatted ?>"
                      data-service-time="<?= $service->base_completion_time_formatted ?>">
                      <span class="action-btn__text">
                        <span class="text__one"><?= $service->base_price_formatted ?>руб.</span>
                        <span class="text__two">Оформить?</span>
                      </span>
                      <span class="action-btn__icon">
                        <?php include('icons/plus.php'); ?>
                      </span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <section class="main-content__tab-content tab-content_history">
      <ul class="tab-content__history-list">
        <?php foreach ($service_history as $key => $order): ?>
          <li class="history-list__el appear-animation" style="--animation-step: <?= $key + 1 ?>">
            <div class="el__background">
              <div class="background__circles">
                <?php include('icons/circle.php'); ?>
                <?php include('icons/circle.php'); ?>
                <?php include('icons/circle.php'); ?>
              </div>
              <?php
              // var_dump($order->is_finished);
              // exit;
              ?>
              <?php if ($order->images->count() > 0): ?>
                <ul class="background__images">
                  <?php foreach ($order->images->take(3) as $image): ?>
                    <li class="images__el">
                      <img src="<?= $image->site_url ?>" alt="Картинка отчет">
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
            <div class="el__container">
              <?php if ($order->is_canceled): ?>
                <div class="container__status status_canceled">
                  <?php include('icons/ban.php'); ?>
                </div>
              <?php elseif ($order->is_finished): ?>
                <div class="container__status status_done">
                  <?php include('icons/check.php'); ?>
                </div>
              <?php elseif ($order->is_in_work): ?>
                <div class="container__status status_working">
                  <?php include('icons/time.php'); ?>
                </div>
              <?php else: ?>
                <div class="container__status status_initial">
                  <?php include('icons/dots.php'); ?>
                </div>
              <?php endif; ?>
              <header class="container__header">
                <p class="header__id">номер #<?= $order->get_id() ?></p>
                <h3 class="header__title">Услуга: <?= $order->service->name ?></h3>
                <?php if ($order->is_initial): ?>
                  <p class="header__bottom">Дата визита: <?= $order->contact_datetime_formatted ?></p>
                <?php elseif ($order->is_canceled): ?>
                <?php elseif ($order->is_finished): ?>
                  <p class="header__bottom">Срок реализация: <?= $order->created_at_formatted ?> - <?= $order->final_date_formatted ?></p>
                <?php else: ?>
                  <p class="header__bottom"><span>Начало работы: <?= $order->created_at_formatted ?></span><span>Конец работы: <?= $order->final_date_formatted ?></span></p>
                <?php endif; ?>
              </header>
              <div class="container__botom">
                <button class="bottom__action-btn"
                  <?= $order->is_initial ? 'open-edit-history-modal' : 'open-order-details-modal' ?>
                  data-order-id="<?= $order->get_id() ?>"
                  data-order-service-preview-image="<?= $order->service->preview_image_url ?>"
                  data-order-service-name="<?= $order->service->name ?>"
                  data-order-service-price="<?= $order->service->base_price_formatted ?>"
                  data-order-service-workers="<?= $order->service->base_workers_amount_formatted ?>"
                  data-order-service-time="<?= $order->service->base_completion_time_formatted ?>"
                  data-order-contact-phone="<?= $order->phone_number ?>"
                  data-order-contact-datetime="<?= $order->contact_datetime ?>"
                  data-order-address="<?= $order->address ?>">
                  <span class="action-btn__text">
                    <?php if ($order->is_initial): ?>
                      <span class="text__one">Редактировать</span>
                      <span class="text__two">Редактировать?</span>
                    <?php else: ?>
                      <span class="text__one">Подробнее</span>
                      <span class="text__two">Подробнее?</span>
                    <?php endif; ?>
                  </span>
                  <span class="action-btn__icon">
                    <?php if ($order->is_initial): ?>
                      <?php include('icons/edit.php'); ?>
                    <?php else: ?>
                      <?php include('icons/arrow-up.php'); ?>
                    <?php endif; ?>
                  </span>
                </button>
                <div class="bottom__price">
                  <?php if ($order->is_finished): ?>
                    <p class="price__title">Итоговая стоимость:</p>
                    <p class="price__value"><?= $order->total_price_formatted ?>руб.</p>
                  <?php elseif ($order->is_canceled): ?>
                    <p class="price__title">Отменен</p>
                  <?php else: ?>
                    <p class="price__title">Оценочная стоимость:</p>
                    <p class="price__value"><?= $order->total_price_formatted ?>руб.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
    <section class="main-content__tab-content tab-content_settings">
      <form id="settings-form" class="tab-content__form">
        <?= $session->set_csrf(); ?>
        <div class="form__group appear-animation" style="--animation-step: 1;">
          <div class="form__row">
            <label class="form__input">
              <?php include('icons/email.php'); ?>
              <input type="email" name="email" placeholder="Почта" value="<?= $session->user->get_email() ?>" disabled>
            </label>
            <button class="form__plain-btn">
              Сменить почту
            </button>
          </div>
          <div class="form__row">
            <label class="form__input">
              <?php include('icons/lock.php'); ?>
              <input type="password" name="old_pass" value="*********" disabled>
            </label>
            <button class="form__plain-btn">
              Сменить пароль
            </button>
          </div>
        </div>
        <div class="form__group appear-animation" style="--animation-step: 2;">
          <label class="form__input">
            <?php include('icons/user.php'); ?>
            <input type="text" name="full_name" placeholder="ФИО" required value="<?= $session->user->full_name ?>">
          </label>
          <label class="form__input">
            <?php include('icons/address.php'); ?>
            <input type="text" name="address" placeholder="Адрес" value="<?= $session->user->address ?>">
          </label>
          <label class="form__input">
            <?php include('icons/phone.php'); ?>
            <input type="tel" name="phone" placeholder="Номер телефона" required value="<?= $session->user->phone_number ?>">
          </label>
        </div>
        <div class="form__group appear-animation" style="--animation-step: 3;">
          <button class="form__submit" type="submit">
            <span class="submit__icon-container">
              <svg
                viewBox="0 0 14 15"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                class="button__icon-svg"
                width="10">
                <path
                  d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                  fill="currentColor"></path>
              </svg>
              <svg
                viewBox="0 0 14 15"
                fill="none"
                width="10"
                xmlns="http://www.w3.org/2000/svg"
                class="button__icon-svg button__icon-svg--copy">
                <path
                  d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                  fill="currentColor"></path>
              </svg>
            </span>
            <span class="submit__text">Сохранить</span>
          </button>
          <div class="form__status status_success status_200">
            <?php include('icons/success.php'); ?>
            <span class="status__description">
              <p class="description__title">Успешно сохранено</p>
            </span>
          </div>
          <div class="form__status status_error status_400">
            <?php include('icons/error.php'); ?>
            <span class="status__description">
              <p class="description__title">Попробуйте еще раз</p>
              <p class="description__message">Введенные данные не соответствуют требованиям:</p>
              <ul class="description__list">
                <li>
                  <p>ФИО и Телефон обязательны</p>
                </li>
                <li>
                  <p>Мин. длина ФИО 8 символов, макс. - 60</p>
                </li>
                <li>
                  <p>Мин. длина Адреса 8 символов, макс. - 160</p>
                </li>
              </ul>
            </span>
          </div>
        </div>
      </form>
    </section>
  </main>
  <?php include_once('components/footer.php'); ?>
  <?php include_once('components/buy-service-modal.php'); ?>
  <?php include_once('components/edit-history-modal.php'); ?>
</body>

</html>