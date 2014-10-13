<?php

namespace Mithos\Item;

class Item {

    const ITEM_SIZE_20 = 20;
    const ITEM_SIZE_32 = 32;

    private $_hex;
    private $_id;
    private $_index;
    private $_unique;
    private $_name;
    private $_level;
    private $_option;
    private $_luck;
    private $_skill;
    private $_durability;
    private $_excellents;
    private $_serial;
    private $_ancient;
    private $_size = array();
    private $_itemSize = self::ITEM_SIZE_20;

    public function __construct($hex = null) {
        if ($hex !== null) {
            $this->setHex($hex);
        }
    }

    public function setHex($hex) {
        $this->_hex = $hex;
        return $this;
    }

    public function getHex() {
        return $this->_hex;
    }

    public function setId($id) {
        $this->_id = $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function getName() {
        return $this->_name;
    }

    public function setIndex($index) {
        $this->_index = $index;
        return $this;
    }

    public function getIndex() {
        return $this->_index;
    }

    public function setLevel($level) {
        $this->_level = $level;
        return $this;
    }

    public function getLevel() {
        return $this->_level;
    }

    public function setOption($option) {
        $this->_option = $option;
        return $this;
    }

    public function getOption() {
        return $this->_option;
    }

    public function setLuck($luck) {
        $this->_luck = $luck;
        return $this;
    }

    public function getLuck() {
        return $this->_luck;
    }

    public function setSkill($skill) {
        $this->_skill = $skill;
        return $this;
    }

    public function getSkill() {
        return $this->_skill;
    }

    public function setDurability($durability) {
        $this->_durability = $durability;
        return $this;
    }

    public function getDurability() {
        return $this->_durability;
    }

    public function addExcellent($position, $excellent) {
        $this->_excellents[$position] = $excellent;
        return $this;
    }

    public function getExcellent($position) {
        return $this->_excellents[$position];
    }

    public function setExcellents(array $excellents) {
        $this->_excellents = $excellents;
        return $this;
    }

    public function getExcellents() {
        return $this->_excellents;
    }

    public function setUnique($unique) {
        $this->_unique = $unique;
        return $this;
    }

    public function isUnique() {
        return $this->_unique;
    }

    public function setSerial($serial) {
        $this->_serial = $serial;
        return $this;
    }

    public function getSerial() {
        return $this->_serial;
    }

    public function setSize($x, $y = null) {
        if (is_array($x)) {
            $this->_size = $x;
        } else {
            $this->_size = array(
                'x' => $x,
                'y' => $y
            );
        }
        return $this;
    }

    public function getSize($position = null) {
        if ($position === null) {
            return $this->_size;
        } else {
            return $this->_size[$position];
        }
    }

    public function setAncient($ancient) {
        $this->_ancient = $ancient;
        return $this;
    }

    public function getAncient() {
        return $this->_ancient;
    }

    public function setItemSize($size) {
        $this->_itemSize = $size;
        return $this;
    }

    public function getItemSize() {
        return $this->_itemSize;
    }

    public function isHexEmpty() {
        return strtoupper($this->getHex()) === str_repeat('F', $this->getItemSize()) || $this->getHex() === str_repeat('0', $this->getItemSize());
    }
}