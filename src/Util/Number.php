<?php
    
namespace Mithos\Util;

class Number {
    
    public static function format($number, $decimals = 0, $sep = '.', $thousand = '.') {
        return number_format($number, $decimals, $sep, $thousand);
    }
    
}