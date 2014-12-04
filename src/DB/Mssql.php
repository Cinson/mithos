<?php

namespace Mithos\DB;

class Mssql {

   const FETCH_ASSOC = 1;
   const FETCH_NUM = 2;
   const FETCH_BOTH = 3;
   const FETCH_OBJ = 4;

   protected $results;
   private static $instance = null;

   public static function getInstance() {
       if (static::$instance === null) {
           static::$instance = new self();
       }
       return static::$instance;
   }

   public function query($sql = null, $params = array()) {
       if (!Connection::isConnected()) {
           throw new Mssql\Exception('Can not find a Connection to MSSQL');
       }

       if (!$result = @mssql_query($this->prepare($sql, $params), Connection::getConnection())) {
           throw new Mssql\Exception(mssql_get_last_message() . ' SQL: ' . $this->prepare($sql, $params));
       }

       return $this->results = $result;
   }

   public function begin() {
       $this->query('BEGIN TRANSACTION');
   }

   public function commit() {
       $this->query('COMMIT');
   }

   public function rollback() {
       $this->query('ROLLBACK');
   }

   public function fetch($sql = null, $params = array(), $type = self::FETCH_ASSOC) {
       $results = $this->query($sql, $params);
       return $this->fetchRow($results, $type);
   }

   public function fetchAll($sql = null, $params = array(), $type = self::FETCH_ASSOC) {
       $results = $this->query($sql, $params);
       $rows = array();
       while ($result = $this->fetchRow($results, $type)) {
           $rows[] = $result;
       }
       return $rows;
   }

   public function fetchRow($results = null, $type = self::FETCH_ASSOC) {
       switch ($type) {
           case self::FETCH_ASSOC:
               $mode = 'mssql_fetch_assoc';
               break;
           case self::FETCH_NUM:
               $mode = 'mssql_fetch_row';
               break;
           case self::FETCH_BOTH:
               $mode = 'mssql_fetch_array';
               break;
           default:
               $mode = 'mssql_fetch_object';
       }
       return $mode($results);
   }

   public function lastInsertId() {
       $result = $this->fetch('SELECT SCOPE_IDENTITY() AS insert_id');
       return $result['insert_id'];
   }

   public function value($data, $type = null) {
       if ($data === null) {
           return 'NULL';
       }
       if (in_array($type, array('integer', 'float', 'binary')) && $data === '') {
           return 'NULL';
       }
       if ($data === '') {
           return "''";
       }
       switch ($type) {
           case 'boolean':
               $data = $this->boolean((bool) $data);
               break;
           default:
               if (get_magic_quotes_gpc()) {
                   $data = stripslashes(str_replace("'", "''", $data));
               } else {
                   $data = str_replace("'", "''", $data);
               }
               break;
       }
       if (in_array($type, array('integer', 'float', 'binary')) && is_numeric($data)) {
           return $data;
       }
       return "'" . $data . "'";
   }

   public function prepare($sql = null, $params = array()) {
       if (!empty($params)) {
           preg_match_all('/:(?P<name>\w+)\[(?P<type>\w+)\]/i', $sql, $match, PREG_SET_ORDER);
           $replace = array();
           foreach ($match as $key => $value) {
               $replace[$value[0]] = $this->value($params[$value['name']], $value['type']);
           }
           $sql = str_replace(array_keys($replace), array_values($replace), $sql);
       }
       return $sql;
   }

   private function boolean($data) {
       if ($data === true || $data === false) {
           if ($data === true) {
               return 1;
           }
           return 0;
       } else {
           return !empty($data);
       }
   }
}