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

    public static function extract(array $data, $path) {
        if (empty($path)) {
            return $data;
        }

        // Simple paths.
        if (!preg_match('/[{\[]/', $path)) {
            return (array)self::get($data, $path);
        }

        if (strpos($path, '[') === false) {
            $tokens = explode('.', $path);
        } else {
            $tokens = String::tokenize($path, '.', '[', ']');
        }

        $_key = '__set_item__';

        $context = array($_key => array($data));

        foreach ($tokens as $token) {
            $next = array();

            $conditions = false;
            $position = strpos($token, '[');
            if ($position !== false) {
                $conditions = substr($token, $position);
                $token = substr($token, 0, $position);
            }

            foreach ($context[$_key] as $item) {
                foreach ((array)$item as $k => $v) {
                    if (self::_matchToken($k, $token)) {
                        $next[] = $v;
                    }
                }
            }

            // Filter for attributes.
            if ($conditions) {
                $filter = array();
                foreach ($next as $item) {
                    if (is_array($item) && self::_matches($item, $conditions)) {
                        $filter[] = $item;
                    }
                }
                $next = $filter;
            }
            $context = array($_key => $next);

        }
        return $context[$_key];
    }

    protected static function _matches(array $data, $selector) {
        preg_match_all(
            '/(\[ (?P<attr>[^=><!]+?) (\s* (?P<op>[><!]?[=]|[><]) \s* (?P<val>(?:\/.*?\/ | [^\]]+)) )? \])/x',
            $selector,
            $conditions,
            PREG_SET_ORDER
        );

        foreach ($conditions as $cond) {
            $attr = $cond['attr'];
            $op = isset($cond['op']) ? $cond['op'] : null;
            $val = isset($cond['val']) ? $cond['val'] : null;

            // Presence test.
            if (empty($op) && empty($val) && !isset($data[$attr])) {
                return false;
            }

            // Empty attribute = fail.
            if (!(isset($data[$attr]) || array_key_exists($attr, $data))) {
                return false;
            }

            $prop = isset($data[$attr]) ? $data[$attr] : null;

            // Pattern matches and other operators.
            if ($op === '=' && $val && $val[0] === '/') {
                if (!preg_match($val, $prop)) {
                    return false;
                }
            } elseif (
                ($op === '=' && $prop != $val) ||
                ($op === '!=' && $prop == $val) ||
                ($op === '>' && $prop <= $val) ||
                ($op === '<' && $prop >= $val) ||
                ($op === '>=' && $prop < $val) ||
                ($op === '<=' && $prop > $val)
            ) {
                return false;
            }

        }
        return true;
    }

    public static function nest(array $data, $options = array()) {
        if (!$data) {
            return $data;
        }

        $alias = key(current($data));
        $options += array(
            'idPath' => "{s}.id",
            'parentPath' => "{s}.parent_id",
            'children' => 'children',
            'root' => null
        );

        $return = $idMap = array();
        $ids = self::extract($data, $options['idPath']);

        $idKeys = explode('.', $options['idPath']);
        array_shift($idKeys);

        $parentKeys = explode('.', $options['parentPath']);
        array_shift($parentKeys);

        foreach ($data as $result) {
            $result[$options['children']] = array();

            $id = self::get($result, $idKeys);
            $parentId = self::get($result, $parentKeys);

            if (isset($idMap[$id][$options['children']])) {
                $idMap[$id] = array_merge($result, (array)$idMap[$id]);
            } else {
                $idMap[$id] = array_merge($result, array($options['children'] => array()));
            }
            if (!$parentId || !in_array($parentId, $ids)) {
                $return[] =& $idMap[$id];
            } else {
                $idMap[$parentId][$options['children']][] =& $idMap[$id];
            }
        }

        if ($options['root']) {
            $root = $options['root'];
        } else {
            $root = self::get($return[0], $parentKeys);
        }

        foreach ($return as $i => $result) {
            $id = self::get($result, $idKeys);
            $parentId = self::get($result, $parentKeys);
            if ($id !== $root && $parentId != $root) {
                unset($return[$i]);
            }
        }
        return array_values($return);
    }

    public static function sort(array $data, $path, $dir, $type = 'regular') {
        if (empty($data)) {
            return array();
        }
        $originalKeys = array_keys($data);
        $numeric = is_numeric(implode('', $originalKeys));
        if ($numeric) {
            $data = array_values($data);
        }
        $sortValues = self::extract($data, $path);
        // pr($data);
        $sortCount = count($sortValues);
        $dataCount = count($data);

        // Make sortValues match the data length, as some keys could be missing
        // the sorted value path.
        if ($sortCount < $dataCount) {
            $sortValues = array_pad($sortValues, $dataCount, null);
        }
        $result = self::_squash($sortValues);
        $keys = self::extract($result, '{n}.id');
        $values = self::extract($result, '{n}.value');

        $dir = strtolower($dir);
        $type = strtolower($type);
        if ($type === 'natural' && version_compare(PHP_VERSION, '5.4.0', '<')) {
            $type = 'regular';
        }
        if ($dir === 'asc') {
            $dir = SORT_ASC;
        } else {
            $dir = SORT_DESC;
        }
        if ($type === 'numeric') {
            $type = SORT_NUMERIC;
        } elseif ($type === 'string') {
            $type = SORT_STRING;
        } elseif ($type === 'natural') {
            $type = SORT_NATURAL;
        } else {
            $type = SORT_REGULAR;
        }
        array_multisort($values, $dir, $type, $keys, $dir, $type);
        $sorted = array();
        $keys = array_unique($keys);

        foreach ($keys as $k) {
            if ($numeric) {
                $sorted[] = $data[$k];
                continue;
            }
            if (isset($originalKeys[$k])) {
                $sorted[$originalKeys[$k]] = $data[$originalKeys[$k]];
            } else {
                $sorted[$k] = $data[$k];
            }
        }
        return $sorted;
    }


    protected static function _squash($data, $key = null) {
        $stack = array();
        foreach ($data as $k => $r) {
            $id = $k;
            if ($key !== null) {
                $id = $key;
            }
            if (is_array($r) && !empty($r)) {
                $stack = array_merge($stack, self::_squash($r, $id));
            } else {
                $stack[] = array('id' => $id, 'value' => $r);
            }
        }
        return $stack;
    }

    protected static function _matchToken($key, $token) {
        if ($token === '{n}') {
            return is_numeric($key);
        }
        if ($token === '{s}') {
            return is_string($key);
        }
        if (is_numeric($token)) {
            return ($key == $token);
        }
        return ($key === $token);
    }
}