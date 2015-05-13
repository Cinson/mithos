<?php
    
namespace Mithos\Util;

class Number {
    
    public static function format($number, $decimals = 0, $sep = '.', $thousand = '.') {
        return number_format($number, $decimals, $sep, $thousand);
    }

    public function toFloat($value) {
        return preg_replace('/^([0-9]+)(\.([0-9]+))?,([0-9]+)$/', '$1$3.$4', $value);
    }
}