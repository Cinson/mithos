<?php

namespace Mithos\Item;

class Slot {
    
    private $_item;
    private $_free;

    public function setItem(Item $item) {
        $this->_item = $item;
        return $this;
    }

    public function getItem() {
        return $this->_item;
    }

    public function setFree($free) {
        $this->_free = $free;
        return $this;
    }

    public function isFree() {
        return $this->_free;
    }
}