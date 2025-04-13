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

    public function update($data)
    {
        if (isset($data['image_path'])) {
            $uploaded = static::upload_image($data['image_path']);
            if ($uploaded) {
                $data['image_path'] = $uploaded;
            } else {
                unset($data['image_path']);
            }
        }

        return parent::update($data);
    }

    public static function create($data)
    {
        if (isset($data['image_path'])) {
            $uploaded = static::upload_image($data['image_path']);
            if ($uploaded) {
                $data['image_path'] = $uploaded;
            } else {
                unset($data['image_path']);
            }
        }

        return parent::create($data);
    }
}