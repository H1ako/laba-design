<?php

global $SITE_URL, $session;
$news = $data['news'] ?? [];
$total_news = $data['total_news'] ?? 0;
$current_page = $data['current_page'] ?? 1;
$total_pages = $data['total_pages'] ?? 1;

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once('components/base-head.php'); ?>
  <title>Новости</title>
  <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/news.css">

  <script>
    const SITE_URL = '<?= $SITE_URL ?>';
  </script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/fetchHandlers.js"></script>
  <script type="module" defer src="<?= $SITE_URL ?>/assets/scripts/libs/marquee.js"></script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/news.js"></script>
</head>

<body>
  <?php include_once('components/banner.php'); ?>
  <?php include_once('components/header.php'); ?>
  <main class="main-content">
    <section class="main-content__section section_news">
      <h2 class="section__title">NEWS</h2>
      
      <?php
        // Pass the news collection to the component
        $newsToShow = $news;
        include('components/news.php');
      ?>
      
      <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <div class="pagination-info">
            Показано <?= ($current_page - 1) * 9 + 1 ?>-<?= min($current_page * 9, $total_news) ?> из <?= $total_news ?> новостей
          </div>
          <div class="pagination-controls">
            <?php if ($current_page > 1): ?>
              <a href="<?= Router::getRoute('/news', ['page' => $current_page - 1]) ?>" class="pagination-link pagination-prev">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
              </a>
            <?php endif; ?>
            
            <?php
              // Show up to 5 page numbers with current page in the middle if possible
              $startPage = max(1, min($current_page - 2, $total_pages - 4));
              $endPage = min($total_pages, max(5, $current_page + 2));
              
              for ($i = $startPage; $i <= $endPage; $i++):
            ?>
              <a href="<?= Router::getRoute('/news', ['page' => $i]) ?>" class="pagination-link <?= $i === $current_page ? 'active' : '' ?>" data-page="<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
              <a href="<?= Router::getRoute('/news', ['page' => $current_page + 1]) ?>" class="pagination-link pagination-next">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </section>
  </main>
  <?php include_once('components/brand-marquee.php'); ?>
  <?php include_once('components/footer.php'); ?>
</body>

</html>