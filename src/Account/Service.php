<?php

namespace Mithos\Account;

use Mithos\Util\Hash;
use Mithos\Database\DriverManager;
use Mithos\Core\Config;

class Service {

    private static $_instance;
    private $_services = [];

    public static function getInstance() {
        if (static::$_instance === null) {
            static::$_instance = new self();
        }
        return static::$_instance;
    }

    public static function getAllServices() {
        $instance = static::getInstance();
        if (empty($instance->_services)) {
            foreach (DriverManager::getConnection()->fetchAll('SELECT * from mw_services order by sequence') as $service) {
                $types = Config::get('vip.types');
                $viptypes = [];
                foreach (DriverManager::getConnection()->fetchAll('SELECT * from mw_viptype_services where service_id = :service_id', ['service_id' => $service['id']]) as $viptype) {
                    $viptypes[] = $types[$viptype['viptype']];
                }
                $instance->_services[$service['service']] = array_merge($service, ['viptypes' => $viptypes]);
            }
        }
        return $instance->_services;
    }

    public static function getService($service) {
        return static::getAllServices()[$service];
    }

    public static function deactive($service) {
        DriverManager::getConnection()->update('mw_services', [
            'active' => false
        ], [
            'service' => $service
        ], [
            'boolean'
        ]);
    }

    public static function active($service) {
        DriverManager::getConnection()->update('mw_services', [
            'active' => true
        ], [
            'service' => $service
        ], [
            'boolean'
        ]);
    }

}