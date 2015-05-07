<?php

namespace Mithos\Util;

class String {

    public static function truncate($string, $length, $etc = ' ...', $break = false, $middle = false) {
        $length -= min($length, strlen($etc));
        if (!$break && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
        }
        if (!$middle) {
            return substr($string, 0, $length) . $etc;
        }

        return substr($string, 0, $length / 2) . $etc . substr($string, - $length / 2);
    }

    public static function toList($list, $and = 'e', $separator = ', ') {
        if (count($list) > 1) {
            return implode($separator, array_slice($list, null, -1)) . ' ' . $and . ' ' . array_pop($list);
        }

        return array_pop($list);
    }

}