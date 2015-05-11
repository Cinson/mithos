<?php

namespace Mithos\Core;

class MuVersion {
    
    const V97 = 1;
    const V100 = 2;
    const V102 = 3;
    
    private $_version;
    private static $_instance;
    
    public static function getInstance() {
        if (static::$_instance === null) {
            static::$_instance = new self();
        }
        return static::$_instance;
    }
    
    public static function setVersion($version) {
        static::getInstance()->_version = $version;
    }
    
    public static function getVersion() {
        return static::getInstance()->_version;
    }

    public static function is() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (static::getVersion() == $arg) {
                return true;
            }
        }
        return false;
    }
    
}