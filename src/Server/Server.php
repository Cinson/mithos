<?php

namespace Mithos\Server;

use Mithos\Database\DriverManager;

class Server {

    public static function getCharactersOnline() {
        $result = DriverManager::getConnection()->fetchAll('SELECT
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

    public static function getMembersTeam() {
        $results = DriverManager::getConnection()->fetchAll('SELECT
            c.Name AS name,
            CASE WHEN s.ConnectStat > 0 and ac.GameIDC = c.Name THEN 1 ELSE 0 END as status
            FROM MEMB_STAT s
            INNER JOIN AccountCharacter ac ON (s.memb___id = ac.ID)
            INNER JOIN Character c ON (s.memb___id = c.AccountID)
            WHERE c.CtlCode > 7
        ');
        return $results;
    }

}