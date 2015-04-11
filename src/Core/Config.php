<?php

namespace Mithos\Core;

use Mithos\Connection;
use Mithos\Util\Hash;
use Mithos\DB\Mssql;

class Config implements \Countable, \Iterator {

    private $_index;
    private $_count;
    private $_data = array();
    private static $_instance;

    public static function getInstance() {
        if (static::$_instance === null) {
            static::$_instance = new self();
        }
        return static::$_instance;
    }

    public static function add(array $array) {
        $instance = self::getInstance();
        $instance->_index = 0;
        $instance->_data = Hash::merge($instance->_data, $array);
        $instance->_count = count($instance->_data);
    }

    public static function load($files) {
        $instance = self::getInstance();
        if (is_array($files)) {
            foreach ($files as $file) {
                self::load($file);
            }
        } else {
            $instance->add(require $files);
        }
        return $instance;
    }

    public static function loadFromDB() {
        $instance = self::getInstance();
        try {
            $stmt = Connection::getConnection()->query('SELECT * FROM mw_config');
            $configs = [];
            foreach ($stmt->fetchAll() as $config) {
                if ($config['type'] === 'array') {
                    $configs[$config['config']] = json_decode($config['body'], true);
                } elseif ($config['type'] === 'boolean') {
                    $configs[$config['config']] = (bool) $config['body'];
                } else {
                    $configs[$config['config']] = html_entity_decode($config['body']);
                }
            }
            $instance->add(Hash::expand($configs));
        } catch (Exception $ex) {
        }
    }

    public static function save($key, $value = null) {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                self::save($k, $v);
            }
        } else {
            $type = '';
            if (is_array($value)) {
                $type = 'array';
            } elseif (is_bool($value)) {
                $type = 'boolean';
            }

            $result = Connection::getConnection()->prepare('SELECT * FROM mw_config WHERE config = :config', ['config' => $key])->execute();
            if (empty($result)) {
                
                Mssql::getInstance()->query('INSERT INTO mw_config (config, body, type) VALUES (:config[string], :body[string], :type[string])', [
                    'config' => $key,
                    'body' => is_array($value) ? json_encode($value) : htmlentities($value),
                    'type' => $type
                ]);
            } else {
                Mssql::getInstance()->query('UPDATE mw_config set body = :body[string] WHERE config = :config[string]', [
                    'config' => $key,
                    'body' => is_array($value) ? json_encode($value) : htmlentities($value),
                ]);
            }
        }
    }

    public static function get($name = null, $default = null) {
        $instance = self::getInstance();
        if ($name === null) {
            return $instance->_data;
        } else {
            $data = Hash::get($instance->_data, $name);
            if ($data !== null) {
                return $data;
            }
            return $default;
        }
    }

    public function count() {
        return $this->_count;
    }

    public function current() {
        return current($this->_data);
    }

    public function key() {
        return key($this->_data);
    }

    public function next() {
        next($this->_data);
        $this->_index++;
    }

    public function rewind() {
        reset($this->_data);
        $this->_index = 0;
    }

    public function valid() {
        return $this->_index < $this->_count;
    }

}