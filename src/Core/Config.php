<?php

namespace Mithos\Core;

use Mithos\Connection;
use Mithos\Database\DriverManager;
use Mithos\Util\Hash;

class Config implements \Countable, \Iterator {

    private $_index;
    private $_count;
    private $_data = [];
    private static $_instance;

    public static function getInstance() {
        if (static::$_instance === null) {
            static::$_instance = new self();
        }
        return static::$_instance;
    }

    public static function add(array $array) {
        $instance = static::getInstance();
        $instance->_index = 0;
        $instance->_data = Hash::merge($instance->_data, $array);
        $instance->_count = count($instance->_data);
    }

    public static function load($files) {
        $instance = static::getInstance();
        if (is_array($files)) {
            foreach ($files as $file) {
                static::load($file);
            }
        } else {
            $instance->add(require CONFIGS_PATH . $files . '.php');
        }
        return $instance;
    }

    public static function loadFromDB() {
        $instance = static::getInstance();
        try {
            $stmt = DriverManager::getConnection()->query('SELECT * FROM mw_config');
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
                static::save($k, $v);
            }
        } else {
            $type = '';
            if (is_array($value)) {
                $type = 'array';
            } elseif (is_bool($value)) {
                $type = 'boolean';
            }

            $result = DriverManager::getConnection()->fetchAssoc('SELECT * FROM mw_config WHERE config = :config', ['config' => $key]);
            if (empty($result)) {
                DriverManager::getConnection()->insert('mw_config', [
                    'config' => $key,
                    'body' => is_array($value) ? json_encode($value) : htmlentities($value),
                    'type' => $type
                ]);
            } else {
                DriverManager::getConnection()->update('mw_config', [
                    'body' => is_array($value) ? json_encode($value) : htmlentities($value)
                ], [
                    'config' => $key
                ]);
            }
        }
    }

    public static function get($name = null, $default = null) {
        $instance = static::getInstance();
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