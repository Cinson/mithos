<?php
    
namespace Mithos\Ranking;
    
use Mithos\Database\DriverManager;
use Mithos\Mssql;
    
class Guild extends AbstractRanking {
    
    protected $_view = 'rankings/guild';

    public function getQuery() {
        $qb = DriverManager::getConnection()->createQueryBuilder();
        $qb->select('G_Name as name',
                'G_Mark as mark',
                'G_Score as score',
                'G_Master as master')
                ->from('Guild', 'g')
                ->orderBy('G_Score', 'DESC')
                ->setMaxResults(50);

        return $qb;
    }
    
}