<?php

namespace Mithos\Ranking;

use Mithos\Plugin;

class Ranking {
    
    public static function factory($ranking) {
        $children = [];
        foreach (get_declared_classes() as $class) {
            if ($class instanceof AbstractRanking) {
                $children[] = $class;
            }
        }

        $class = '\\Mithos\\Ranking\\' . $ranking;
        if (class_exists($class)) {
            $factory = new $class();
            return $factory;
        } else {

        }
        throw new \Exception('Ranking Class "' . $class . '" not found');
    }
    
}