<?php

namespace Mithos\Core;

class MuVersion {
    
    const V97 = 1;
    
    private $_version;
    private static $_instance;
    
    public function getInstance() {
        if (self::_instance === null) {
            self::_instance = new self();
        }
        return self::_instance;
    }
    
    public static function setVersion($version) {
        self::getInstance()->_version = $version;
    }
    
    public static function getVersion() {
        return self::getInstance()->_version;
    }
    
}