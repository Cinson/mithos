<?php

namespace Mithos\Account;

use Mithos\Item\Container;
use Mithos\Database\DriverManager;
use Mithos\Account\Account;

class Warehouse extends Container {

    private $_account;
    private $_money;

    public function __construct(Account $account, $width, $height) {
        parent::__construct($width, $height);
        $this->_account = $account;
        $this->_money = DriverManager::getConnection()->fetchColumn('SELECT Money FROM warehouse where AccountID = :account', ['account' => $account->getUsername()]);
    }

    public function getAccount() {
        return $this->_account;
    }

    public function setAccount(Account $account) {
        $this->_account = $account;
        return $this;
    }

    public function getMoney() {
        return $this->_money;
    }

    public function setMoney($money) {
        $this->_money = $money;
        return $this;
    }

    public function update($items = true) {
        DriverManager::getConnection()->executeUpdate('UPDATE warehouse
            SET
            Money = :money
            ' . ($items ? ', Items = 0x' . $this->generate() : '') . '
            WHERE AccountId = :account
        ', [
            'money' => $this->getMoney(),
            'account' => $this->getAccount()->getUsername()
        ]);

    }

}