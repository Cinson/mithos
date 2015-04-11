<?php

namespace Mithos\Account;

use Mithos\DB\Mssql;
use Mithos\Character\Character;
use Mithos\Core\Config;

class Account {

    private $name;
    private $username;
    private $password;
    private $email;
    private $confirmedEmail;
    private $phone;
    private $secretQuestion;
    private $secretAnswer;
    private $blocked;
    private $personalId;
    private $binWarehouse;
    private $connected;
    private $characters;
    private $vipType;
    private $vipExpire;
    private $coins = array();
    private $data = array();

    public function __construct($username = null) {
        if ($username !== null) {
            $this->read($username);
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
        $this->username = strtolower($username);
        return $this;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setConfirmedEmail($confirmedEmail) {
        $this->confirmedEmail = (bool) $confirmedEmail;
        return $this;
    }

    public function isConfirmedEmail() {
        return $this->confirmedEmail;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setSecretQuestion($question) {
        $this->secretQuestion = $question;
        return $this;
    }

    public function getSecretQuestion() {
        return $this->secretQuestion;
    }

    public function setSecretAnswer($answer) {
        $this->secretAnswer = $answer;
        return $this;
    }

    public function getSecretAnswer() {
        return $this->secretAnswer;
    }

    public function setBlocked($blocked) {
        $this->blocked = (bool) $blocked;
        return $this;
    }

    public function isBlocked() {
        return $this->blocked;
    }

    public function setPersonalId($id) {
        $this->personalId = $id;
        return $this;
    }

    public function getPersonalId() {
        return $this->personalId;
    }

    public function setBinWarehouse($warehouse) {
        $this->binWarehouse = $warehouse;
        return $this;
    }

    public function getBinWarehouse() {
        return $this->binWarehouse;
    }

    public function setVipType($vipType) {
        $this->vipType = $vipType;
        return $this;
    }

    public function getVipType() {
        return $this->vipType;
    }

    public function setVipExpire($vipExpire) {
        $this->vipExpire = new \DateTime($vipExpire);
        return $this;
    }

    public function getVipExpire() {
        return $this->vipExpire;
    }

    public function setCoins(array $coins) {
        $this->coins = $coins;
        return $this;
    }

    public function setCoin($name, $value) {
        $this->coins[$name] = $value;
        return $this;
    }

    public function getCoins() {
        return $this->coins;
    }

    public function getCoin($coin) {
        return isset($this->coins[$coin]) ? $this->coins[$coin] : null;
    }

    public function isConnected() {
        return $this->connected;
    }

    public function toArray() {
        return $this->data;
    }

    public function exists() {
        return $this->getUsername() !== null;
    }

    public function getCharacters() {
        if ($this->characters === null) {
            $this->characters = array();
            $results = Mssql::getInstance()->fetchAll('SELECT
                Name AS name FROM Character
                WHERE AccountID = :username[string]
            ', ['username' => $this->getUsername()]);
            foreach ($results as $result) {
                $character = new Character($result['name']);
                $this->characters[] = $character;
                if (!isset($this->data['characters'])) {
                    $this->data['characters'] = [];
                }
                $this->data['characters'][] = $character->toArray();
            }
        }
        return $this->characters;
    }

    public function read($username = null) {
        $coins = '';
        foreach (Config::get('coins', []) as $coin) {
            $coins .= '(select ' . $coin['column'] . ' from ' . $coin['table'] . ' where ' . $coin['foreign_key'] . ' = :username[string]) as ' . $coin['column'] . ',';
        }

        $vip = '';
        if (config('vip.column_type')) {
            $vip .= 'mi.' . Config::get('vip.column_type') . ' as vipType,';
        }

        if (config('vip.column_expire')) {
            $vip .= 'mi.' . Config::get('vip.column_expire') . ' as vipExpire,';
        }

        $result = Mssql::getInstance()->fetch('SELECT
            mi.memb_guid AS guid,
            mi.memb___id AS username,
            mi.memb__pwd AS password,
            mi.memb_name AS name,
            mi.mail_addr AS email,
            mi.mail_chek AS confirmedEmail,
            mi.tel__numb AS phone,
            mi.fpas_ques AS secretQuestion,
            mi.fpas_answ AS secretAnswer,
            mi.sno__numb AS personalId,
            mi.bloc_code AS blocked,
            ms.ServerName AS serverName,
            ms.ConnectTM AS connectedAt,
            ms.DisConnectTM AS disconnectedAt,
            ms.ConnectStat AS connected,
            ' . $coins . '
            ' . $vip . '
            ms.IP AS ip,
            CONVERT(VARBINARY(1200), w.Items) as binWarehouse,
            ac.GameIDC AS lastUsedCharacter
            FROM MEMB_INFO mi
            LEFT JOIN MEMB_STAT ms ON mi.memb___id = ms.memb___id COLLATE DATABASE_DEFAULT
            LEFT JOIN AccountCharacter ac ON mi.memb___id = ac.Id COLLATE DATABASE_DEFAULT
            LEFT JOIN warehouse w ON mi.memb___id = w.AccountID COLLATE DATABASE_DEFAULT
            WHERE mi.memb___id = :username[string]
        ', array('username' => $username === null ? $this->getUsername() : $username));
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $method = 'set' . ucfirst($key);
                if (method_exists($this, $method)) {
                    $this->{$method}($value);
                }
            }

            $result['coins'] = [];
            foreach (Config::get('coins', []) as $coin) {
                $result['coins'][$coin['column']] = $result[$coin['column']];
                unset($result[$coin['column']]);
            }

            $this->data = $result;
            $this->setCoins($result['coins']);
        }
    }

    private function saveCoins() {
        foreach (Config::get('coins', []) as $coin) {
            if ($this->getCoin($coin['column'])) {
                $exists = Mssql::getInstance()->fetch('SELECT COUNT(1) as total FROM ' . $coin['table'] . ' where ' . $coin['foreign_key'] . ' = :username[string]', ['username' => $this->getUsername()]);
                if ($coin['table'] !== 'MEMB_INFO' && $exists['total'] == 0) {
                    Mssql::getInstance()->query('INSERT INTO ' . $coin['table'] . ' (\'' . $coin['column'] . '\', \'' . $coin['foreign_key'] . '\') VALUES (:coin[interger], :account[string])', [
                        'coin' => $this->getCoin($coin['column']),
                        'username' => $this->getUsername()
                    ]);
                } else {
                    Mssql::getInstance()->query('UPDATE ' . $coin['table'] . ' SET ' . $coin['column'] . ' = :coin[integer] WHERE ' . $coin['foreign_key'] . ' = :username[string]', [
                        'coin' => $this->getCoin($coin['column']),
                        'username' => $this->getUsername()
                    ]);
                }
            }
        }
    }

    public function insert() {
        Mssql::getInstance()->query('SET IDENTITY_INSERT MEMB_INFO ON;INSERT INTO MEMB_INFO
            (memb_guid, memb___id, memb__pwd, memb_name, sno__numb, post_code, addr_info, 
            addr_deta, tel__numb, phon_numb, mail_addr, mail_chek, bloc_code, ctl1_code, 
            fpas_ques, fpas_answ, appl_days, modi_days, out__days, true_days, ' . Config::get('vip.column_type') . ', ' . Config::get('vip.column_expire') . ') 
            VALUES 
            (:guid[integer], :username[string], :password[string], :name[string], :pid[string], 
            0, 0, 0, :phone[string], :phone[string], :email[string], :mailcheck[boolean], :blocked[boolean], 1, :question[string],
            :answer[string], GETDATE(), GETDATE(), GETDATE(), GETDATE(), :viptype[integer], :vipexpire[date]);SET IDENTITY_INSERT MEMB_INFO OFF
        ', [
            'guid' => $this->getNextGuid(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'name' => $this->getName(),
            'pid' => $this->getPersonalId(),
            'phone' => $this->getPhone(),
            'email' => $this->getEmail(),
            'mailcheck' => $this->isConfirmedEmail(),
            'blocked' => $this->isBlocked(),
            'question' => $this->getSecretQuestion(),
            'answer' => $this->getSecretAnswer(),
            'viptype' => $this->getVipType(),
            'vipexpire' => $this->getVipExpire()->format('Y-m-d H:i:s')
        ]);

        $this->saveCoins();
    }

    public function update() {
        if ($this->getUsername() != null) {
            Mssql::getInstance()->query('UPDATE MEMB_INFO SET 
                memb__pwd = :password[string],
                memb_name = :name[string],
                sno__numb = :pid[string],
                tel__numb = :phone[string],
                phon_numb = :phone[string],
                mail_addr = :email[string],
                mail_chek = :mailcheck[string],
                fpas_ques = :question[string], 
                fpas_answ = :answer[string],
                bloc_code = :blocked[boolean],
                ' . Config::get('vip.column_type') . ' = :viptype[integer],
                ' . Config::get('vip.column_expire') . ' = :vipexpire[date]
                WHERE memb___id = :username[string]
            ', [
                'username' => $this->getUsername(),
                'password' => $this->getPassword(),
                'name' => $this->getName(),
                'pid' => $this->getPersonalId(),
                'phone' => $this->getPhone(),
                'email' => $this->getEmail(),
                'mailcheck' => $this->isConfirmedEmail(),
                'blocked' => $this->isBlocked(),
                'question' => $this->getSecretQuestion(),
                'answer' => $this->getSecretAnswer(),
                'viptype' => $this->getVipType(),
                'vipexpire' => $this->getVipExpire()->format('Y-m-d H:i:s')
            ]);

            $this->saveCoins();
        }
    }

    public function count($where = null) {
        $where = $where !== null ? ' WHERE ' . $where : '';
        $result = Mssql::getInstance()->fetch('SELECT
           COUNT(1) AS total FROM MEMB_INFO
       ' . $where);
        return $result['total'];
    }

    public function getLastConnections() {
        $result = Mssql::getInstance()->fetchAll('SELECT 
            ac.GameIDC AS character,
            ms.IP AS ip,
            ms.ConnectStat AS connect_stat,
            ms.ServerName AS server_name,
            ms.ConnectTM AS connected_at,
            ms.DisConnectTM AS disconnected_at
            FROM MEMB_STAT ms
            JOIN AccountCharacter ac ON ms.memb___id = ac.Id
            WHERE memb___id = :username[string] 
            ORDER BY ConnectTM DESC
        ', ['username' => $this->getUsername()]);
        return $result;
    }

    public function getNextGuid() {
        $result = Mssql::getInstance()->fetch('SELECT MAX(memb_guid)+1 AS next FROM MEMB_INFO');
        return $result['next'] + 1;
    }

    public function __toString() {
        return $this->getName();
    }
}