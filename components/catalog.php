<?php

use app\models\Product;

// Get all products from the database
$products = Product::get_all();
?>

<div class="catalog">
    <ul class="catalog__list">
        <?php foreach ($products as $product) : ?>
            <li class="list__item" data-catalog-product-id="<?= $product->id ?>">
                <div class="item__image">
                    <?php if ($product->discount > 0) : ?>
                        <div class="image__discount-badge">-<?= $product->discount ?>%</div>
                    <?php endif; ?>
                    <div class="image__description">
                        <p class="description__text">
                            <?= $product->description ?>
                        </p>
                    </div>
                    <img src="<?= $product->thumb_url ?>" alt="<?= $product->name ?>" class="image__src">
                </div>
                <div class="item__info">
                    <div class="info__left">
                        <p class="left__name"><?= $product->name ?></p>
                    </div>
                    <div class="info__right">
                        <?php if ($product->discount > 0) : ?>
                            <p class="right__price right__price--discount">
                                <span class="price__current"><?= $product->price_formatted ?></span>
                                <span class="price__original"><?= $product->base_price_formatted ?></span>
                            </p>
                        <?php else : ?>
                            <p class="right__price">
                                <?= $product->price_formatted ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="item__actions">
                    <button class="actions__add-to-cart" product-add-to-cart>В Корзину</button>
                    <div class="actions__change-quantity">
                        <button class="change-quantity__action-btn" product-quantity-minus>
                            <span class="action-btn__default"><?php include('icons/minus.php') ?></span>
                            <span class="action-btn__secondary"><?php include('icons/trash.php') ?></span>
                        </button>
                        <input product-quantity type="number" class="change-quantity__input" value="1" min="1" max="99" step="1">
                        <button class="change-quantity__action-btn" product-quantity-plus><?php include('icons/plus.php') ?></button>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>