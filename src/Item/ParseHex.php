<?php

namespace Mithos\Item;

class ParseHex {
    
    private $_hex;

    public function __construct($hex) {
        $this->setHex($hex);
    }

    public function setHex($hex) {
        $this->_hex = $hex;
        return $this;
    }

    public function getHex() {
        return $this->_hex;
    }

    public function getId() {
        return hexdec(substr($this->getHex(), 0, 2)) & 0x1F;
    }

    public function getIndex() {
        return ((hexdec(substr($this->getHex(), 0, 2)) & 0xE0) >> 5) + (((hexdec(substr($this->getHex(), 14, 2)) & 0x80) == 0x80) ? 8 : 0);
    }

    public function getLevel() {
        return (hexdec(substr($this->getHex(), 2, 2)) & 0x78) >> 3;
    }

    public function getExcellents() {
        $hex = hexdec(substr($this->getHex(), 14, 2));
        $exc = array();
        $exc[0] = ($hex & 0x01) == 0x01;
        $exc[1] = ($hex & 0x02) == 0x02;
        $exc[2] = ($hex & 0x04) == 0x04;
        $exc[3] = ($hex & 0x08) == 0x08;
        $exc[4] = ($hex & 0x10) == 0x10;
        $exc[5] = ($hex & 0x20) == 0x20;
        return $exc;
    }

    public function getDurability() {
        return hexdec(substr($this->getHex(), 4, 2));
    }

    public function getLuck() {
        return (hexdec(substr($this->getHex(), 2, 2)) & 0x04) == 0x04;
    }

    public function getOption() {
        return (((hexdec(substr($this->getHex(), 2, 2)) & 0x03)) + (((hexdec(substr($this->getHex(), 14, 2)) & 0x40) == 0x40) ? 4 : 0)) * 4;
    }

    public function getSerial() {
        return substr($this->getHex(), 6, 8);
    }

    public function getSkill() {
        return (hexdec(substr($this->getHex(), 2, 2)) & 0x80) === 0x80;
    }

    public function getUnique() {
        return (hexdec(substr($this->getHex(), 14, 2)) & 0x80) == 0x80;
    }

    public function getAncient() {

    }
    
}