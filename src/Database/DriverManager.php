<?php

namespace Mithos\Database;

class DriverManager {

    public static $conn = null;

    public static function setConnection($config = []) {
        if (static::$conn === null) {
            static::$conn = \Doctrine\DBAL\DriverManager::getConnection($config, new \Doctrine\DBAL\Configuration());
        }
        return static::$conn;
    }

    public static function getConnection() {
        return static::$conn;
    }

}