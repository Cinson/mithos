<?php
    
namespace Mithos\Guild;

use Mithos\DB\Mssql;

class Guild {
    
    public static function count($where = null) {
        $where = $where !== null ? ' WHERE ' . $where : '';
        $result = Mssql::getInstance()->fetch('SELECT 
            COUNT(1) AS total FROM Guild
        ' . $where);
        return $result['total'];
    }
    
}