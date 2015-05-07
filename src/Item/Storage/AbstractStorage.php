<?php

namespace Mithos\Item\Storage;

abstract class AbstractStorage {

    protected $_items = array();
    protected $_file;

    public function __construct($file) {
        $this->setFile($file);
        $this->parse();
    }

    public function getItems() {
        return $this->_items;
    }

    public function getItem($section, $index) {
//        echo '<pre>' . print_r($this->_items, true) . '</pre>';
        if (!isset($this->_items[$section][$index])) {
            throw new \Exception('No find item in file section: ' . $section . ' index: ' . $index);
        }
        return $this->_items[$section][$index];
    }

    public function setFile($file) {
        if (!file_exists($file)) {
            throw new Exception('Could not find file "' . $file . '"');
        }
        $this->_file = $file;
        return $this;
    }

    public function getFile() {
        return $this->_file;
    }

    protected function parse() {
        throw new \Exception('No method "parse" implementation');
    }
}