<?php

global $SITE_URL, $session;
$news = $data['news'] ?? null;
$related_news = $data['related_news'] ?? [];

if (!$news) {
  header('Location: ' . Router::getRoute('/news'));
  exit;
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php include_once('components/base-head.php'); ?>
  <title><?= htmlspecialchars($news->title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($news->description) ?>">
  <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/news-single.css">

  <script>
    const SITE_URL = '<?= $SITE_URL ?>';
  </script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/fetchHandlers.js"></script>
  <script type="module" defer src="<?= $SITE_URL ?>/assets/scripts/libs/marquee.js"></script>
</head>

<body>
  <?php include_once('components/banner.php'); ?>
  <?php include_once('components/header.php'); ?>
  <main class="main-content">
    <article class="news-article">
      <header class="news-article__header">
        <div class="header__meta">
          <div class="meta__date"><?= $news->date_formatted ?></div>
          <div class="meta__reading-time"><?= $news->reading_time ?> минут чтения</div>
        </div>
        <h1 class="header__title"><?= htmlspecialchars($news->title) ?></h1>
        <p class="header__description"><?= htmlspecialchars($news->description) ?></p>
      </header>
      
      <div class="news-article__image">
        <img src="<?= $news->thumb_url ?>" alt="<?= htmlspecialchars($news->title) ?>" class="image__src">
      </div>
      
      <div class="news-article__content">
        <?= $news->content ?>
      </div>
      
      <footer class="news-article__footer">
        <div class="footer__share">
          <span class="share__title">Поделиться:</span>
          <div class="share__buttons">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(Router::getRoute('/news/' . $news->id, [], true)) ?>" target="_blank" rel="noopener noreferrer" class="share-button facebook" title="Поделиться в Facebook">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
            </a>
            <a href="https://twitter.com/intent/tweet?text=<?= urlencode(htmlspecialchars($news->title)) ?>&url=<?= urlencode(Router::getRoute('/news/' . $news->id, [], true)) ?>" target="_blank" rel="noopener noreferrer" class="share-button twitter" title="Поделиться в Twitter">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
            </a>
            <a href="https://t.me/share/url?url=<?= urlencode(Router::getRoute('/news/' . $news->id, [], true)) ?>&text=<?= urlencode(htmlspecialchars($news->title)) ?>" target="_blank" rel="noopener noreferrer" class="share-button telegram" title="Поделиться в Telegram">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2l-2 14.5-7 3-7-3L3.5 2l9 4z"></path><path d="M21.5 2L12.5 10"></path><path d="M3.5 2l9 8"></path><path d="M12.5 22v-8"></path></svg>
            </a>
          </div>
        </div>
        
        <a href="<?= Router::getRoute('/news') ?>" class="footer__back-link">
          <?php include('icons/arrow-left.php'); ?>
          <span>Вернуться к списку новостей</span>
        </a>
      </footer>
    </article>
    
    <?php if (count($related_news) > 0): ?>
    <section class="related-news">
      <h2 class="related-news__title">Вам также может быть интересно</h2>
      <?php
        $newsToShow = $related_news;
        $newsLimit = 4;
        include('components/news.php');
      ?>
    </section>
    <?php endif; ?>
  </main>
  <?php include_once('components/brand-marquee.php'); ?>
  <?php include_once('components/footer.php'); ?>
</body>

</html>