<?php

namespace Mithos\Core;

use Mithos\Util\Hash;

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

    public static function load($files, $path = '') {
        $instance = self::getInstance();
        if (is_array($files)) {
            foreach ($files as $file) {
                self::load($file, $path);
            }
        } else {
            $instance->add(require $path . $files . '.php');
        }
        return $instance;
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