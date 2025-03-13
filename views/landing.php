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
  <title>Главная</title>
  <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/landing.css">

  <script>
    const SITE_URL = '<?= $SITE_URL ?>';
  </script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/fetchHandlers.js"></script>
  <script type="module" defer src="<?= $SITE_URL ?>/assets/scripts/libs/marquee.js"></script>
  <!-- <script defer src="<?= $SITE_URL ?>/assets/scripts/home.js"></script> -->
</head>

<body>
  <?php include_once('components/banner.php'); ?>
  <?php include_once('components/hero-header.php'); ?>
  <?php include_once('components/brand-marquee.php'); ?>
  <main class="main-content">
    <section class="main-content__section section__history-banner">
      <?php include_once('components/history-banner.php'); ?>      
    </section>
    <section class="main-content__section section_catalog">
      <?php include_once('components/catalog.php'); ?>
      <a href="<?= Router::getRoute('/catalog') ?>" class="section__action-btn">перейти в каталог</a>
    </section>
    <!-- <section class="main-content__section section_reviews">
      <h2 class="section__title">Reviews</h2>
      <?php include_once('components/reviews-marquee.php'); ?>
    </section>
    <section class="main-content__section section_news">
      <h2 class="section__title">Last News</h2>
      <?php include_once('components/news.php'); ?>
      <a href="<?= Router::getRoute('/news') ?>" class="section__action-btn">смотреть все новости</a>
    </section> -->
  </main>
  <?php include_once('components/footer.php'); ?>
</body>

</html>