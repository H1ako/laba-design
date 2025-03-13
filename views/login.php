<?php
global $SITE_URL, $session;
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once('components/base-head.php'); ?>
  <title>Вход|Регистрация</title>
  <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/login.css">

  <script>
    const SITE_URL = '<?= $SITE_URL ?>';
  </script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/controlTabs.js"></script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/fetchHandlers.js"></script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/login.js"></script>
</head>

<body>
  <main class="main-content">
    <div class="main-content__background">
      <?php include('icons/login-bg.php'); ?>
      <div class="background__hero-text">
        <h1 class="hero-text__updating-title">
          <span class="updating-title__const-text">Мы поможем с </span>
          <div class="wrapper">
            <ul class="updating-title__changing-words">
              <li class="changing-words__el">утеплением стен</li>
              <li class="changing-words__el">ремонтом кровли</li>
              <li class="changing-words__el">утеплением крыши</li>
              <li class="changing-words__el">ремонтом изоляции</li>
              <li class="changing-words__el">утеплением пола</li>
              <li class="changing-words__el">утеплением стен</li>
            </ul>
          </div>
        </h1>
      </div>
    </div>
    <section class="main-content__form-section">
      <nav class="form-section__tabs">
        <ul class="tabs__list">
          <li class="list__el">
            <label class="el__wrapper">
              <input type="radio" data-tabs-control="tabs" name="tabs" value="signin" checked>
              <span class="wrapper__title">Вход</span>
            </label>
          </li>
          <li class="list__el">
            <label class="el__wrapper">
              <input type="radio" data-tabs-control="tabs" name="tabs" value="signup">
              <span class="wrapper__title">Регистрация</span>
            </label>
          </li>
          <span class="list__glider"></span>
        </ul>
      </nav>
      <div class="form-section__tabs-content" data-tabs-id="tabs" data-tabs-state="signin">
        <form id="signin-form" class="tabs-content__container container_signin">
          <?php $session->set_csrf(); ?>
          <div class="container__group">
            <label class="container__input appear-animation" style="--animation-step: 1;">
              <?php include('icons/email.php'); ?>
              <input type="email" name="email" placeholder="Почта" required>
            </label>
            <label class="container__input appear-animation" style="--animation-step: 2;">
              <?php include('icons/lock.php'); ?>
              <input type="password" name="pass" placeholder="Пароль" required>
            </label>
          </div>
          <div class="container__group">
            <button class="container__submit appear-animation" style="--animation-step: 3;" type="submit">
              <span class="submit__icon-container">
                <svg
                  viewBox="0 0 14 15"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                  width="10">
                  <path
                    d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                    fill="currentColor"></path>
                </svg>
                <svg
                  viewBox="0 0 14 15"
                  fill="none"
                  width="10"
                  xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                    fill="currentColor"></path>
                </svg>
              </span>
              <span class="submit__text">Войти</span>
            </button>
            <div class="container__status status_error status_401">
              <?php include('icons/error.php'); ?>
              <span class="status__description">
                <p class="description__title">Попробуйте еще раз</p>
                <p class="description__message">Почта или логин введены неверно</p>
              </span>
            </div>
          </div>
        </form>
        <form id="signup-form" class="tabs-content__container container_signup">
          <?php $session->set_csrf(); ?>
          <div class="container__group appear-animation" style="--animation-step: 1;">
            <label class="container__input">
              <?php include('icons/user.php'); ?>
              <input type="text" name="full_name" placeholder="ФИО" required>
            </label>
            <label class="container__input">
              <?php include('icons/phone.php'); ?>
              <input type="tel" name="phone" placeholder="+7 (___) ___-__-__" maxlength="18" required>
            </label>
          </div>
          <div class="container__group appear-animation" style="--animation-step: 2;">
            <label class="container__input">
              <?php include('icons/email.php'); ?>
              <input type="email" name="email" placeholder="Почта" required>
            </label>
            <div class="container__row">
              <label class="container__input">
                <?php include('icons/lock.php'); ?>
                <input type="password" name="pass1" placeholder="Пароль" required>
              </label>
              <label class="container__input">
                <?php include('icons/lock.php'); ?>
                <input type="password" name="pass2" placeholder="Пароль еще раз" required>
              </label>
            </div>
            <label class="container__checkbox">
              <input type="checkbox" name="agreement" required />
              <span class="checkbox__icon">
                <svg viewBox="0 0 12 10" height="10px" width="12px">
                  <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                </svg>
              </span>
              <span class="checkbox__text">Я принимаю условия пользования и соглашаюсь с политикой конфиденциальности</span>
            </label>
          </div>
          <div class="form__group">
            <button class="container__submit appear-animation" style="--animation-step: 3;" type="submit">
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
              <span class="submit__text">Регистрация</span>
            </button>
            <div class="container__status status_error status_400">
              <?php include('icons/error.php'); ?>
              <span class="status__description">
                <p class="description__title">Попробуйте еще раз</p>
                <p class="description__message">Введенные данные не соответствуют требованиям:</p>
                <ul class="description__list">
                  <li>
                    <p>Все поля обязательны</p>
                  </li>
                  <li>
                    <p>Мин. длина пароля 8 символов</p>
                  </li>
                  <li>
                    <p>Мин. длина ФИО 8 символов</p>
                  </li>
                </ul>
              </span>
            </div>
            <div class="container__status status_error status_502">
              <?php include('icons/error.php'); ?>
              <span class="status__description">
                <p class="description__title">Попробуйте еще раз позже</p>
                <p class="description__message">Произошла ошибка на стороне сервера</p>
              </span>
            </div>
            <div class="container__status status_error status_500">
              <?php include('icons/error.php'); ?>
              <span class="status__description">
                <p class="description__title">Попробуйте еще раз</p>
                <p class="description__message">Пользовтель с такой почтой уже зарегистрирован</p>
              </span>
            </div>
            <div class="container__status status_error status_504">
              <?php include('icons/error.php'); ?>
              <span class="status__description">
                <p class="description__title">Попробуйте еще раз</p>
                <p class="description__message">Пароли не совпадают</p>
              </span>
            </div>
          </div>
        </form>
      </div>
    </section>
  </main>
  <?php include_once('components/footer.php'); ?>
</body>

</html>