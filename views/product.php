<?php
global $SITE_URL, $session;
$product = $data['product'] ?? null;

if (!$product) {
    header("Location: " . Router::getRoute('/catalog'));
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
  <title><?= $product->name ?></title>
  <link rel="stylesheet" href="<?= $SITE_URL ?>/assets/styles/css/product.css">
  <script>
    const SITE_URL = '<?= $SITE_URL ?>';
  </script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/libs/fetchHandlers.js"></script>
  <script type="module" defer src="<?= $SITE_URL ?>/assets/scripts/libs/marquee.js"></script>
  <script defer src="<?= $SITE_URL ?>/assets/scripts/product.js"></script>
</head>

<body>
  <?php include_once('components/banner.php'); ?>
  <?php include_once('components/header.php'); ?>
  <main class="main-content">
    <div class="breadcrumbs">
      <div class="breadcrumbs__container">
        <a href="<?= Router::getRoute('/') ?>" class="breadcrumbs__item">Главная</a>
        <span class="breadcrumbs__separator">/</span>
        <a href="<?= Router::getRoute('/catalog') ?>" class="breadcrumbs__item">Каталог</a>
        <span class="breadcrumbs__separator">/</span>
        <span class="breadcrumbs__item breadcrumbs__item--active"><?= $product->name ?></span>
      </div>
    </div>
    
    <div class="product-container">
      <div class="product__gallery">
        <div class="gallery__main-slider" id="main-slider">
          <?php 
          $images = $product->images;
          if (count($images) === 0) {
              $images = [
                  (object)['image_url' => $product->thumb_url]
              ];
          }
          
          foreach($images as $index => $image): 
          ?>
            <div class="slider__slide <?= $index === 0 ? 'active' : '' ?>" data-slide-index="<?= $index ?>">
              <img src="<?= $image->image_url ?>" alt="<?= $product->name ?> - Image <?= $index + 1 ?>">
            </div>
          <?php endforeach; ?>
        </div>
        
        <?php if(count($images) > 1): ?>
        <div class="gallery__thumbnail-slider" id="thumbnail-slider">
          <?php foreach($images as $index => $image): ?>
            <div class="thumbnail__slide <?= $index === 0 ? 'active' : '' ?>" data-slide-index="<?= $index ?>">
              <img src="<?= $image->image_url ?>" alt="Thumbnail <?= $index + 1 ?>">
            </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      
      <div class="product__info">
        <h1 class="info__name"><?= $product->name ?></h1>
        
        <div class="info__pricing">
          <?php if ($product->discount > 0): ?>
            <div class="pricing__current"><?= $product->price_formatted ?> ₽</div>
            <div class="pricing__original"><?= $product->base_price_formatted ?> ₽</div>
            <div class="pricing__discount">-<?= $product->discount ?>%</div>
          <?php else: ?>
            <div class="pricing__current"><?= $product->price_formatted ?> ₽</div>
          <?php endif; ?>
        </div>
        
        <?php if(count($product->characteristics) > 0): ?>
        <div class="info__characteristics">
          <h3 class="characteristics__title">Характеристики</h3>
          <ul class="characteristics__list">
            <?php foreach($product->characteristics as $characteristic): ?>
            <li class="list__item">
              <span class="item__name"><?= $characteristic->name ?></span>
              <span class="item__value"><?= $characteristic->value ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
        
        <?php if(count($product->sizes) > 0): ?>
        <div class="info__sizes">
          <h3 class="sizes__title">Размеры</h3>
          <div class="sizes__list" id="product-sizes">
            <?php foreach($product->sizes as $size): ?>
            <div class="list__item <?= $size->in_stock ? '' : 'out-of-stock' ?>" data-size="<?= $size->size ?>">
              <?= $size->size ?>
              <?php if(!$size->in_stock): ?>
                <span class="item__out-of-stock-indicator"></span>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        
        <div class="info__description">
          <h3 class="description__title">Описание</h3>
          <div class="description__content">
            <?= nl2br($product->description) ?>
          </div>
        </div>
        
        <div class="info__actions" data-catalog-product-id="<?= $product->id ?>">
          <button class="actions__add-to-cart" product-add-to-cart>В корзину</button>
          <div class="actions__change-quantity">
            <button class="change-quantity__action-btn" product-quantity-minus>
              <span class="action-btn__default"><?php include('icons/minus.php') ?></span>
              <span class="action-btn__secondary"><?php include('icons/trash.php') ?></span>
            </button>
            <input product-quantity type="number" class="change-quantity__input" value="1" min="1" max="99" step="1">
            <button class="change-quantity__action-btn" product-quantity-plus><?php include('icons/plus.php') ?></button>
          </div>
        </div>
      </div>
    </div>
  </main>
  <?php include_once('components/brand-marquee.php'); ?>
  <?php include_once('components/footer.php'); ?>
</body>

</html>