<?php
    
namespace Mithos\Account;

use Mithos\Network\Session;
use Mithos\Database\DriverManager;

class Auth {
 
    private static $instance = null;
    private $account;

    public static function getInstance() {
        if (static::$instance === null) {
            static::$instance = new self();
        }
        return static::$instance;
    }

   public static function getAccount() {
       $instance = static::getInstance();
       if (self::loggedIn()) {
           if ($instance->account === null) {
               $instance->account = new Account(Session::read('Site.username'));
           }
           return $instance->account;
       }
   }

   public static function loggedIn() {
       return Session::check('Site.username');
   }

   public static function login($username, $password) {
       if (!static::check($username, $password)) {
           return false;
       } else {
           Session::write('Site.username', $username);
           return true;
       }
       return false;
   }

   public static function logout() {
       Session::delete('Site.username');
   }

   private static function check($username, $password) {
       $total = DriverManager::getConnection()->fetchColumn('SELECT COUNT(1) AS total
           FROM MEMB_INFO WHERE memb___id = :username AND
           memb__pwd = :password
       ', ['username' => $username, 'password' => $password]);
       return $total == 1;
   }   
}