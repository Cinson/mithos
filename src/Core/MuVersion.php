<?php

namespace Mithos\Core;

class MuVersion {
    
    const V97 = 1;
    const V100 = 2;
    const V102 = 3;
    
    private $_version;
    private static $_instance;
    
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public static function setVersion($version) {
        self::getInstance()->_version = $version;
    }
    
    public static function getVersion() {
        return self::getInstance()->_version;
    }

    public static function is() {
        $args = func_get_args();
        foreach ($args as $arg) {
            if (self::getVersion() == $arg) {
                return true;
            }
        }
        return false;
    }
    
}