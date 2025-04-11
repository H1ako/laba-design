<?php

namespace app\models;

use app\models\core\BaseModel;

class Product extends BaseModel
{
    protected static $table_name = 'products';

    protected static $public_fields = ['name', 'base_price', 'discount_price', 'thumb', 'description'];

    public $name;
    public $base_price;
    public $discount_price;
    public $thumb;
    public $description;

    public function get_thumb_url_attribute()
    {
        global $SITE_URL;

        $src = $this->thumb;
        return "$SITE_URL$src";
    }

    public function get_price_attribute()
    {
        if ($this->discount_price > 0) {
            return $this->discount_price;
        }

        return $this->base_price;
    }

    public function get_price_formatted_attribute()
    {
        return static::format_price($this->price);
    }

    public function get_base_price_formatted_attribute()
    {
        return static::format_price($this->base_price);
    }

    public function get_discount_price_formatted_attribute()
    {
        return static::format_price($this->discount_price);
    }

    public function get_discount_attribute()
    {
        return $this->discount_price > 0 ? round(100 - ($this->discount_price / $this->base_price * 100)) : 0;
    }

    public function get_discount_sum_attribute()
    {
        if ($this->discount_price > 0) {
            return $this->base_price - $this->discount_price;
        }

        return 0;
    }

    public static function create($data)
    {
        if (isset($data['thumb'])) {
            $uploaded = static::upload_image($data['thumb']);
            if ($uploaded) {
                $data['thumb'] = $uploaded;
            } else {
                unset($data['thumb']);
            }
        }

        return parent::create($data);
    }

    public function update($data)
    {
        if (isset($data['thumb'])) {
            $uploaded = static::upload_image($data['thumb']);
            if ($uploaded) {
                $data['thumb'] = $uploaded;
            } else {
                unset($data['thumb']);
            }
        }

        return parent::update($data);
    }
}
