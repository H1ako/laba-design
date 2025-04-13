<?php

use app\models\News;

// Get news to display
$newsLimit = $newsLimit ?? 'all';
$newsToShow = $newsToShow ?? 'recent';

if ($newsToShow === 'recent') {
    $news = News::where('is_published', '=', 1)
        ->order_by('created_at', 'DESC');

    if ($newsLimit !== 'all') {
        $news = $news->limit($newsLimit);
    }
    $news = $news->get();
} else if ($newsToShow !== null) {
    $news = $newsToShow;
}
?>

<div class="news">
    <ul class="news__list">
        <?php if (count($news) === 0): ?>
            <li class="list__empty">
                <p>Новости не найдены</p>
            </li>
        <?php else: ?>
            <?php foreach ($news as $article): ?>
                <li class="list__item" data-news-id="<?= $article->id ?>">
                    <a href="<?= Router::getRoute('/news/' . $article->id) ?>" class="item__link">
                        <div class="item__image">
                            <div class="image__meta">
                                <span class="meta__date"><?= $article->date_formatted ?></span>
                                <span class="meta__reading-time"><?= $article->reading_time ?> мин</span>
                            </div>
                            <img src="<?= $article->thumb_url ?>" alt="<?= htmlspecialchars($article->title) ?>" class="image__src">
                        </div>
                        <div class="item__info">
                            <h3 class="info__title"><?= htmlspecialchars($article->title) ?></h3>
                            <p class="info__description"><?= htmlspecialchars($article->description) ?></p>
                        </div>
                        <div class="item__action">
                            <span class="action__read-more">Читать далее</span>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>