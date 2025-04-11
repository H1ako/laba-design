<?php
$data = [
    [
        'category' => 'Верхняя одежда',
        'name' => 'Куртка "Tactical Field"',
        'price' => '32 500 ₽',
        'description' => 'Укороченная куртка из вощеного хлопка с водоотталкивающей пропиткой. Детали: съемный капюшон, металлические заклепки, 6 функциональных карманов.',
        'sizes' => 'S–XL',
        'colors' => 'хаки, черный, оливковый'
    ],
    [
        'category' => 'Брюки',
        'name' => 'Брюки "Urban Soldier"',
        'price' => '18 900 ₽',
        'description' => 'Прямые брюки из плотного хлопка-рипстопа с накладными карманами и регулируемым поясом. Усиленные колени и манжеты.',
        'sizes' => '46–56 (RU)',
        'colors' => 'графитовый, камуфляж, темно-синий'
    ],
    [
        'category' => 'Верхняя одежда',
        'name' => 'Commander Oversized',
        'price' => '14 700 ₽',
        'description' => 'Свободная рубашка милитари-кроя с контрастными патчами на плечах. Материал: смесь льна и хлопка.',
        'sizes' => 'XS–XL',
        'colors' => 'песочный, мокко, болотный'
    ],
    [
        'category' => 'Верхняя одежда',
        'name' => 'Парка "Arctic Shield"',
        'price' => '47 800 ₽',
        'description' => 'Теплая удлиненная парка с меховой отделкой капюшона. Внутренняя подкладка из термофлиса, ветрозащитные манжеты.',
        'sizes' => 'M–XXL',
        'colors' => 'белый камуфляж, стальной'
    ],
    [
        'category' => 'Аксессуары',
        'name' => 'Жилет "Cargo Utility"',
        'price' => '22 300 ₽',
        'description' => 'Унисекс-жилет с 8 карманами, включая скрытые отсеки для гаджетов. Создан из нейлона с армированными вставками.',
        'sizes' => 'универсальный (регулируется ремнями)',
        'colors' => 'хаки, черный, серый'
    ],
    [
        'category' => 'Обувь',
        'name' => 'Tundra Boots',
        'price' => '29 500 ₽',
        'description' => 'Высокие ботинки на массивной подошве с противоскользящим протектором. Материал: водоотталкивающая кожа и замша.',
        'sizes' => '36–45',
        'colors' => 'коричневый, черный'
    ],
    [
        'category' => 'Футболки',
        'name' => 'Футболка "Base Camp"',
        'price' => '6 900 ₽',
        'description' => 'Мужская футболка из органического хлопка с принтом в виде топографической карты. Усиленный воротник-стойка.',
        'sizes' => 'S–XL',
        'colors' => 'угольный, оливковый, белый'
    ],
    [
        'category' => 'Юбки',
        'name' => 'Юбка "Barracks Midi"',
        'price' => '16 200 ₽',
        'description' => 'Юбка-трапеция с кожаными ремнями и металлическими пряжками. Карманы в стиле "карго", пояс на шлевках.',
        'sizes' => 'XS–L',
        'colors' => 'хаки, темно-зеленый, черный'
    ],
    [
        'category' => 'Аксессуары',
        'name' => 'Рюкзак "Tactical Night"',
        'price' => '25 000 ₽',
        'description' => 'Вместительный рюкзак с MOLLE-системой для крепления аксессуаров. Водонепроницаемая ткань, светоотражающие элементы.',
        'sizes' => '30×45×15 см',
        'colors' => 'черный, оливковый, камуфляж'
    ],
    [
        'category' => 'Костюмы',
        'name' => 'Костюм "Stealth Uniform"',
        'price' => '54 000 ₽',
        'description' => 'Женский комплект (куртка + брюки) из бесшумной ткани со стрейчем. Маскировочный принт, съемные нашивки.',
        'sizes' => 'XS–L',
        'colors' => 'ночной камуфляж, темно-серый'
    ]
];
?>

<div class="catalog">
    <ul class="catalog__list">
        <?php foreach ($data as $key => $item) : ?>
            <!-- <li class="list__item" data-catalog-product-id="<?= $item['id'] ?>"> -->
            <li class="list__item" data-catalog-product-id="<?= $key ?>">
                <div class="item__image">
                    <div class="image__description">
                        <p class="description__text">
                            <?= $item['description'] ?>
                        </p>
                    </div>
                    <img src="<?= Router::getRoute('/assets/images/product.png') ?>" alt="" class="image__src">
                </div>
                <div class="item__info">
                    <div class="info__left">
                        <p class="left__category"><?= $item['category']  ?></p>
                        <p class="left__name"><?= $item['name']  ?></p>
                    </div>
                    <div class="info__right">
                        <p class="right__price">
                            <?= $item['price']  ?>
                        </p>
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