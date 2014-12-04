<?php

/**
 * @created Wolf, adaptado Flavio Hernandes
 */
namespace Mithos\Item;

class Container {

    private $_items = array(array());
    private $_map = array(array());
    private $_width = 0;
    private $_height = 0;

    public function __construct($width, $height) {
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $this->_items[$x][$y] = null;
                $this->_map[$x][$y] = false;
            }
        }

        $this->_width = $width;
        $this->_height = $height;
    }

    public function getWidth() {
        return $this->_width;
    }

    public function getHeight() {
        return $this->_height;
    }

    public function load($hex) {
        $items = str_split($hex, Item::getItemSize());

        for ($i = 0; $i < $this->getWidth() * $this->getHeight(); $i++) {
            $cords = $this->_getCordBySlot($i);

            $item = new Item($items[$i]);
            if (!$item->isHexEmpty()) {
                $this->_put($item->parse(), $cords['x'], $cords['y']);
            }
        }
    }

    public function generate() {
        $temp = array();
        for ($y = 0; $y <= $this->getHeight(); $y++) {
            for ($x = 0; $x <= $this->getWidth(); $x++) {
                $item = $this->getItem($x, $y);
                if ($item == null) {
                    $temp[] = str_repeat('f', Item::getItemSize());
                } else {
                    $temp[] = $item->generate();
                }
            }
        }
        return join('', $temp);
    }

    public function insert(Item $item) {
        if ($item->getWidth() > $this->getWidth()) {
            throw new \Exception('Item width is bigger than containers width.');
        } elseif ($item->getHeight() > $this->getHeight()) {
            throw new \Exception('Item width is bigger than containers width.');
        } elseif ($item->getHeight() <= 0 || $item->getWidth() <= 0) {
            throw new Exception('Invalid item size (width <= 0 or height <= 0).');
        }

        for ($y = 0; $y <= $this->getHeight() - $item->getHeight(); $y++) {
            for ($x = 0; $x <= $this->getWidth() - $item->getWidth(); $x++) {
                if ($this->_fit($item, $x, $y)) {
                    $this->_put($item, $x, $y);
                    return true;
                }
            }
        }

        return false;
    }

    private function _put(Item $item, $posx, $posy) {
        if ($posx >= $this->getWidth() || $posx < 0) {
            throw new Exception('Invalid slot position. (x > width || x < 0)');
        } elseif ($posy >= $this->getHeight() || $posy < 0) {
            throw new Exception('Invalid slot position. (y > height || y < 0)');
        }

        for ($y = $posy; $y < $posy + $item->getHeight(); $y++) {
            for ($x = $posx; $x < $posx + $item->getWidth(); $x++) {
                $this->_map[$x][$y] = true;
            }
        }
        $this->_items[$posx][$posy] = $item;

        return true;
    }

    private function _fit($item, $posx, $posy) {
        if ($posx >= $this->getWidth() || $posx < 0) {
            throw new Exception('Invalid slot position. (x > width || x < 0)');
        } else if ($posy >= $this->getHeight() || $posy < 0) {
            throw new Exception('Invalid slot position. (y > height || y < 0)');
        }

        for ($y = $posy; $y < $posy + $item->getHeight(); $y++) {
            for ($x = $posx; $x < $posx + $item->getWidth(); $x++) {
                if ($this->hasItem($x, $y) != null) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getItem($x, $y) {
        if ($x < 0 || $y < 0 || $x >= $this->getWidth() || $y >= $this->getHeight()) {
            return null;
        }
        return $this->_items[$x][$y];
    }

    public function hasItem($x, $y) {
        if ($x < 0 || $y < 0 || $x >= $this->getWidth() || $y >= $this->getHeight()) {
            return false;
        }
        return $this->_map[$x][$y];
    }

    private function _getCordBySlot($slot) {
        return array(
            'x' => $slot % $this->getWidth(),
            'y' => floor($slot / $this->getWidth())
        );
    }
}