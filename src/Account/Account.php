<?php

namespace Mithos\Account;

use Mithos\Database\DriverManager;
use Mithos\Character\Character;
use Mithos\Core\Config;

class Account {

    private $_name;
    private $_username;
    private $_password;
    private $_email;
    private $_confirmedEmail;
    private $_phone;
    private $_secretQuestion;
    private $_secretAnswer;
    private $_blocked;
    private $_personalId;
    private $_binWarehouse;
    private $_connected;
    private $_characters;
    private $_credit;
    private $_vipType;
    private $_vipExpire;
    private $_coins = [];
    private $_data = [];
    private $_services = [];

    public function __construct($username = null) {
        if ($username !== null) {
            $this->read($username);
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
        $this->_username = strtolower($username);
        return $this;
    }

    public function getUsername() {
        return $this->_username;
    }

    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }

    public function getPassword() {
        return $this->_password;
    }

    public function setEmail($email) {
        $this->_email = $email;
        return $this;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function setConfirmedEmail($confirmedEmail) {
        $this->_confirmedEmail = (bool) $confirmedEmail;
        return $this;
    }

    public function isConfirmedEmail() {
        return $this->_confirmedEmail;
    }

    public function setPhone($phone) {
        $this->_phone = $phone;
        return $this;
    }

    public function getPhone() {
        return $this->_phone;
    }

    public function setSecretQuestion($question) {
        $this->_secretQuestion = $question;
        return $this;
    }

    public function getSecretQuestion() {
        return $this->_secretQuestion;
    }

    public function setSecretAnswer($answer) {
        $this->_secretAnswer = $answer;
        return $this;
    }

    public function getSecretAnswer() {
        return $this->_secretAnswer;
    }

    public function setBlocked($blocked) {
        $this->_blocked = (bool) $blocked;
        return $this;
    }

    public function isBlocked() {
        return $this->_blocked;
    }

    public function setPersonalId($id) {
        $this->_personalId = $id;
        return $this;
    }

    public function getPersonalId() {
        return $this->_personalId;
    }

    public function setCredit($credit) {
        $this->_credit = $credit;
        return $this;
    }

    public function getCredit() {
        return $this->_credit;
    }

    public function setBinWarehouse($warehouse) {
        $this->_binWarehouse = $warehouse;
        return $this;
    }

    public function getBinWarehouse() {
        return $this->_binWarehouse;
    }

    public function setVipType($vipType) {
        $this->_vipType = $vipType;
        return $this;
    }

    public function getVipType() {
        return $this->_vipType;
    }

    public function setVipExpire($vipExpire) {
        if ($vipExpire instanceof \DateTime) {
            $this->_vipExpire = $vipExpire;
        } else {
            $this->_vipExpire = new \DateTime($vipExpire);
        }
        return $this;
    }

    public function getVipExpire() {
        return $this->_vipExpire;
    }

    public function setCoins(array $coins) {
        $this->_coins = $coins;
        return $this;
    }

    public function setCoin($name, $value) {
        $this->_coins[$name] = $value;
        return $this;
    }

    public function addCoin($name, $value) {
        $coin = $this->_coins[$name];
        $this->_coins = $coin + $value;
        return $this;
    }

    public function getCoins() {
        return $this->_coins;
    }

    public function getCoin($coin) {
        return isset($this->_coins[$coin]) ? $this->_coins[$coin] : null;
    }

    public function isConnected() {
        return $this->_connected;
    }

    public function toArray() {
        return $this->_data;
    }

    public function exists() {
        return $this->getUsername() !== null;
    }

    public function getCharacters() {
        if ($this->_characters === null) {
            $this->_characters = array();
            $results = DriverManager::getConnection()->fetchAll('SELECT
                Name AS name FROM Character
                WHERE AccountID = :username
            ', ['username' => $this->getUsername()]);
            foreach ($results as $result) {
                $character = new Character($result['name']);
                $this->_characters[] = $character;
                if (!isset($this->_data['characters'])) {
                    $this->_data['characters'] = [];
                }
                $this->_data['characters'][] = $character->toArray();
            }
        }
        return $this->_characters;
    }

    public function read($username = null) {
        $fields = [
            'mi.memb_guid as guid',
            'mi.memb___id as username',
            'mi.memb__pwd as password',
            'mi.memb_name as name',
            'mi.mail_addr as email',
            'mi.mail_chek as confirmedEmail',
            'mi.tel__numb as phone',
            'mi.' . Config::get('credit.column') . ' as credit',
            'mi.fpas_ques as secretQuestion',
            'mi.fpas_answ as secretAnswer',
            'mi.sno__numb as personalId',
            'mi.bloc_code as blocked',
            'mi.appl_days as createdAt',
            'ms.ServerName as serverName',
            'ms.ConnectTM as connectedAt',
            'ms.DisConnectTM as disconnectedAt',
            'ms.ConnectStat as connected',
            'ms.IP AS ip',
            'CONVERT(VARBINARY(1200), w.Items) as binWarehouse',
            'ac.GameIDC as lastUsedCharacter'
        ];

        foreach (Config::get('coins', []) as $coin) {
            $fields[] = '(select ' . $coin['column'] . ' from ' . $coin['table'] . ' where ' . $coin['foreign_key'] . ' = :username) as ' . $coin['id'];
        }

        if (Config::get('vip.column_type')) {
            $fields[] = 'mi.' . Config::get('vip.column_type') . ' as vipType';
        }

        if (Config::get('vip.column_expire')) {
            $fields[] = 'mi.' . Config::get('vip.column_expire') . ' as vipExpire';
        }

        $sql = 'SELECT ' . join(',', $fields) . '
            FROM MEMB_INFO mi
            LEFT JOIN MEMB_STAT ms ON mi.memb___id = ms.memb___id COLLATE DATABASE_DEFAULT
            LEFT JOIN AccountCharacter ac ON mi.memb___id = ac.Id COLLATE DATABASE_DEFAULT
            LEFT JOIN warehouse w ON mi.memb___id = w.AccountID COLLATE DATABASE_DEFAULT
            WHERE mi.memb___id = :username';

        $result = DriverManager::getConnection()->fetchAssoc($sql, ['username' => $username == null ? $this->getUsername() : $username]);

        if (!empty($result)) {
            $result['coins'] = [];
            foreach (Config::get('coins', []) as $coin) {
                $result['coins'][$coin['id']] = $result[$coin['id']];
                unset($result[$coin['id']]);
            }

            foreach ($result as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                }
            }

            $this->_data = $result;
            $this->getCharacters();
        }
    }

    private function _saveCoins() {
        foreach (Config::get('coins', []) as $coin) {
            if ($this->getCoin($coin['id'])) {
                $total = DriverManager::getConnection()->fetchColumn('SELECT COUNT(1) as total FROM ' . $coin['table'] . ' where ' . $coin['foreign_key'] . ' = :username', ['username' => $this->getUsername()]);
                if ($coin['table'] !== 'MEMB_INFO' && $total == 0) {
                    DriverManager::getConnection()->insert($coin['table'], [
                        $coin['column'] => $this->getCoin($coin['id']),
                        $coin['foreign_key'] => $this->getUsername()
                    ], [
                        'integer', 'string'
                    ]);
                } else {
                    DriverManager::getConnection()->update($coin['table'], [
                        $coin['column'] => $this->getCoin($coin['id'])
                    ], [
                        $coin['foreign_key'] => $this->getUsername()
                    ], [
                        'integer'
                    ]);
                }
            }
        }
    }

    public function insert() {

        DriverManager::getConnection()->transactional(function () {

            DriverManager::getConnection()->exec('SET IDENTITY_INSERT MEMB_INFO ON');

            DriverManager::getConnection()->insert('MEMB_INFO', [
                'memb_guid' => $this->getNextGuid(),
                'memb___id' => $this->getUsername(),
                'memb__pwd' => $this->getPassword(),
                'memb_name' => $this->getName(),
                'sno__numb' => $this->getPersonalId(),
                'post_code' => 0,
                'addr_info' => 0,
                'addr_deta' => 0,
                'tel__numb' => $this->getPhone(),
                'phon_numb' => $this->getPhone(),
                'mail_addr' => $this->getEmail(),
                'mail_chek' => $this->isConfirmedEmail(),
                'bloc_code' => $this->isBlocked(),
                'ctl1_code' => 1,
                'fpas_ques' => $this->getSecretQuestion(),
                'fpas_answ' => $this->getSecretAnswer(),
                'appl_days' => new \DateTime(),
                'modi_days' => new \DateTime(),
                'out__days' => new \DateTime(),
                'true_days' => new \DateTime(),
                Config::get('credit.column') => $this->getCredit(),
                Config::get('vip.column_type') => $this->getVipType(),
                Config::get('vip.column_expire') => $this->getVipExpire()
            ], [
                'string', 'string', 'string', 'string',
                'string', 'integer', 'integer', 'integer',
                'string', 'string', 'string', 'boolean',
                'boolean', 'integer', 'string', 'string',
                'datetime', 'datetime', 'datetime', 'datetime',
                'float', 'integer', 'datetime'
            ]);

            DriverManager::getConnection()->exec('SET IDENTITY_INSERT MEMB_INFO OFF');

            $this->_saveCoins();
        });
    }

    public function update() {
        if ($this->getUsername() != null) {
            DriverManager::getConnection()->transactional(function () {
                DriverManager::getConnection()->update('MEMB_INFO', [
                    'memb__pwd' => $this->getUsername(),
                    'memb_name' => $this->getName(),
                    'sno__numb' => $this->getPersonalId(),
                    'tel__numb' => $this->getPhone(),
                    'phon_numb' => $this->getPhone(),
                    'mail_addr' => $this->getEmail(),
                    'mail_chek' => $this->isConfirmedEmail(),
                    'fpas_ques' => $this->getSecretQuestion(),
                    'fpas_answ' => $this->getSecretAnswer(),
                    'bloc_code' => $this->isBlocked(),
                    Config::get('credit.column') => $this->getCredit(),
                    Config::get('vip.column_type') => $this->getVipType(),
                    Config::get('vip.column_expire') => $this->getVipExpire()
                ], [
                    'memb___id' => $this->getUsername()
                ], [
                    'string', 'string', 'string', 'string',
                    'string', 'string', 'integer', 'string',
                    'string', 'integer', 'float', 'integer', 'datetime'
                ]);

                $this->_saveCoins();
            });
        }
    }

    public function count($where = null) {
        $where = $where !== null ? ' WHERE ' . $where : '';
        $total = DriverManager::getConnection()->fetchColumn('SELECT
           COUNT(1) AS total FROM MEMB_INFO
       ' . $where);
        return $total;
    }

    public function getLastConnections() {
        $results = DriverManager::getConnection()->fetchAll('SELECT
            ac.GameIDC AS character,
            ms.IP AS ip,
            ms.ConnectStat AS connect_stat,
            ms.ServerName AS server_name,
            ms.ConnectTM AS connected_at,
            ms.DisConnectTM AS disconnected_at
            FROM MEMB_STAT ms
            JOIN AccountCharacter ac ON ms.memb___id = ac.Id
            WHERE memb___id = :username
            ORDER BY ConnectTM DESC
        ', ['username' => $this->getUsername()]);
        return $results;
    }

    public function getNextGuid() {
        $next = DriverManager::getConnection()->fetchColumn('SELECT MAX(memb_guid)+1 AS next FROM MEMB_INFO');
        return $next + 1;
    }

    public function getAvaliableServices() {
        if (empty($this->_services)) {
            $avaliables = DriverManager::getConnection()->fetchAll('SELECT * FROM mw_services s
                WHERE (s.parent_id is null
                OR s.allowed = 1
                OR EXISTS (SELECT 1 FROM mw_viptype_services WHERE viptype = :viptype AND s.id = service_id))
                AND
                (s.active = 1)
                ORDER BY s.sequence
            ', ['viptype' => $this->getVipType()]);
            $services = [];
            foreach ($avaliables as $avaliable) {
                $services[$avaliable['service']] = $avaliable;
            }
            $this->_services = $services;
        }
        return $this->_services;
    }

    public function getRootAvaliableServices() {
        $services = [];
        foreach ($this->getAvaliableServices() as $service) {
            if (empty($service['parent_id'])) {
                $services[] = $service;
            }
        }
        return $services;
    }

    public function __toString() {
        return $this->getName();
    }
}