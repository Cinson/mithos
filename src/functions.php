<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!function_exists('h')) {
    function h($text, $double = true, $charset = null) {
        if (is_string($text)) {
            //optimize for strings
        } elseif (is_array($text)) {
            $texts = [];
            foreach ($text as $k => $t) {
                $texts[$k] = h($t, $double, $charset);
            }
            return $texts;
        } elseif (is_object($text)) {
            if (method_exists($text, '__toString')) {
                $text = (string)$text;
            } else {
                $text = '(object)' . get_class($text);
            }
        } elseif (is_bool($text)) {
            return $text;
        }

        static $defaultCharset = false;
        if ($defaultCharset === false) {
            $defaultCharset = mb_internal_encoding();
            if ($defaultCharset === null) {
                $defaultCharset = 'UTF-8';
            }
        }
        if (is_string($double)) {
            $charset = $double;
        }
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, ($charset) ? $charset : $defaultCharset, $double);
    }

}

if (!function_exists('pr')) {
    function pr($var) {
        if (config('debug')) {
            printf('<pre class="pr">%s</pre>', trim(print_r($var, true)));
        }
    }

}

if (!function_exists('pj')) {
    function pj($var) {
        if (!config('debug')) {
            return;
        }
        printf('<pre class="pj">%s</pre>', trim(json_encode($var, JSON_PRETTY_PRINT)));
    }
}

if (!function_exists('env')) {
    function env($key) {
        if ($key === 'HTTPS') {
            if (isset($_SERVER['HTTPS'])) {
                return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            }
            return (strpos(env('SCRIPT_URI'), 'https://') === 0);
        }

        if ($key === 'SCRIPT_NAME') {
            if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
                $key = 'SCRIPT_URL';
            }
        }

        $val = null;
        if (isset($_SERVER[$key])) {
            $val = $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            $val = $_ENV[$key];
        } elseif (getenv($key) !== false) {
            $val = getenv($key);
        }

        if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
            $addr = env('HTTP_PC_REMOTE_ADDR');
            if ($addr !== null) {
                $val = $addr;
            }
        }

        if ($val !== null) {
            return $val;
        }

        switch ($key) {
            case 'DOCUMENT_ROOT':
                $name = env('SCRIPT_NAME');
                $filename = env('SCRIPT_FILENAME');
                $offset = 0;
                if (!strpos($name, '.php')) {
                    $offset = 4;
                }
                return substr($filename, 0, -(strlen($name) + $offset));
            case 'PHP_SELF':
                return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
            case 'CGI_MODE':
                return (PHP_SAPI === 'cgi');
        }
        return null;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null) {
        return Mithos\Core\Config::get($key, $default);
    }
}

if (!function_exists('__')) {
    function __($string) {
        // $args = func_get_args();
        // $string = $args[0];
        // array_shift($args);
        // return printf($string, $args);
        return $string;
    }
}

if (!function_exists('notify')) {
    function notify($name, $args = null) {
        Mithos\Core\Plugin::notify($name, $args);
    }
}

if (!function_exists('logged_in')) {
    function logged_in() {
        return Mithos\Account\Auth::loggedIn();
    }
}

if (!function_exists('user')) {
    function user() {
        return Mithos\Account\Auth::getAccount();
    }
}

if (!function_exists('require_auth')) {
    function require_auth() {
        return Mithos\Slim\Application::requireAuth();
    }
}

if (!function_exists('require_service')) {
    function require_service($service) {
        $instance = Mithos\Slim\Application::getInstance();
        return function () use ($instance, $service) {
            if (user() !== null) {
                $services = user()->getAvaliableServices();
                if (!isset($services[$service])) {
                    $instance->redirect('/');
                }
            } else {
                $instance->redirect('/');
            }
        };
    }
}

if (!function_exists('util')) {
    function util($class) {
        if (strpos($class, '.')) {
            list($plugin, $class) = explode('.', $class);
            $class = $plugin . '\\Util\\' . $class;
        } else {
            $class = 'Mithos\\Util\\' . $class;
        }
        if (class_exists($class)) {
            return new $class;
        }
        return null;
    }
}