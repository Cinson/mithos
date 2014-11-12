<?php

namespace Mithos\Item;

use Mithos\Item\Storage\AbstractStorage;

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
    private $_refine;
    private $_sockets;
    private $_harmonyType;
    private $_harmonyLevel;
    private $_size = array();
    private static $_itemSize = self::ITEM_SIZE_20;
    private static $_storage;

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

    public function getWidth() {
        return $this->_size['x'];
    }

    public function getHeight() {
        return $this->_size['y'];
    }

    public function setAncient($ancient) {
        $this->_ancient = $ancient;
        return $this;
    }

    public function getAncient() {
        return $this->_ancient;
    }

    public function setRefine($refine) {
        $this->_refine = $refine;
        return $this;
    }

    public function getRefine() {
        return $this->_refine;
    }

    public function setHarmonyType($harmonyType) {
        $this->_harmonyType = $harmonyType;
        return $this;
    }

    public function getHarmonyType() {
        return $this->_harmonyType;
    }

    public function setHarmonyLevel($harmonyLevel) {
        $this->_harmonyLevel = $harmonyLevel;
        return $this;
    }

    public function getHarmonyLevel() {
        return $this->_harmonyLevel;
    }

    public function addSocket($position, $socket) {
        $this->_sockets[$position] = $socket;
        return $this;
    }

    public function getSocket($position) {
        return $this->_sockets[$position];
    }

    public function setSockets(array $sockets) {
        $this->_sockets = $sockets;
        return $this;
    }

    public function getSockets() {
        return $this->_sockets;
    }

    public static function setItemSize($size) {
        self::$_itemSize = $size;
    }

    public static function getItemSize() {
        return self::$_itemSize;
    }

    public static function setStorage(AbstractStorage $storage) {
        self::$_storage = $storage;
    }

    public static function getStorage() {
        return self::$_storage;
    }

    public function isHexEmpty() {
        return strtoupper($this->getHex()) === str_repeat('F', $this->getItemSize()) || $this->getHex() === str_repeat('0', $this->getItemSize());
    }

    public function getExcellentName($index) {
        $names = $this->getExcellentsName();
        if (isset($names[$index])) {
            return $names[$index];
        }
        return '';
    }

    public function getExcellentsName() {
        $names = array();
        $options = ItemUtil::getExcellentsName($this->getId(), $this->getIndex());
        $excellents = $this->getExcellents();

        for ($i = 0; $i < 6; $i++) {
            if ($excellents[$i]) {
                $names[] = $options['options'][$i];
            }
        }

        return $names;
    }

    public function parse() {
        if ($this->isHexEmpty()) {
            return;
        }

        $parse = new ParseHex($this->getHex());

        $this->setId($parse->getId())
            ->setIndex($parse->getIndex())
            ->setUnique($parse->getUnique())
            ->setLevel($parse->getLevel())
            ->setOption($parse->getOption())
            ->setSkill($parse->getSkill())
            ->setLuck($parse->getLuck())
            ->setDurability($parse->getDurability())
            ->setExcellents($parse->getExcellents())
            ->setSerial($parse->getSerial())
            ->update();

        return $this;
    }

    public function toArray() {
        $item = array();
        if (!$this->isHexEmpty()) {
            $item = array(
                'id' => $this->getId(),
                'index' => $this->getIndex(),
                'name' => $this->getName(),
                'size' => $this->getSize(),
                'unique' => $this->isUnique(),
                'level' => $this->getLevel(),
                'option' => $this->getOption(),
                'skill' => $this->getSkill(),
                'luck' => $this->getLuck(),
                'durability' => $this->getDurability(),
                'excellents' => $this->getExcellents(),
                'serial' => $this->getSerial(),
                'ancient' => $this->getAncient(),
                'refine' => $this->getRefine(),
                'sockets' => $this->getSockets(),
                'harmonyType' => $this->getHarmonyType(),
                'harmonyLevel' => $this->getHarmonyLevel()
            );
        }
        return $item;
    }

    public function update() {
        if (!$this->getStorage()) {
            throw new \RuntimeException('Storage class not found');
        }

        $item = $this->getStorage()->getItem($this->getIndex(), $this->getId());

        $this->setName($item['name'])
            ->setSize($item['width'], $item['height']);

        if ($this->getDurability() === null) {
            $this->setDurability($item['durability']);
        }
    }

    public function generate() {
        $generate = new GenerateHex($this);
        $hex = $generate->generate();
        $this->setHex($hex);
        return $hex;
    }

    public function clear() {
        $this->_hex = null;
        $this->_id = null;
        $this->_index = null;
        $this->_unique = null;
        $this->_name = null;
        $this->_level = null;
        $this->_option = null;
        $this->_luck = null;
        $this->_skill = null;
        $this->_durability = null;
        $this->_excellents = null;
        $this->_serial = null;
        $this->_ancient = null;
        $this->_size = array();
        $this->_refine = null;
        $this->_sockets = null;
        $this->_harmonyType = null;
        $this->_harmonyLevel = null;
    }
}