<?php

namespace Mithos\Session;

class Session {

    protected static $options = array(
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true
    );

    public static function start() {
        if (self::started()) {
            return true;
        }

        if (!isset($_SESSION)) {
            session_cache_limiter('nocache');
            self::setCookieParams();
        }

        return session_start();
    }

    public static function started() {
        return (boolean) self::id();
    }

    public static function read($key) {
        if (!self::started() && !self::start()) {
            throw new \RuntimeException('could not start session');
        }

        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        
        return null;
    }

    public static function check($key) {
        if (!self::started() && !self::start()) {
            throw new \RuntimeException('could not start session');
        }
        
        return self::read($key) !== null;
    }

    public static function write($key, $value) {
        if (!self::started() && !self::start()) {
            throw new \RuntimeException('could not start session');
        }

        $_SESSION[$key] = $value;
    }

    public static function delete($name) {
        if (!self::started() && !self::start()) {
            throw new \RuntimeException('could not start session');
        }

        unset($_SESSION[$name]);
    }

    public static function destroy() {
        if (!self::started() && !self::start()) {
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
        if (!self::started() && !self::start()) {
            throw new \RuntimeException('could not start session');
        }

        self::setCookieParams();
        session_regenerate_id();
    }

    public static function option($option, $value) {
        self::$options[$option] = $value;
    }

    protected static function setCookieParams() {
        session_set_cookie_params(
            self::$options['lifetime'],
            self::$options['path'],
            self::$options['domain'],
            self::$options['secure'],
            self::$options['httponly']
        );
    }
}