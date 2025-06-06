<?php

namespace app\models;

use app\models\core\BaseModel;
use app\models\core\Collection;

class OrderProduct extends BaseModel
{
    protected static $table_name = 'order_products';

    protected static $public_fields = ['product_id', 'order_id', 'quantity', 'price', 'discount_sum', 'size'];
    protected static $private_fields = ['id', 'updated_at', 'created_at'];

    public $product_id;
    public $order_id;
    public $quantity;
    public $price;
    public $discount_sum;
    public $size;

    protected $product_data;
    protected $order_data;

    public function get_product_attribute()
    {
        if (!isset($this->product_data)) {
            $this->product_data = Product::get_by_id($this->product_id);
        }
        return $this->product_data;
    }

    public function get_order_attribute()
    {
        if (!isset($this->order_data)) {
            $this->order_data = Order::get_by_id($this->order_id);
        }
        return $this->order_data;
    }

    public function get_total_price_attribute()
    {
        return $this->price * $this->quantity;
    }

    public function get_total_price_formatted_attribute()
    {
        return static::format_price($this->total_price);
    }

    public function get_price_formatted_attribute()
    {
        return static::format_price($this->price);
    }

    public function get_discount_sum_formatted_attribute()
    {
        return static::format_price($this->discount_sum);
    }

    public function get_price_original_attribute()
    {
        return $this->price + $this->discount_sum;
    }

    public function get_price_original_formatted_attribute()
    {
        return static::format_price($this->price_original);
    }

    public function get_discount_attribute()
    {
        return $this->discount_sum > 0 ? round($this->discount_sum / $this->price * 100) : 0;
    }
}
