<?php
    
namespace Mithos\Guild;

use Mithos\Database\DriverManager;

class Guild {
    
    public static function count($where = null) {
        $where = $where !== null ? ' WHERE ' . $where : '';
        $total = DriverManager::getConnection()->fetchColumn('SELECT
            COUNT(1) AS total FROM Guild
        ' . $where);
        return $total;
    }
    
}