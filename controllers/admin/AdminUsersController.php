<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;
use app\models\Order;
use app\models\Session;
use app\models\User;

class AdminUsersController extends Controller
{
    public static function index()
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();

        $search = static::get_query_field('search');
        $page = max(1, intval(static::get_query_field('page') ?? 1));
        $per_page = 20;

        // Get unique customers from orders
        $query = Order::select('MAX(id) as id, customer_email, MAX(customer_full_name) as customer_full_name, MAX(customer_phone_number) as customer_phone_number, MAX(customer_address) as customer_address')
            ->group_by('customer_email');

        if ($search) {
            $query = $query->where_raw(
                "(customer_full_name LIKE ? OR customer_email LIKE ? OR customer_phone_number LIKE ?)",
                ["%$search%", "%$search%", "%$search%"]
            );
        }

        $total_users = $query->count();
        $total_pages = ceil($total_users / $per_page);

        $users = $query->limit($per_page)->offset(($page - 1) * $per_page)->get();

        // Get order count for each user
        foreach ($users as $user) {
            $user->orders_count = Order::where('customer_email', '=', $user->customer_email)->count();
            $user->total_spent = Order::where('customer_email', '=', $user->customer_email)
                ->get()
                ->reduce(function ($carry, $order) {
                    return $carry + $order->total_price;
                }, 0);
        }

        // Also get actual admin users
        $admin_users = User::where('role', '=', 'admin')
            ->or_where('role', '=', 'super_admin')
            ->get();

        return static::view('views/admin/users/index.php', [
            'user' => $session->user,
            'users' => $users,
            'admin_users' => $admin_users,
            'total_users' => $total_users,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'search' => $search
        ]);
    }
}
