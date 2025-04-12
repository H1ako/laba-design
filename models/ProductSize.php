<?php

namespace app\models;

use app\models\core\BaseModel;

class ProductSize extends BaseModel
{
    protected static $table_name = 'product_sizes';
    protected static $public_fields = ['product_id', 'size', 'in_stock'];
    protected static $private_fields = ['id', 'updated_at', 'created_at'];

    public $product_id;
    public $size;
    public $in_stock;
}