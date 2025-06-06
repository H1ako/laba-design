<?php

namespace app\models;

use app\models\core\BaseModel;
use app\models\core\Collection;

class Order extends BaseModel
{
    protected static $table_name = 'orders';

    protected static $public_fields = ['discount_value', 'customer_email', 'customer_phone_number', 'customer_full_name', 'customer_address'];
    protected static $private_fields = ['id', 'updated_at', 'created_at', 'status'];

    public $discount_value;
    public $customer_email;
    public $customer_phone_number;
    public $customer_full_name;
    public $customer_address;
    protected $status;

    protected $items_data;


    public function get_items_attribute()
    {
        if (!isset($this->items_data)) {
            $this->items_data = OrderProduct::where('order_id', '=', $this->id)->get();
        }
        return $this->items_data;
    }

    public function get_status_attribute()
    {
        return $this->status;
    }

    public function set_initial()
    {
        $this->set_status('initial');
    }

    public function set_in_work()
    {
        $this->set_status('working');
    }

    public function finish()
    {
        $this->set_status('success');
    }

    public function cancel()
    {
        $this->set_status('canceled');
    }

    public function get_total_price_attribute()
    {
        error_log('asdas ' . count($this->items));
        $itemsCost = $this->items->reduce(function ($carry, $item) {
            return $carry + $item->price * $item->quantity;
        }, 0);
        return $itemsCost - $this->discount_value;
    }

    public function get_discount_value_formatted_attribute()
    {
        return static::format_price($this->discount_value);
    }

    public function get_total_price_formatted_attribute()
    {
        return static::format_price($this->total_price);
    }

    public function get_total_discount_sum_attribute()
    {
        $itemsDiscount = $this->items->reduce(function ($carry, $item) {
            return $carry + $item->discount_sum * $item->quantity;
        }, 0);
        return $itemsDiscount + $this->discount_value;
    }

    public function get_total_discount_sum_formatted_attribute()
    {
        return static::format_price($this->total_discount_sum);
    }

    public function get_total_discount_attribute()
    {
        return $this->total_discount_sum > 0 ? round($this->total_discount_sum / $this->total_price * 100) : 0;
    }

    public function get_total_quantity_attribute()
    {
        return $this->items->reduce(function ($carry, $item) {
            return $carry + $item->quantity;
        }, 0);
    }

    public function set_status($status)
    {
        if (!in_array($status, ['initial', 'working', 'success', 'canceled'])) return false;

        $this->status = $status;
    }
}
