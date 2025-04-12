<?php

namespace app\controllers;

use app\controllers\core\Controller;
use app\models\Product;

class ProductController extends Controller
{
    protected static $model = Product::class;

    public static function show($product_id)
    {
        $product = Product::get_by_id($product_id);
        
        if (!$product) {
            return static::redirect('/catalog');
        }
        
        return static::view('views/product.php', [
            'product' => $product
        ]);
    }
}