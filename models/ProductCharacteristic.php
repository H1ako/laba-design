<?php

namespace app\models;

use app\models\core\BaseModel;

class ProductCharacteristic extends BaseModel
{
    protected static $table_name = 'product_characteristics';
    protected static $public_fields = ['product_id', 'name', 'value'];
    protected static $private_fields = ['id', 'updated_at', 'created_at'];

    public $product_id;
    public $name;
    public $value;
}