<?php

namespace Mithos\Item\Storage;

use Mithos\Item\Storage\AbstractStorage;

class ItemKOR extends AbstractStorage {

    protected function parse() {
        if (!$file = fopen($this->getFile(), 'rb+')) {
            throw new \Exception('Was not possible to open the file, verify that the file has permissions');
        }
        $section = -1;
        while (!feof($file)) {
            $line = fgets($file);
            $line = trim($line, " \t\r\n");

            if (substr($line, 0, 2) == '//' || substr($line, 0, 2) == '#' || $line == '') {
                continue;
            }

            if (($pos = strpos($line, '//')) !== false) {
                $line = substr($line, 0, $pos);
            }
            $line = trim($line, " \t\r\n");

            if ($section == -1) {
                if (is_numeric($line)) {
                    $section = $line;
                }
            } else {
                if (strtolower($line) == 'end') {
                    $section = -1;
                    continue;
                } else {
                    $columns = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[\s,]+/", $line, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                    $this->_items[$section][$columns[0]] = array(
                        'name' => $columns[8],
                        'width' => $columns[3],
                        'height' => $columns[4],
                        'durability' => isset($columns[12]) ? $columns[12] : 0,
                        'id' => $columns[0]
                    );
                }
            }
        }
        return $this->_items;
    }
}