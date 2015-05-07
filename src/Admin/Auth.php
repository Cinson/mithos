<?php
    
namespace Mithos\Admin;

use Mithos\Network\Session;
use Mithos\Database\DriverManager;

class Auth {
 
    private static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public static function getUser($field = null) {
        $user = Session::read('Admin.user');
        if ($field === null) {
            return $user;
        } else {
            return $user[$field];
        }
    }

    public static function loggedIn() {
        return Session::check('Admin.user');
    }

    public static function login($username, $password) {
        if (!self::check($username, $password)) {
            return false;
        } else {
            $user = DriverManager::getConnection()->fetchAssoc('SELECT
                u.*,
                g.name AS group_name,
                g.access 
                FROM mw_users u 
                JOIN mw_user_groups g ON (g.id = u.group_id) 
                WHERE u.username = :username
            ', ['username' => $username]);
            Session::write('Admin.user', $user);
            return true;
        }
        return false;
    }

    public static function logout() {
        Session::delete('Admin.user');
    }

    private static function check($username, $password) {
        $result = DriverManager::getConnection()->fetchAssoc('SELECT COUNT(1) AS total
            FROM mw_users WHERE username = :username AND
            password = :password
       ', ['username' => $username, 'password' => md5($password)]);
       return $result['total'] == 1;
    }   
}