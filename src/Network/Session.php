<?php

namespace Mithos\Network;

class Session {

    private static $_options = [
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true
    ];

    public static function start() {
        if (static::started()) {
            return true;
        }

        if (!isset($_SESSION)) {
            session_cache_limiter(false);
            static::setCookieParams();
        }

        return session_start();
    }

    public static function started() {
        return (boolean) static::id();
    }

    public static function read($key) {
        if (!static::started() && !static::start()) {
            throw new \RuntimeException('could not start session');
        }

        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return null;
    }

    public static function check($key) {
        if (!static::started() && !static::start()) {
            throw new \RuntimeException('could not start session');
        }

        return self::read($key) !== null;
    }

    public static function write($key, $value) {
        if (!static::started() && !static::start()) {
            throw new \RuntimeException('could not start session');
        }

        $_SESSION[$key] = $value;
    }

    public static function delete($name) {
        if (!static::started() && !static::start()) {
            throw new \RuntimeException('could not start session');
        }

        unset($_SESSION[$name]);
    }

    public static function destroy() {
        if (!static::started() && !static::start()) {
            throw new \RuntimeException('could not start session');
        }

        session_destroy();
    }

    public static function id() {
        $id = session_id();

        if (!empty($id)) {
            return $id;
        }
    }

    public static function regenerate() {
        if (!static::started() && !static::start()) {
            throw new \RuntimeException('could not start session');
        }

        static::setCookieParams();
        session_regenerate_id();
    }

    public static function option($option, $value) {
        static::$_options[$option] = $value;
    }

    protected static function setCookieParams() {
        session_set_cookie_params(
            static::$_options['lifetime'],
            static::$_options['path'],
            static::$_options['domain'],
            static::$_options['secure'],
            static::$_options['httponly']
        );
    }
}