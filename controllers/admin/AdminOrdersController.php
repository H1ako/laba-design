<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;
use app\models\Order;
use app\models\OrderProduct;
use app\models\Session;

class AdminOrdersController extends Controller
{
    public static function index()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $session = Session::get();
        
        $status_filter = static::get_query_field('status');
        $search = static::get_query_field('search');
        $page = max(1, intval(static::get_query_field('page') ?? 1));
        $per_page = 20;
        
        $query = Order::order_by('created_at', 'DESC');
        
        if ($status_filter) {
            $query = $query->where('status', '=', $status_filter);
        }
        
        if ($search) {
            $query = $query->where_raw("(customer_full_name LIKE ? OR customer_email LIKE ? OR customer_phone_number LIKE ? OR id LIKE ?)", 
                ["%$search%", "%$search%", "%$search%", "%$search%"]);
        }
        
        $total_orders = $query->count();
        $total_pages = ceil($total_orders / $per_page);
        
        $orders = $query->limit($per_page)->offset(($page - 1) * $per_page)->get();
        
        return static::view('views/admin/orders/index.php', [
            'user' => $session->user,
            'orders' => $orders,
            'total_orders' => $total_orders,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'status_filter' => $status_filter,
            'search' => $search
        ]);
    }
    
    public static function show($order_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $session = Session::get();
        
        $order = Order::get_by_id($order_id);
        
        if (!$order) {
            return static::redirect('/admin/orders');
        }
        
        return static::view('views/admin/orders/show.php', [
            'user' => $session->user,
            'order' => $order
        ]);
    }
    
    public static function edit($order_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $session = Session::get();
        
        $order = Order::get_by_id($order_id);
        
        if (!$order) {
            return static::redirect('/admin/orders');
        }
        
        return static::view('views/admin/orders/edit.php', [
            'user' => $session->user,
            'order' => $order,
            'statuses' => ['initial', 'working', 'success', 'canceled']
        ]);
    }
    
    public static function update($order_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $order = Order::get_by_id($order_id);
        
        if (!$order) {
            return static::response_error(404, 'Заказ не найден');
        }
        
        $data = static::get_post_data([
            'customer_full_name' => 'customer_full_name',
            'customer_email' => 'customer_email',
            'customer_phone_number' => 'customer_phone_number',
            'customer_address' => 'customer_address',
            'discount_value' => 'discount_value',
            'status' => 'status'
        ]);
        
        $is_validated = static::validate_data($data, [
            'customer_full_name' => 'required|string|min:3',
            'customer_email' => 'required|email',
            'customer_phone_number' => 'required|string',
            'customer_address' => 'required|string',
            'discount_value' => 'numeric|min:0',
            'status' => 'required|in:initial,working,success,canceled'
        ]);
        
        if (!$is_validated) {
            return static::response_error(400, 'Неверные данные');
        }
        
        $order->update($data);
        
        return static::response_success([
            'message' => 'Заказ успешно обновлен',
            'redirect' => '/admin/orders/' . $order_id
        ]);
    }
    
    public static function update_status($order_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $order = Order::get_by_id($order_id);
        
        if (!$order) {
            return static::response_error(404, 'Заказ не найден');
        }
        
        $status = static::get_post_field('status');
        
        if (!in_array($status, ['initial', 'working', 'success', 'canceled'])) {
            return static::response_error(400, 'Неверный статус');
        }
        
        $order->update(['status' => $status]);
        
        return static::response_success([
            'message' => 'Статус заказа обновлен'
        ]);
    }
    
    public static function delete($order_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $order = Order::get_by_id($order_id);
        
        if (!$order) {
            return static::response_error(404, 'Заказ не найден');
        }
        
        // Delete order products first
        $order_products = OrderProduct::where('order_id', '=', $order_id)->get();
        foreach ($order_products as $order_product) {
            $order_product->delete();
        }
        
        // Then delete the order
        $order->delete();
        
        return static::response_success([
            'message' => 'Заказ удален',
            'redirect' => '/admin/orders'
        ]);
    }
    
    public static function remove_item($order_id, $item_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $item = OrderProduct::get_by_id($item_id);
        
        if (!$item || $item->order_id != $order_id) {
            return static::response_error(404, 'Товар не найден');
        }
        
        $item->delete();
        
        return static::response_success([
            'message' => 'Товар удален из заказа'
        ]);
    }
}