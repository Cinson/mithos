<?php

namespace Mithos\Core;

use Mithos\Slim\Application;

class Plugin {

    private $_plugins = array();
    private static $instance;

    public function __construct() {
        $this->_plugins = Config::get('plugins', []);
    }

    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    public function getPlugins() {
        return $this->_plugins;
    }

    public function getPluginsInfo() {
        $plugins = [];
        foreach (new \DirectoryIterator(Config::get('path.plugins')) as $file) {
            if ($file->isDir() && !in_array($file->getFilename(), ['.', '..'])) {
                $temp = [];
                if (file_exists($file->getPathname() . DS . 'info.php')) {
                    $temp = require $file->getPathname() . DS . 'info.php';
                } else {
                    $temp = [
                        'name' => $file->getFilename(),
                        'author' => '',
                        'description' => '',
                        'website' => '',
                        'version' => ''
                    ];
                }
                $temp['active'] = in_array($temp['name'], (array) Config::get('plugins', []));
                $plugins[] = $temp;
            }
        }
        return $plugins;
    }
    
    public static function loadAll() {
        $instance = self::getInstance();
        $app = Application::getInstance();
        foreach ($instance->getPlugins() as $plugin) {
            if (is_dir(Config::get('path.plugins') . $plugin)) {
                $controllers = Config::get('path.plugins') . $plugin . DS . 'src' . DS . ($app->inAdmin() ? 'admin' . DS : '') . 'controllers' . DS . '*.php';
                foreach (glob($controllers) as $controller) {
                    require_once $controller;
                }
            }
        }
    }

    public static function isActive($plugin) {
        $instance = self::getInstance();
        $plugins = $instance->getPlugins();
        return in_array($plugin, $plugins);
    }
    
    public static function activate($plugin) {
        $instance = self::getInstance();
        $plugins = $instance->getPlugins();
        $plugins[] = $plugin;
        Config::save('plugins', $plugins);
    }
    
    public static function deactivate($plugin) {
        $instance = self::getInstance();
        $plugins = $instance->getPlugins();
        $plugins = array_diff($plugins, [$plugin]);
        Config::save('plugins', $plugins);
    }
    
    public static function notify($name, $args = null) {
        Application::getInstance()->applyHook($name, $args);
    }
    
    public static function autoload($loader) {
        $instance = static::getInstance();
        foreach ($instance->getPlugins() as $plugin) {
            $loader->addPsr4($plugin . '\\', Config::get('path.plugins') . $plugin . DS . 'src' . DS . 'lib', true);
        }
    }

}