<?php

namespace app\models;

use app\models\core\BaseModel;

class ProductImage extends BaseModel
{
    protected static $table_name = 'product_images';
    protected static $public_fields = ['product_id', 'image_path', 'sort_order'];
    protected static $private_fields = ['id', 'updated_at', 'created_at'];

    public $product_id;
    public $image_path;
    public $sort_order;

    public function get_image_url_attribute()
    {
        global $SITE_URL;
        return "$SITE_URL{$this->image_path}";
    }
}