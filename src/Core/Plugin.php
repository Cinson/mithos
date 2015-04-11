<?php

namespace Mithos\Core;

use Mithos\Slim\Application;

class Plugin {

    private $plugins = array();
    private static $instance;

    public function __construct() {
        $this->plugins = Config::get('plugins', []);
    }

    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new self();
        }
        return static::$instance;
    }
    
    public function getPlugins() {
        return $this->plugins;
    }
    
    public function getPluginsInfo() {
        $plugins = array();
        foreach (new \DirectoryIterator(PLUGINS_PATH) as $file) {
            if ($file->isDir() && !in_array($file->getFilename(), ['.', '..'])) {
                $temp = array();
                if (file_exists($file->getPathname() . DS . 'info.php')) {
                    $temp = require $file->getPathname() . DS . 'info.php';
                } else {
                    $temp = array(
                        'name' => $file->getFilename(),
                        'author' => '',
                        'description' => '',
                        'website' => '',
                        'version' => ''
                    );
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
            if (is_dir(PLUGINS_PATH . $plugin)) {
                $controllers = PLUGINS_PATH . $plugin . DS . 'src' . DS . ($app->inAdmin() ? 'admin' . DS : '') . 'controllers' . DS . '*.php';
                foreach (glob($controllers) as $controller) {
                    require_once $controller;
                }
            }
        }
    }
    
//    public static function loadStylesheets() {
//        $instance = self::getInstance();
//        $app = Application::getInstance();
//        $out = '';
//        $type = in_admin() ? 'admin' : 'site';
//        foreach ($instance->getPlugins() as $plugin) {
//            if (is_dir(PLUGINS_PATH . $plugin) && is_dir(PLUGINS_PATH . $plugin . DS . 'css' . DS . $type . DS)) {
//                foreach (new \DirectoryIterator(PLUGINS_PATH . $plugin . DS . 'css' . DS . $type . DS) as $file) {
//                    if ($file->getExtension() == 'css') {
//                        $out .= '<link rel="stylesheet" href="' . dirname($_SERVER['PHP_SELF']) . '/plugins/' . $plugin . '/css/' . $type . '/' . $file->getFilename() . '" />';
//                    }
//                }
//            }
//        }
//        return $out;
//    }
    
//    public static function loadJavascripts() {
//        $instance = self::getInstance();
//        $app = Application::getInstance();
//        $out = '';
//        $type = in_admin() ? 'admin' : 'site';
//        foreach ($instance->getPlugins() as $plugin) {
//            if (is_dir(PLUGINS_PATH . $plugin) && is_dir(PLUGINS_PATH . $plugin . DS . 'js' . DS . $type . DS)) {
//                foreach (new \DirectoryIterator(PLUGINS_PATH . $plugin . DS . 'js' . DS . $type . DS) as $file) {
//                    if ($file->getExtension() == 'js') {
//                        $out .= '<script type="text/javascript" src="' . dirname($_SERVER['PHP_SELF']) . '/plugins/' . $plugin . '/js/' . $type . '/' . $file->getFilename() . '"></script>';
//                    }
//                }
//            }
//        }
//        return $out;
//    }
    
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
    
//    public static function autoload($className) {
//        $className = ltrim($className, '\\');
//        $fileName  = PLUGINS_PATH;
//
//        $parts = explode('\\', $className);
//        $plugin = $parts[0];
//
//        $fileName = $fileName . $plugin . DS . 'libs' . DS . join(DS, array_slice($parts, 1)) . '.php';
//
//        if (file_exists($fileName)) {
//            require $fileName;
//        }
//    }
//
//    public static function registerAutoloader() {
//        spl_autoload_register(__NAMESPACE__ . '\\Plugin::autoload');
//    }
}