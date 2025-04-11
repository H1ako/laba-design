<?php

namespace app\models;

use app\models\core\Database;

class Session
{
    protected static $conn;
    protected static $table_name = 'sessions';

    protected $user_data;
    protected $key;


    protected function __construct($session_key)
    {
        $this->key = $session_key;
    }

    public function get_csrf_token()
    {
        if (!isset($_SESSION['csrf'])) {
            $this->generate_csrf_token();
        }
        return $_SESSION['csrf'];
    }

    public function set_csrf()
    {
        echo '<input type="hidden" name="csrf" value="' . $this->get_csrf_token() . '">';
    }

    public function set_csrf_meta()
    {
        echo '<meta name="csrf-token" content="' . $this->get_csrf_token() . '">';
    }

    protected function generate_csrf_token()
    {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    public function validate_csrf_token($token)
    {
        return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
    }

    public function regenerate_csrf_token()
    {
        $this->generate_csrf_token();
    }

    protected static function connect()
    {
        if (!static::$conn) {
            $database = Database::getInstance();
            static::$conn = $database->getConnection();
        }
    }

    protected function get_user()
    {
        static::connect();

        $smtp = static::$conn->prepare('SELECT user_id FROM ' . static::$table_name . ' WHERE `key`= ? AND `expires_at` > NOW() LIMIT 1;');
        $smtp->bind_param('s', $this->key);
        $smtp->execute();
        $result = $smtp->get_result();
        if ($result->num_rows === 0) {
            return null;
        }

        $user_id = $result->fetch_assoc()['user_id'];

        return User::get_by_id($user_id);
    }

    public static function get()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (static::is_session_key_set()) {
            return new static(static::get_session_key());
        }

        return static::create();
    }

    protected static function is_session_key_set()
    {
        return isset($_SESSION['sessionkey']);
    }

    protected static function get_session_key()
    {
        return isset($_SESSION['sessionkey']) ? $_SESSION['sessionkey'] : null;
    }

    protected static function create()
    {
        $new_session_key = bin2hex(random_bytes(50));
        static::set_session_key($new_session_key);

        return new static($new_session_key);
    }

    protected static function set_session_key($key)
    {
        $_SESSION['sessionkey'] = $key;
    }

    public function get_is_authed_attribute()
    {
        return $this->user != null && get_class($this->user) === User::class;
    }

    public function sign_in($login, $password)
    {
        $user = User::get_by_email($login);

        if ($user && $user->check_password($password)) {
            $this->set_user($user);
            return $user;
        }
        return false;
    }

    public function set_user($user)
    {
        static::connect();

        $smtp = static::$conn->prepare('INSERT INTO ' . static::$table_name . ' (`user_id`, `key`, `expires_at`) VALUES (?, ?, NOW() + INTERVAL 7 DAY)');
        $user_id = $user->get_id();
        $smtp->bind_param('ss', $user_id, $this->key);

        if ($smtp->execute()) {
            $this->user_data = $user;
        }
    }

    public function logout()
    {
        static::connect();

        $smtp = static::$conn->prepare('DELETE FROM ' . static::$table_name . ' WHERE `key` = ?');
        $smtp->bind_param('s', $this->key);
        $smtp->execute();

        $this->remove_session_key();
    }

    protected function remove_session_key()
    {
        unset($_SESSION['sessionkey']);
    }

    public function __get($name)
    {
        $method = 'get_' . ucfirst($name) . '_attribute';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \Exception("Property '{$name}' does not exist or is inaccessible.");
    }

    public function get_user_attribute()
    {
        if (!isset($this->user_data)) {
            $this->user_data = $this->get_user();
        }
        return $this->user_data;
    }

    public function __clone() {}
    public function __wakeup() {}
}
