<?php

namespace app\controllers\admin;

use app\controllers\core\Controller;
use app\models\Session;
use app\models\User;

class AdminAuthController extends Controller
{
    public static function login_page()
    {
        $session = Session::get();
        
        if ($session->is_authed && $session->user->is_admin) {
            return static::redirect('/admin');
        }
        
        return static::view('views/admin/login.php');
    }
    
    public static function login()
    {
        $username = static::get_post_field('username');
        $password = static::get_post_field('password');
        
        if (!$username || !$password) {
            return static::view('views/admin/login.php', [
                'error' => 'Пожалуйста, введите имя пользователя и пароль'
            ]);
        }
        
        $user = User::get_by_username($username);
        
        if ($user && $user->check_password($password) && $user->is_admin) {
            $session = Session::get();
            $session->set_user($user);
            
            return static::redirect('/admin');
        } else {
            return static::view('views/admin/login.php', [
                'error' => 'Неверное имя пользователя или пароль',
                'username' => $username
            ]);
        }
    }
    
    public static function logout()
    {
        $session = Session::get();
        $session->logout();
        
        return static::redirect('/admin/login');
    }
    
    public static function check_admin()
    {
        $session = Session::get();
        
        if (!$session->is_authed || !$session->user->is_admin) {
            return static::redirect('/admin/login');
        }
        
        return true;
    }
}