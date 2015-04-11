<?php

namespace Mithos\Character;

use Mithos\DB\Mssql;
use Mithos\Core\Config;
//use Mithos\Version;

class Character {

    private $name;
    private $username;
    private $klass;
    private $level;
    private $experience;
    private $points;
    private $strength;
    private $agility;
    private $energy;
    private $vitality;
    private $command;
    private $code;
    private $money;
    private $map;
    private $positionX;
    private $positionY;
    private $guild;
    private $inventory;
    private $connected;

    public function __construct($name = null, $account = null) {
        if ($name !== null) {
            $this->read($name, $account);
        }
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }
    
    public function getUsername() {
        return $this->username;
    }

    public function setClass($class) {
        $this->klass = $class;
        return $this;
    }

    public function getClass() {
        return $this->klass;
    }

    public function setLevel($level) {
        $this->level = $level;
        return $this;
    }
    
    public function getLevel() {
        return $this->level;
    }

    public function setExperience($experience) {
        $this->experience = $experience;
        return $this;
    }
    
    public function getExperience() {
        return $this->experience;
    }

    public function setPoints($points) {
        $this->points = $points;
        return $this;
    }

    public function setStrength($strength) {
        $this->strength = $strength;
        return $this;
    }
    
    public function getStrength() {
        return $this->strength;
    }

    public function setAgility($agility) {
        $this->agility = $agility;
        return $this;
    }
    
    public function getAgility() {
        return $this->agility;
    }
    
    public function setEnergy($energy) {
        $this->energy = $energy;
        return $this;
    }
    
    public function getEnergy() {
        return $this->energy;
    }

    public function setVitality($vitality) {
        $this->vitality = $vitality;
        return $this;
    }

    public function getVitality() {
        return $this->vitality;
    }

    public function setCommand($command) {
        $this->command = $command;
        return $this;
    }

    public function getCommand() {
        return $this->command;
    }
    
    public function setCode($code) {
        $this->code = $code;
        return $this;
    }
    
    public function getCode() {
        return $this->code;
    }

    public function setMoney($money) {
        $this->money = $money;
        return $this;
    }

    public function getMoney() {
        return $this->money;
    }

    public function setMap($map) {
        $this->map = $map;
        return $this;
    }
    
    public function getMap() {
        return $this->map;
    }

    public function setPositionX($x) {
        $this->positionX = $x;
        return $this;
    }
    
    public function getPositionX() {
        return $this->positionX;
    }
       
    public function setPositionY($y) {
        $this->positionY = $y;
        return $this;
    }
    
    public function getPositionY() {
        return $this->positionY;
    }
   
    public function setConnected($connected) {
        $this->connected = $connected;
        return $this;
    }
    
    public function isConnected() {
        return $this->connected;
    }
   
    public function exists() {
        return $this->getName() !== null;
    }
   
    public function count($where = null) {
        $where = $where !== null ? ' WHERE ' . $where : '';
        $result = Mssql::getInstance()->fetch('SELECT 
            COUNT(1) AS total FROM Character
        ' . $where);
        return $result['total'];
    }

    public function getMembersTeam() {
        $results = Mssql::getInstance()->fetchAll('SELECT
            c.Name AS name,
            CASE WHEN s.ConnectStat > 0 and ac.GameIDC = c.Name THEN 1 ELSE 0 END as status
            FROM MEMB_STAT s
            INNER JOIN AccountCharacter ac ON (s.memb___id = ac.ID) 
            INNER JOIN Character c ON (s.memb___id = c.AccountID) 
            WHERE c.CtlCode > 7
        ');
        return $results;
    }
    
    public function getLogged() {
        $result = Mssql::getInstance()->fetchAll('SELECT 
            ac.GameIDC AS character,
            ms.IP AS ip,
            ms.memb___id AS account, 
            ms.ConnectStat AS connect_stat,
            ms.ServerName AS server_name,
            DATEDIFF(SECOND, ms.ConnectTM, GETDATE()) AS connected_time,
            ms.ConnectTM AS connected_at,
            ms.DisConnectTM AS disconnected_at
            FROM MEMB_STAT ms
            JOIN AccountCharacter ac ON ms.memb___id = ac.Id
            JOIN Character c ON (ms.memb___id = c.AccountID)
            WHERE ms.ConnectStat > 0 
            AND ac.GameIDC = c.Name 
            ORDER BY ConnectTM DESC
        ');
        return $result;
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
            Mssql::getInstance()->begin();
            
            Mssql::getInstance()->query('UPDATE MEMB_INFO SET 
                Name = :name[string],
                cLevel = :level[integer],
                Experience = :experience[integer],
                Class = :class[integer],
                LevelUpPoint = :points[integer],
                MapNumber = :map[integer],
                MapPosY = :positionY[integer],
                MapPosX = :positionX[integer],
                CtlCode = :code[integer],
                Strength = :strength[integer],
                Dexterity = :agility[integer],
                Vitality = :vitality[integer],
                Energy = :energy[integer],
                Money AS money[integer],
            ', [
                'name' => $this->getName(),
                'level' => $this->getLevel(),
                'experience' => $this->getExperience(),
                'class' => $this->getClass(),
                'points' => $this->getPoints(),
                'map' => $this->getMap(),
                'positionY' => $this->getPositionX(),
                'positionX' => $this->getPositionY(),
                'code' => $this->getCode(),
                'strength' => $this->getStrength(),
                'agility' => $this->getAgility(),
                'vitality' => $this->getVitality(),
                'energy' => $this->getEnergy(),
                'command' => $this->getCommand(),
                'money' => $this->getMoney(),
            ]);

            if ($this->data['name'] !== $this->getName()) {
//                $this->rename();
            }

            Mssql::getInstance()->commit();
        } else {
            Mssql::getInstance()->rollback();
            throw new Exception('ddd');
        }
    }
    
    public function toArray() {
        return $this->data;
    }
    
    public function read($name, $account = null) {
        $where = '';
        if ($account !== null) {
            $where = ' AND c.AccountID = :account[string]';
        }
        $result = Mssql::getInstance()->fetch('SELECT
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
            WHERE c.Name = :name[string]
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

//    public function disconnect() {
//        $socket = new Socket();
//        $socket->connect();
//
//        $socket->send("\xC1\x13\xA0\x00\x00\x00\x00\x00" . str_pad($this->getUsername(), 10, "\x00") . "\x00");
//
//        $read = $socket->read();
//        $read = hexdec(substr($read, 16, 2));
//        if ($tmpResponse == 1) {
//            for ($i = 0; $i < 3; $i++) {
//                sleep(1);
//                $socket->send("\xC1\x13\xA0\x00\x00\x00\x00\x00" . str_pad($this->getUsername(), 10, "\x00") . "\x00");
//            }
//
//            return true;
//        }
//        return false;
//    }
}