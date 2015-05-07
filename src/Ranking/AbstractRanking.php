<?php
    
namespace Mithos\Ranking;

class AbstractRanking implements InterfaceRanking {
    
    protected $_view = 'rankings/default';
    
    public function getQuery() {
        throw new \Exception('Method getResult() not implemented');
    }
    
    public function getView() {
        return $this->_view;
    }
    
}