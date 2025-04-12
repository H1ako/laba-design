<?php

namespace app\models;

use app\models\core\BaseModel;
use app\models\core\Collection;
use Router;

class OrdersAccess extends BaseModel
{
    protected static $table_name = 'orders_access';

    protected static $public_fields = ['key', 'email', 'expires_at'];
    protected static $private_fields = ['id', 'updated_at', 'created_at'];

    public $key;
    public $email;

    protected $expires_at;

    public static function login($key, $email)
    {
        $access = static::where('key', '=', $key)
        ->where('email', '=', $email)
        ->where('expires_at', '>', date('Y-m-d H:i:s'))
        ->get()
        ->first();
        if (!$access) {
            return null;
        }

        $access->expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $access->save();
        
        return $access;
    }

    public function get_orders_attribute()
    {
        return Order::where('customer_email', '=', $this->email)->get();
    }

    public function get_access_url_attribute()
    {
        return Router::getRoute('/orders', [
            'key' => $this->key,
            'email' => $this->email,
        ]);
    }

    public static function generate_key()
    {
        return bin2hex(random_bytes(16));
    }

    public static function create($data) {
        $email = $data['email'] ?? null;
        if (!$email) {
            return null;
        }
        $data['key'] = static::generate_key();
        $data['expires_at'] = date('Y-m-d H:i:s', strtotime('+1 hour'));

        return parent::create($data);
    }
}
