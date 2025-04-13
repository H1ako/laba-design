<?php
global $SITE_URL, $session;

$page = $data['page'] ?? null;
$is_preview = $data['is_preview'] ?? isset($_GET['preview']);

if (!$page) {
    return Router::not_found();
}

$title = htmlspecialchars($page->title);
$meta_description = $page->meta_description ? htmlspecialchars($page->meta_description) : '';

// Special handling for iframe preview
$is_iframe = $is_preview && isset($_GET['preview']);
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php if ($meta_description): ?>
  <meta name="description" content="<?= $meta_description ?>">
  <?php endif; ?>
  <?php include_once('components/base-head.php'); ?>
  <title><?= $title ?></title>
  <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/custom-page.css">

  <script>
    const SITE_URL = '<?= $SITE_URL ?>';
  </script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/fetchHandlers.js"></script>
  <script type="module" defer src="<?= $SITE_URL ?>/assets/scripts/libs/marquee.js"></script>
  
  <?php if ($is_iframe): ?>
  <style>
    /* Hide elements that might interfere with clean preview */
    body {
      padding-top: 0 !important;
    }
    .banner, .header-notification {
      display: none !important;
    }
  </style>
  <?php endif; ?>
</head>

<body>
  <?php if (!$is_iframe): ?>
    <?php include_once('components/banner.php'); ?>
    <?php include_once('components/header.php'); ?>
  <?php endif; ?>
  
  <main class="main-content">
    <section class="custom-page">
      <div class="custom-page__container">
        <?php if ($is_preview && !$is_iframe): ?>
          <div class="custom-page__preview-notice">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
              <circle cx="12" cy="12" r="3"></circle>
            </svg>
            <p>Это предварительный просмотр. Страница может выглядеть иначе после публикации.</p>
          </div>
        <?php endif; ?>

        <div class="custom-page__header">
          <h1 class="custom-page__title"><?= $title ?></h1>
        </div>
        
        <div class="custom-page__content">
          <?= $page->content ?>
        </div>
      </div>
    </section>
  </main>
  
  <?php if (!$is_iframe): ?>
    <?php include_once('components/brand-marquee.php'); ?>
    <?php include_once('components/footer.php'); ?>
  <?php endif; ?>
</body>

</html>