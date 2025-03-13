<?php

namespace app\models\core;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $this->connection = new \mysqli(
            $GLOBALS['DATABASE_SERVERNAME'],
            $GLOBALS['DATABASE_USERNAME'],
            $GLOBALS['DATABASE_PASSWORD'],
            $GLOBALS['DATABASE_NAME']
        );

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function __clone() {}
    public function __wakeup() {}
}
