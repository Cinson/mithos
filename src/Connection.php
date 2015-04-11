<?php

namespace Mithos;

class Connection {

    private static $conn;

    public static function connect($config = []) {
        static::$conn = \Doctrine\DBAL\DriverManager::getConnection($config, new \Doctrine\DBAL\Configuration());
        static::$conn->connect();
    }

    public static function getConnection() {
        return static::$conn;
    }

}