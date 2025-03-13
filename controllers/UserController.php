<?php

namespace app\controllers;

use app\controllers\core\Controller;

use app\models\Session;
use app\models\User;


class UserController extends Controller
{
    protected static $model = User::class;

    public static function sign_in()
    {
        $session = Session::get();

        $email = static::get_post_field('email');
        $password = static::get_post_field('pass');

        $user = $session->sign_in($email, $password);

        if ($user) {
            return static::response_success([
                'user' => $user->to_array(),
                'redirect' => '/'
            ]);
        }

        return static::response_error(401, 'Invalid credentials');
    }

    public static function sign_up()
    {
        $session = Session::get();

        $data = static::get_post_data(['full_name' => 'full_name', 'phone' => 'phone_number', 'pass1' => 'password', 'email' => 'email']);
        $is_validated = static::validate_data($data, [
            'full_name' => 'required|string|min:8|max:60',
            'phone_number' => 'required|phone_number',
            'password' => 'required|string|min:8',
            'email' => 'required|string|email'
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        if (User::check_for_unique($data['email'])) {
            $user = User::create($data);

            if ($user) {
                $session->set_user($user);

                return static::response_success([
                    'user' => $user->to_array(),
                    'redirect' => '/'
                ], 'User created successfully');
            } else {
                return static::response_error(502, 'Failed to create user');
            }
        }

        return static::response_error(500, 'User already exists');
    }

    public static function edit_settings()
    {
        $session = Session::get();

        $data = static::get_post_data(['full_name' => 'full_name', 'phone' => 'phone_number', 'address' => 'address']);
        $is_validated = static::validate_data($data, [
            'full_name' => 'required|string|min:8|max:60',
            'phone_number' => 'required|string|phone_number',
            'address' => 'string|min:8|max:160',
        ]);
        if (!$is_validated) {
            return static::response_error(400, 'Invalid data');
        }

        $session->user->update($data);
        $session->user->save();

        return static::response_success([
            'user' => $session->user->to_array(),
        ]);
    }

    public static function logout()
    {
        $session = Session::get();
        $session->logout();

        return static::response_success([
            'redirect' => '/login'
        ]);
    }
}
