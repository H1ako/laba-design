<?php

namespace app\controllers;

use app\controllers\core\Controller;
use app\models\Product;
use app\models\Session;
use app\models\User;


class UserController extends Controller
{
    protected static $model = User::class;

    public static function get_cart() {
        $data = static::get_post_field('data');

        if (!$data) {
            return static::response_success(['cart' => null]);
        }

        $cart_output = [];

        foreach ($data as $cart_item) {
            $product = Product::get_by_id($cart_item['product_id']);
            if (!$product) continue;

            $quantity = $cart_item['quantity'] ?? 1;
            $cart_item_data = $product->to_array();
            $cart_item_data['price'] = $product->price;
            $cart_item_data['discount'] = $product->discount;
            $cart_output[] = [
                'product' => $cart_item_data,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity,
                'total_discount_sum' => $product->discount_sum * $quantity,
            ];
        }

        return static::response_success([
            'cart' => [
                'items' => $cart_output,
                'total_price' => array_reduce($cart_output, function ($carry, $item) {
                    return $carry + $item['total_price'];
                }, 0),
                'total_items' => array_reduce($cart_output, function ($carry, $item) {
                    return $carry + $item['quantity'];
                }, 0),
                'total_discount_sum' => array_reduce($cart_output, function ($carry, $item) {
                    return $carry + $item['total_discount_sum'];
                }, 0),
            ]
        ]);
    }

    // public static function sign_in()
    // {
    //     $session = Session::get();

    //     $email = static::get_post_field('email');
    //     $password = static::get_post_field('pass');

    //     $user = $session->sign_in($email, $password);

    //     if ($user) {
    //         return static::response_success([
    //             'user' => $user->to_array(),
    //             'redirect' => '/'
    //         ]);
    //     }

    //     return static::response_error(401, 'Invalid credentials');
    // }

    // public static function sign_up()
    // {
    //     $session = Session::get();

    //     $data = static::get_post_data(['full_name' => 'full_name', 'phone' => 'phone_number', 'pass1' => 'password', 'email' => 'email']);
    //     $is_validated = static::validate_data($data, [
    //         'full_name' => 'required|string|min:8|max:60',
    //         'phone_number' => 'required|phone_number',
    //         'password' => 'required|string|min:8',
    //         'email' => 'required|string|email'
    //     ]);
    //     if (!$is_validated) {
    //         return static::response_error(400, 'Invalid data');
    //     }

    //     if (User::check_for_unique($data['email'])) {
    //         $user = User::create($data);

    //         if ($user) {
    //             $session->set_user($user);

    //             return static::response_success([
    //                 'user' => $user->to_array(),
    //                 'redirect' => '/'
    //             ], 'User created successfully');
    //         } else {
    //             return static::response_error(502, 'Failed to create user');
    //         }
    //     }

    //     return static::response_error(500, 'User already exists');
    // }

    // public static function edit_settings()
    // {
    //     $session = Session::get();

    //     $data = static::get_post_data(['full_name' => 'full_name', 'phone' => 'phone_number', 'address' => 'address']);
    //     $is_validated = static::validate_data($data, [
    //         'full_name' => 'required|string|min:8|max:60',
    //         'phone_number' => 'required|string|phone_number',
    //         'address' => 'string|min:8|max:160',
    //     ]);
    //     if (!$is_validated) {
    //         return static::response_error(400, 'Invalid data');
    //     }

    //     $session->user->update($data);
    //     $session->user->save();

    //     return static::response_success([
    //         'user' => $session->user->to_array(),
    //     ]);
    // }

    // public static function logout()
    // {
    //     $session = Session::get();
    //     $session->logout();

    //     return static::response_success([
    //         'redirect' => '/login'
    //     ]);
    // }
}
