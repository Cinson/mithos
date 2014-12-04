<?php

namespace Mithos\DB;

class Connection {

    private static $connection = null;

    public static function connect(array $config) {
        if ($config['pdo']) {
            self::$connection = new \PDO('dblib:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['username'], $config['password']);
        } else {
            self::$connection = mssql_connect($config['host'], $config['username'], $config['password'], true);
            if (!mssql_select_db(config('conn.dbname'), self::$connection)) {
                throw new ConnectionException('Unable to connect to the database');
            }
            ini_set('mssql.charset', 'UTF-8');
        }
    }

    public static function isConnected() {
        return is_resource(self::$connection);
    }

    public static function disconnect() {
        if (self::$connection instanceof \PDO) {
            self::$connection = null;
        } else {
            if (mssql_close(self::getConnection())) {
                self::$connection = null;
            }
        }
    }

    public static function getConnection() {
        return self::$connection;
    }
}