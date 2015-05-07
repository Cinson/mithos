<?php
    
namespace Mithos\Account;

use Mithos\Network\Session;
use Mithos\Database\DriverManager;

class Auth {
 
    private static $instance = null;
    private $account;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

   public static function getAccount() {
       $instance = self::getInstance();
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
       if (!self::check($username, $password)) {
           return false;
       } else {
           Session::write('Site.username', $username);
           return true;
       }
       return false;
   }

   public static function logout() {
       Session::delete('Site.username');
       Session::destroy();
   }

   private static function check($username, $password) {
       $total = DriverManager::getConnection()->fetchColumn('SELECT COUNT(1) AS total
           FROM MEMB_INFO WHERE memb___id = :username AND
           memb__pwd = :password
       ', ['username' => $username, 'password' => $password]);
       return $total == 1;
   }   
}