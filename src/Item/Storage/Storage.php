<?php

namespace Mithos\Item\Storage;

class Storage {
    
    public static function factory($storage, $file) {
        $class = '\\Mithos\\Item\\Storage\\' . $storage;
        if (class_exists($class)) {
            $factory = new $class($file);
            return $factory;
        }
        throw new \Exception('Storage Class "' . $class . '" not found');
    }
    
}