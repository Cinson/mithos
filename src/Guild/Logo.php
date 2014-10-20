<?php

namespace Mithos\Guild;

class Logo {

    private $_mark = null;
    private $_size = 32;

    public function __construct($mark = null) {
        $this->setMark($mark);
    }

    public function setMark($mark) {
        $this->_mark = $mark;
        return $this;
    }
    
    public function getMark() {
        return $this->_mark;
    }

    public function setSize($size) {
        $this->_size = $size;
        return $this;
    }
    
    public function getSize() {
        return $this->_size;
    }

    public function toHtml() {
        $size = $this->getSize() / 8;
        $html = '<table cellpadding="0" cellspacing="0" class="flag">';
        foreach ($this->getPixels() as $key => $value) {
            $html .= '<tr>';
            foreach ($value as $k => $color) {
                $html .= '<td style="width:' . $size . 'px;height:' . $size . 'px;background:rgb(' . $color[0] . ', ' . $color[0] . ', ' . $color[0] . ');"></td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

    public function toImage() {
        $size = $this->getSize() / 8;
        $image = imagecreate($this->getSize(), $this->getSize());
        imagecolorallocatealpha($image, 0, 0, 0, 127);
        foreach ($this->getPixels() as $key => $value) {
            foreach ($value as $k => $color) {
                $x = $k * $size;
                $y = $key * $size;
                imagefilledrectangle($image, $x, $y, $x + $size, $y + $size, imagecolorallocate($image, $color[0], $color[1], $color[2]));
            }
        }
        imagepng($image);
        imagedestroy($image);
    }

    private function getPixels() {
        $h = 0;
        $pixels = array();
        for ($x = 0; $x < 8; $x++) {
            for ($y = 0; $y < 8; $y++) {
                $char = substr($this->getMark(), $h++, 1);
                $color = $this->getColor($char);
                $pixels[$x][$y] = $color;
            }
        }
        return $pixels;
    }

    private function getColor($str) {
        switch (strtolower($str)) {
            case 1: return array(0, 0, 0);
                break;
            case 2: return array(128, 128, 128);
                break;
            case 3: return array(255, 255, 255);
                break;
            case 4: return array(254, 0, 0);
                break;
            case 5: return array(255, 127, 0);
                break;
            case 6: return array(255, 255, 0);
                break;
            case 7: return array(128, 255, 0);
                break;
            case 8: return array(0, 254, 1);
                break;
            case 9: return array(0, 254, 129);
                break;
            case 'a': return array(0, 255, 255);
                break;
            case 'b': return array(0, 128, 255);
                break;
            case 'c': return array(0, 0, 254);
                break;
            case 'd': return array(127, 0, 255);
                break;
            case 'e': return array(255, 0, 254);
                break;
            case 'f': return array(255, 0, 128);
                break;
        }
    }
}