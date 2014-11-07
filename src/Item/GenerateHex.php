<?php

namespace Mithos\Item;

use Mithos\Core\MuVersion;

class GenerateHex {
    
    private $_item;

    public function __construct(Item $item) {
        $this->setItem($item);
    }

    public function setItem(Item $item) {
        $this->_item = $item;
        return $this;
    }

    public function getItem() {
        return $this->_item;
    }

    public function generate() {
        $item = $this->getItem();

        if ($item->getId() === null || $item->getIndex() === null) {
            return str_repeat('F', 20);
        }

        $hex = '';
        $hex .= $this->_generateByte1();
        $hex .= $this->_generateByte2();
        $hex .= $this->_generateByte3();
        $hex .= $this->_generateByte4to7();
        $hex .= $this->_generateByte8();

        if (MuVersion::is(MuVersion::V97)) {
            $hex .= '0000';
        } else if (MuVersion::is(MuVersion::V100)) {
            $hex .= $this->_generateByte9();
            $hex .= '00';
        } else {
            $hex .= $this->_generateByte9();
            $hex .= $this->_generateByte10();
            $hex .= $this->_generateByte11();
            $hex .= $this->_generateByte12to16();
        }

        return strtoupper($hex);
    }

    private function _generateByte1() {
        return $this->_fix(dechex((($this->getItem()->getId() & 0x1F) | (($this->getItem()->getIndex() << 5) & 0xE0)) & 0xFF));
    }

    private function _generateByte2() {
        $item = $this->getItem();
        $level = $item->getLevel() * 8;
        $level += $item->getSkill() ? 128 : 0;
        $level += $item->getLuck() ? 4 : 0;

        switch ($item->getOption()) {
            case 4: $level += 1; break;
            case 8: $level += 2; break;
            case 12: $level += 3; break;
            case 16: $level += 0; break;
            case 20: $level += 1; break;
            case 24: $level += 2; break;
            case 28: $level += 3; break;
        }

        return $this->_fix(dechex($level));
    }

    private function _generateByte3() {
        return $this->_fix(dechex($this->getItem()->getDurability()));
    }

    private function _generateByte4to7() {
        return $this->_fix($this->getItem()->getSerial(), 8);
    }

    private function _generateByte8() {
        $item = $this->getItem();
        $excellent = 0;
        if (MuVersion::is(MuVersion::V97, MuVersion::V100)) {
            $excellent += $item->isUnique() ? 128 : 0;
            $excellent += $item->getOption() >= 16 ? 64 : 0;
            $excellent += $item->getExcellent(0) ? 1 : 0;
            $excellent += $item->getExcellent(1) ? 2 : 0;
            $excellent += $item->getExcellent(2) ? 4 : 0;
            $excellent += $item->getExcellent(3) ? 8 : 0;
            $excellent += $item->getExcellent(4) ? 16 : 0;
            $excellent += $item->getExcellent(5) ? 32 : 0;
        } else {
            $excellent += $item->getOption() >= 4 ? 64 : 0;
            $excellent += $item->getExcellent(0) ? 1 : 0;
            $excellent += $item->getExcellent(1) ? 2 : 0;
            $excellent += $item->getExcellent(2) ? 4 : 0;
            $excellent += $item->getExcellent(3) ? 8 : 0;
            $excellent += $item->getExcellent(4) ? 16 : 0;
            $excellent += $item->getExcellent(5) ? 32 : 0;
        }
        return $this->_fix(dechex($excellent));
    }

    private function _generateByte9() {
        return $this->_fix(($this->getItem()->getAncient() == 1 ? 5 : 0) + ($this->getItem()->getAncient() == 2 ? 9 : 0), 2);
    }

    private function _generateByte10() {
        return $this->getItem()->getIndex() . ($this->getItem()->getRefine() ? 8 : 0);
    }

    private function _generateByte11() {
        $harmony = $this->getItem()->getHarmonyType();
        $harmony .= $this->getItem()->getHarmonyLevel();
        return $harmony;
    }

    private function _generateByte12to16() {
        $sockets = $this->getItem()->getSocket(0);
        $sockets .= $this->getItem()->getSocket(1);
        $sockets .= $this->getItem()->getSocket(2);
        $sockets .= $this->getItem()->getSocket(3);
        $sockets .= $this->getItem()->getSocket(4);
        return $sockets;
    }

    private function _fix($string, $size = 2) {
        return str_pad($string, $size, 0, STR_PAD_LEFT);
    }
}