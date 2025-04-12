<?php

namespace app\controllers;

use app\controllers\core\Controller;
use app\models\Order;
use app\models\OrdersAccess;
use app\models\Product;
use app\models\OrderProduct;

class OrderController extends Controller
{
    protected static $model = Order::class;

    public static function index()
    {
        $key = static::get_query_field('key');
        $email = static::get_query_field('email');

        if ($key && $email) {
            $validation = [
                'key' => $key,
                'email' => $email,
            ];

            $is_validated = static::validate_data($validation, [
                'email' => 'required|string|email',
                'key' => 'required|string',
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
            return static::view('views/orders.php', [
                'status' => 'success',
                'orders' => $orders,
                'access' => $access,
            ]);
        }

        return static::view('views/orders.php', [
            'status' => 'form'
        ]);
    }

    public static function show_order($order_id)
    {
        $key = static::get_query_field('key');
        $email = static::get_query_field('email');

        // Validate access first
        if (!$key || !$email) {
            return static::redirect('/orders');
        }

        $access = OrdersAccess::login($key, $email);
        if (!$access) {
            return static::redirect('/orders');
        }

        // Get order and confirm it belongs to this email
        $order = Order::get_by_id($order_id);
        if (!$order || $order->customer_email !== $email) {
            return static::redirect('/orders?key='.$key.'&email='.$email);
        }

        return static::view('views/order.php', [
            'order' => $order,
            'access' => $access
        ]);
    }

    public static function genererate_access()
    {
        $email = static::get_post_field('email');
        
        $validation = [
            'email' => $email
        ];
        
        $is_validated = static::validate_data($validation, [
            'email' => 'required|string|email'
        ]);
        
        if (!$is_validated) {
            return static::json_response([
                'status' => 'error',
                'message' => 'Неверный email адрес'
            ]);
        }
        
        // Check if orders exist for this email
        $orders = Order::where('customer_email', '=', $email)->get();
        if (count($orders) === 0) {
            return static::json_response([
                'status' => 'error',
                'message' => 'Заказов с таким email не найдено'
            ]);
        }
        
        // Create or refresh access
        $access = OrdersAccess::create([
            'email' => $email
        ]);
        
        if (!$access) {
            return static::json_response([
                'status' => 'error',
                'message' => 'Не удалось создать доступ'
            ]);
        }
        
        // Send email with access link (mock in this implementation)
        $sent = static::send_access_email($email, $access);
        
        return static::json_response([
            'status' => 'success',
            'message' => 'Ссылка для доступа отправлена на указанный email'
        ]);
    }
    
    private static function send_access_email($email, $access)
    {
        // In a real implementation, you would send an actual email here
        // But for this example, we'll just simulate success
        
        // The access URL is already generated in the $access->access_url property
        return true;
    }
}