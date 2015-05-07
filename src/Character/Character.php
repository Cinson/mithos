<?php

namespace Mithos\Character;

use Mithos\Database\DriverManager;
use Mithos\Core\Config;
use Mithos\Network\Socket;
//use Mithos\Version;

class Character {

    private $_name;
    private $_username;
    private $_klass;
    private $_level;
    private $_experience;
    private $_points;
    private $_strength;
    private $_agility;
    private $_energy;
    private $_vitality;
    private $_command;
    private $_code;
    private $_money;
    private $_map;
    private $_positionX;
    private $_positionY;
    private $_guild;
    private $_inventory;
    private $_connected;

    public function __construct($name = null, $account = null) {
        if ($name !== null) {
            $this->read($name, $account);
        }
    }

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function setUsername($username) {
        $this->_username = $username;
        return $this;
    }
    
    public function getUsername() {
        return $this->_username;
    }

    public function setClass($class) {
        $this->_klass = $class;
        return $this;
    }

    public function getClass() {
        return $this->_klass;
    }

    public function setLevel($level) {
        $this->_level = $level;
        return $this;
    }
    
    public function getLevel() {
        return $this->_level;
    }

    public function setExperience($experience) {
        $this->_experience = $experience;
        return $this;
    }
    
    public function getExperience() {
        return $this->_experience;
    }

    public function setPoints($points) {
        $this->_points = $points;
        return $this;
    }

    public function setStrength($strength) {
        $this->_strength = $strength;
        return $this;
    }
    
    public function getStrength() {
        return $this->_strength;
    }

    public function setAgility($agility) {
        $this->_agility = $agility;
        return $this;
    }
    
    public function getAgility() {
        return $this->_agility;
    }
    
    public function setEnergy($energy) {
        $this->_energy = $energy;
        return $this;
    }
    
    public function getEnergy() {
        return $this->_energy;
    }

    public function setVitality($vitality) {
        $this->_vitality = $vitality;
        return $this;
    }

    public function getVitality() {
        return $this->_vitality;
    }

    public function setCommand($command) {
        $this->_command = $command;
        return $this;
    }

    public function getCommand() {
        return $this->_command;
    }
    
    public function setCode($code) {
        $this->_code = $code;
        return $this;
    }
    
    public function getCode() {
        return $this->_code;
    }

    public function setMoney($money) {
        $this->_money = $money;
        return $this;
    }

    public function getMoney() {
        return $this->_money;
    }

    public function setMap($map) {
        $this->_map = $map;
        return $this;
    }
    
    public function getMap() {
        return $this->_map;
    }

    public function setPositionX($x) {
        $this->_positionX = $x;
        return $this;
    }
    
    public function getPositionX() {
        return $this->_positionX;
    }
       
    public function setPositionY($y) {
        $this->_positionY = $y;
        return $this;
    }
    
    public function getPositionY() {
        return $this->_positionY;
    }
   
    public function setConnected($connected) {
        $this->_connected = $connected;
        return $this;
    }
    
    public function isConnected() {
        return $this->_connected;
    }
   
    public function exists() {
        return $this->getName() !== null;
    }
   
    public function count($where = null) {
        $where = $where !== null ? ' WHERE ' . $where : '';
        $total = DriverManager::getConnection()->fetchColumn('SELECT
            COUNT(1) AS total FROM Character
        ' . $where);
        return $total;
    }

    public function rename($new) {
        if (!$this->exists()) {
            throw new Exception(__('Character not found'));
        } else {
            $char = new self($new);
            if ($char->exists()) {
                throw new Exception('dds');
            } else {
                $idc = Mssql::fetch('SELECT * FROM AccountCharacter WHERE Id = :account[string]', array(
                    'account' => $this->getUsername()
                ));
            }
        }
    }

    public function update() {
        if ($this->exists()) {

            DriverManager::getConnection()->transactionl(function () {
                $columns = [
                    'Name' => $this->getName(),
                    'cLevel' => $this->getLevel(),
                    'Experience' => $this->getExperience(),
                    'Class' => $this->getClass(),
//                    'LevelUpPoint' => $this->get,
                    'MapNumber' => $this->getMap(),
                    'MapPosY' => $this->getPositionY(),
                    'MapPosX' => $this->getPositionX(),
                    'CtlCode' => $this->getCode(),
                    'Strength' => $this->getStrength(),
                    'Dexterity' => $this->getAgility(),
                    'Vitality' => $this->getAgility(),
                    'Energy' => $this->getEnergy(),
                    'Money' => $this->getMoney()

                ];


                DriverManager::getConnection()->update('Character', $columns);
            });


            if ($this->data['name'] !== $this->getName()) {
//                $this->rename();
            }


        }
    }
    
    public function toArray() {
        return $this->data;
    }
    
    public function read($name, $account = null) {
        $where = '';
        if ($account !== null) {
            $where = ' AND c.AccountID = :account';
        }
        $result = DriverManager::getConnection()->fetchAssoc('SELECT
            s.ConnectStat AS connectStat,
            s.ConnectTM AS lastConnection,
            s.ServerName AS server,
            ac.GameIDC AS gameIdc,
            c.Name AS name,
            c.cLevel AS level,
            c.Experience AS experience,
            CONVERT(TEXT, CONVERT(VARCHAR(760), c.Inventory)) AS inventory,
            c.Class AS class,
            c.PkLevel AS pkLevel,
            c.PkCount AS pkCount,
            c.AccountID AS username,
            c.LevelUpPoint AS points,
            c.MapNumber AS map,
            c.MapPosY AS positionY,
            c.MapPosX AS positionX,
            c.CtlCode AS code,
            c.Strength AS strength,
            c.Dexterity AS agility,
            c.Vitality AS vitality,
            c.Energy AS energy,
            c.Money AS money,
            gm.G_Name AS guild,
            CASE WHEN s.ConnectStat > 0 and ac.GameIDC = c.Name THEN 1 ELSE 0 END as connected
            FROM Character c
            LEFT JOIN AccountCharacter ac ON c.AccountID = ac.ID COLLATE DATABASE_DEFAULT
            LEFT JOIN MEMB_STAT s ON c.AccountID = s.memb___id COLLATE DATABASE_DEFAULT
            LEFT JOIN GuildMember gm ON c.Name = gm.Name COLLATE DATABASE_DEFAULT
            WHERE c.Name = :name
        ' . $where, ['name' => $name, 'account' => $account]);
        if (!empty($result)) {
            $this->data = $result;
            foreach ($result as $key => $value) {
               $method = 'set' . ucfirst($key);
               if (method_exists($this, $method)) {
                   $this->{$method}($value);
               }
           }
        }
    }

    public function disconnect() {
        $socket = new Socket();
        $socket->connect();

        $socket->send("\xC1\x13\xA0\x00\x00\x00\x00\x00" . str_pad($this->getUsername(), 10, "\x00") . "\x00");

        $read = $socket->read();
        $read = hexdec(substr($read, 16, 2));
        if ($tmpResponse == 1) {
            for ($i = 0; $i < 3; $i++) {
                sleep(1);
                $socket->send("\xC1\x13\xA0\x00\x00\x00\x00\x00" . str_pad($this->getUsername(), 10, "\x00") . "\x00");
            }

            return true;
        }
        return false;
    }
}