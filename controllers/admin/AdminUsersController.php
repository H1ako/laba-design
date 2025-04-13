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

    /**
     * Show details of a customer
     */
    public static function show($user_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();

        $customer = Order::get_by_id($user_id);

        if (!$customer) {
            return static::redirect('/admin/users');
        }

        // Get all orders for this customer
        $orders = Order::where('customer_email', '=', $customer->customer_email)
            ->order_by('created_at', 'DESC')
            ->get();

        // Calculate total spent
        $total_spent = $orders->reduce(function ($carry, $order) {
            return $carry + $order->total_price;
        }, 0);

        return static::view('views/admin/users/show.php', [
            'user' => $session->user,
            'customer' => $customer,
            'orders' => $orders,
            'total_spent' => $total_spent
        ]);
    }

    /**
     * Delete a customer
     */
    public static function delete($user_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        // We don't actually delete customers, just their association with orders
        // so we only need the ID to get their customer information
        $customer = Order::get_by_id($user_id);

        if (!$customer) {
            return static::response_error(404, 'Customer not found');
        }

        // Get customer email
        $customer_email = $customer->customer_email;

        // Anonymize all orders with this email
        $orders = Order::where('customer_email', '=', $customer_email)->get();
        foreach ($orders as $order) {
            $order->update([
                'customer_full_name' => 'Deleted User',
                'customer_email' => 'deleted_' . time() . '@example.com',
                'customer_phone_number' => '',
                'customer_address' => ''
            ]);
            $order->save();
        }

        return static::response_success([
            'message' => 'Customer data anonymized successfully'
        ]);
    }

    /**
     * Show edit form for admin user
     */
    public static function edit_admin($admin_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();

        $admin = User::get_by_id($admin_id);

        if (!$admin || !$admin->is_admin) {
            return static::redirect('/admin/users');
        }

        return static::view('views/admin/users/edit.php', [
            'user' => $session->user,
            'admin' => $admin
        ]);
    }

    /**
     * Update admin user
     */
    public static function update_admin($admin_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $admin = User::get_by_id($admin_id);

        if (!$admin || !$admin->is_admin) {
            return static::response_error(404, 'Administrator not found');
        }

        $data = static::get_post_data([
            'username' => 'username',
            'email' => 'email',
            'password' => 'password',
            'role' => 'role'
        ]);

        $validation_rules = [
            'username' => 'required|string|min:3',
            'email' => 'required|email',
            'role' => 'required|in:admin,super_admin'
        ];

        // Only validate password if it's provided
        if (!empty($data['password'])) {
            $validation_rules['password'] = 'string|min:6';
        } else {
            unset($data['password']);  // Don't update password if not provided
        }

        $is_validated = static::validate_data($data, $validation_rules);

        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $admin->update($data);
        $admin->save();

        return static::response_success([
            'message' => 'Administrator updated successfully',
            'redirect' => '/admin/users'
        ]);
    }

    /**
     * Delete admin user
     */
    public static function delete_admin($admin_id)
    {
        $auth_check = AdminAuthController::check_admin();
        if ($auth_check !== true) {
            return $auth_check;
        }

        $session = Session::get();
        $current_user = $session->user;

        // Don't allow deleting yourself
        if ($current_user->id == $admin_id) {
            return static::response_error(400, 'You cannot delete your own account');
        }

        $admin = User::get_by_id($admin_id);

        if (!$admin || !$admin->is_admin) {
            return static::response_error(404, 'Administrator not found');
        }

        // Only super_admins can delete other super_admins
        if ($admin->role === 'super_admin' && $current_user->role !== 'super_admin') {
            return static::response_error(403, 'You do not have permission to delete a super administrator');
        }

        $admin->delete();

        return static::response_success([
            'message' => 'Administrator deleted successfully'
        ]);
    }
}
