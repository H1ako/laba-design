<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;

use app\models\Session;
use app\models\User;


class AdminUserController extends Controller
{
    protected static $model = User::class;


    public static function create()
    {
        $session = Session::get();
        $admin = $session->user->admin;

        $data = static::get_post_data([
            'full_name' => 'full_name',
            'phone_number' => 'phone_number',
            'password' => 'password',
            'email' => 'email'
        ]);
        $is_validated = static::validate_data($data, [
            'full_name' => 'required|string',
            'phone_number' => 'phone_number',
            'password' => 'required|string',
            'email' => 'required|string|email'
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $user = $admin->create_user($data);

        if ($user) {
            return static::response_success([
                'user' => $user->to_array(),
            ], 'User created successfully');
        }

        return static::response_error(502, 'Failed to create user');
    }

    public static function edit($id)
    {
        $session = Session::get();
        $admin = $session->user->admin;

        $data = static::get_post_data([
            'full_name' => 'full_name',
            'phone_number' => 'phone_number',
            'password' => 'password',
            'email' => 'email',
            'address' => 'address'
        ]);
        $is_validated = static::validate_data($data, [
            'full_name' => 'required|string',
            'phone_number' => 'phone_number',
            'password' => 'string',
            'address' => 'string',
            'email' => 'required|string|email'
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $user = $admin->update_user_by_id($id, $data);

        return static::response_success([
            'user' => $user->to_array(),
        ]);
    }

    public static function delete($id) {
        $session = Session::get();
        $admin = $session->user->admin;

        if ($admin->delete_user_by_id($id)) {
            return static::response_success([], 'User deleted successfully');    
        }

        return static::response_error(502, 'User to delete service');
    }
}
