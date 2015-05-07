<?php
    
namespace Mithos\Ranking;

use Mithos\Database\DriverManager;
use Mithos\Mssql;
use Mithos\Plugin;

class Character extends AbstractRanking {

    public function getQuery() {
        $qb = DriverManager::getConnection()->createQueryBuilder();
        $qb->select('s.ConnectStat AS connect_stat',
                's.ConnectTM AS last_connection',
                's.ServerName AS server',
                'ac.GameIDC AS game_idc',
                'c.Name AS name',
                'c.cLevel AS level',
                'c.Experience AS experience',
                'c.Class AS class',
                'c.PkLevel AS pk_level',
                'c.PkCount AS pk_count',
                'c.AccountID AS account',
                'c.LevelUpPoint AS points',
                'c.MapNumber AS map',
                'c.MapPosY AS positionY',
                'c.MapPosX AS positionX',
                'c.CtlCode AS code',
                'c.Strength AS strength',
                'c.Dexterity AS agility',
                'c.Vitality AS vitality',
                'c.Energy AS energy',
                'c.Money AS money',
                'gm.G_Name AS guild',
                'CASE WHEN s.ConnectStat > 0 and ac.GameIDC = c.Name THEN 1 ELSE 0 END as status'
                )
                ->from('Character', 'c')
                ->leftJoin('c', 'AccountCharacter', 'ac', 'c.AccountID = ac.ID COLLATE DATABASE_DEFAULT')
                ->leftJoin('c', 'MEMB_STAT', 's', 'c.AccountID = s.memb___id COLLATE DATABASE_DEFAULT')
                ->leftJoin('c', 'GuildMember', 'gm', 'c.Name = gm.Name COLLATE DATABASE_DEFAULT')
                ->setMaxResults(50)
                ->orderBy('c.cLevel', 'DESC');

        return $qb;
    }
}