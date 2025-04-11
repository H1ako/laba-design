<?php

namespace app\controllers;

use app\controllers\core\Controller;
use app\models\Order;
use app\models\OrdersAccess;
use app\models\Product;


class OrderController extends Controller
{
    protected static $model = Order::class;

    public static function index()
    {
        $key = static::get_query_field('key');
        $email = static::get_query_field('email');
        $validation = [
            'key' => $key,
            'email' => $email,
        ];

        if ($key || $email) {
            $is_validated = static::validate_data($validation, [
                'email' => 'required|string|email',
                'key' => 'reuired|string',
            ]);
            if (!$is_validated) {
                return static::view('views/orders.php', [
                    'status' => 'error',
                    'message' => 'Неправильный формат ключа или email',
                ]);
            }

            $access = OrdersAccess::login($key, $email);
            if (!$access) {
                return static::view('views/orders.php', [
                    'status' => 'error',
                    'message' => 'Неправильный ключ или email',
                ]);
            }

            $orders = $access->orders;
            if (!$orders) {
                return static::view('views/orders.php', [
                    'status' => 'success',
                    'message' => 'Заказов не найдено',
                ]);
            }

            return static::view('views/orders.php', [
                'status' => 'success',
                'orders' => $orders,
            ]);
        }

        return static::view('views/orders.php');
    }
}
