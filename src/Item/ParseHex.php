<?php

namespace Mithos\Item;

use Mithos\Core\MuVersion;

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
        $temp = hexdec(substr($this->getHex(), 0, 2));
        if (MuVersion::is(MuVersion::V97, MuVersion::V100)) {
            return $temp & 0x1F;
        } else {
            return $temp;
        }
    }

    public function getIndex() {
        $temp = hexdec(substr($this->getHex(), 0, 2));
        $unique = hexdec(substr($this->getHex(), 14, 2));
        if (MuVersion::is(MuVersion::V97, MuVersion::V100)) {
            return ($temp & 0xE0 >> 5) + ((($unique & 0x80) == 0x80) ? 8 : 0);
        } else {
            return hexdec(substr($this->getHex(), 18, 1));
        }
    }

    public function getLevel() {
        return (hexdec(substr($this->getHex(), 2, 2)) & 0x78) >> 3;
    }

    public function getExcellents() {
        $temp = hexdec(substr($this->getHex(), 14, 2));
        $exc = array();
        $exc[0] = ($temp & 0x01) == 0x01;
        $exc[1] = ($temp & 0x02) == 0x02;
        $exc[2] = ($temp & 0x04) == 0x04;
        $exc[3] = ($temp & 0x08) == 0x08;
        $exc[4] = ($temp & 0x10) == 0x10;
        $exc[5] = ($temp & 0x20) == 0x20;
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
        return hexdec(substr($this->getHex(), 17, 1));
    }
    
    public function getRefine() {
        return hexdec(substr($this->getHex(), 19, 1));
    }
    
    public function getHarmonyType() {
        return hexdec(substr($this->getHex(), 20, 2)) & 0xF0 >> 4;
    }
    
    public function getHarmonyLevel() {
        return hexdec(substr($this->getHex(), 20, 2)) & 0x0F;
    }
    
    public function getSockets() {
        $sock = array();
        $sock[0] = hexdec(substr($this->getHex(), 22, 2));
        $sock[1] = hexdec(substr($this->getHex(), 24, 2));
        $sock[2] = hexdec(substr($this->getHex(), 26, 2));
        $sock[3] = hexdec(substr($this->getHex(), 28, 2));
        $sock[4] = hexdec(substr($this->getHex(), 30, 2));
        return $sock; 
    }
    
}