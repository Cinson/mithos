<?php

namespace Mithos\Util;

class Hash {

    public static function get(array $data, $path) {
        if (empty($data)) {
            return null;
        }
        if (is_string($path) || is_numeric($path)) {
            $parts = explode('.', $path);
        } else {
            $parts = $path;
        }
        foreach ($parts as $key) {
            if (is_array($data) && isset($data[$key])) {
                $data = &$data[$key];
            } else {
                return null;
            }
        }
        return $data;
    }

    public static function expand($data, $separator = '.') {
        $result = array();
        foreach ($data as $flat => $value) {
            $keys = array_reverse(explode($separator, $flat));
            $child = array(
                $keys[0] => $value
            );
            array_shift($keys);
            foreach ($keys as $k) {
                $child = array(
                    $k => $child
                );
            }
            $result = self::merge($result, $child);
        }
        return $result;
    }

    public static function merge(array $data, $merge) {
        $args = func_get_args();
        $return = current($args);

        while (($arg = next($args)) !== false) {
            foreach ((array) $arg as $key => $val) {
                if (!empty($return[$key]) && is_array($return[$key]) && is_array($val)) {
                    $return[$key] = self::merge($return[$key], $val);
                } elseif (is_int($key) && isset($return[$key])) {
                    $return[] = $val;
                } else {
                    $return[$key] = $val;
                }
            }
        }
        return $return;
    }
}