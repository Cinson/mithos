<?php
    
namespace Mithos\Util;

use Mithos\Core\Config;

class Character {
    
    public static function status($online) {
        if ($online) {
            return '<span class="online">Online</span>';
        }
        return '<span class="offline">Offline</span>';
    }
    
    public static function className($class) {
        $map = Config::get('characters.classes', []);
        return isset($map[$class]) ? $map[$class] : '-';
    }
    
}