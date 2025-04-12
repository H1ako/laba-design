<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;
use app\models\Order;
use app\models\Product;
use app\models\Session;

class AdminDashboardController extends Controller
{
    public static function index()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }
        
        $session = Session::get();
        
        $orders_count = Order::count();
        $products_count = Product::count();
        
        // Get recent orders
        $recent_orders = Order::order_by('created_at', 'DESC')->limit(5)->get();
        
        // Get low stock products
        $low_stock_products = [];
        $products = Product::get_all();
        foreach ($products as $product) {
            $sizes_with_stock = array_filter($product->sizes->to_array(), function($size) {
                return $size['in_stock'];
            });
            
            if (count($sizes_with_stock) == 0) {
                $low_stock_products[] = $product;
            }
            
            if (count($low_stock_products) >= 5) break;
        }
        
        return static::view('views/admin/dashboard.php', [
            'user' => $session->user,
            'orders_count' => $orders_count,
            'products_count' => $products_count,
            'recent_orders' => $recent_orders,
            'low_stock_products' => $low_stock_products
        ]);
    }
}