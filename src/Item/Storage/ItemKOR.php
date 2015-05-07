<?php

namespace Mithos\Item\Storage;

class ItemKOR extends AbstractStorage {

    protected function parse() {
        if (!$file = fopen($this->getFile(), 'rb+')) {
            throw new Exception('Was not possible to open the file, verify that the file has permissions');
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
                    $old = [
                        'index' => 0,
                        'x' => 1,
                        'y' => 2,
                        'name' => 6
                    ];
                    $new = [
                        'index' => 0,
                        'x' => 3,
                        'y' => 4,
                        'name' => 8
                    ];
                    switch ($section) {
                        case 0:
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                        case 5:
                            // 29 season 2
                            // 19 97d
                            switch(sizeof($columns)) {
                                case 30:
                                case 29:
                                case 28:
                                case 25:
                                    $key = $new;
                                    break;
                                case 19:
                                    $key = $old;
                                    break;
                                default:
                                    //throw new Exception("O item(kor) n�o � compativel com o sistema ou esta danificado (Categoria: {$counts["section"]}, Index: {$arrayResult[0]} {".sizeof($arrayResult)."}).");
                            }
                            break;
                        case 6:
                            //25 season 2
                            // 16 97d
                            switch($columns) {
                                case 27:
                                case 26:
                                case 25:
                                case 22:
                                    $key = $new;
                                    break;
                                case 16:
                                    $key = $old;
                                    break;
                                default:
//                                    throw new Exception("O item(kor) n�o � compativel com o sistema ou esta danificado (Categoria: {$counts["section"]}, Index: {$arrayResult[0]} {".sizeof($arrayResult)."}).");
                            }
                            break;
                        case 7:
                        case 8:
                        case 9:
                        case 10:
                        case 11:
                            //25 season 2
                            // 17 97d
                            switch(sizeof($columns)) {
                                case 27:
                                case 26:
                                case 25:
                                case 21:
                                    $key = $new;
                                    break;
                                case 17:
                                    $key = $old;
                                    break;
                                default:
//                                    throw new Exception("O item(kor) n�o � compativel com o sistema ou esta danificado (Categoria: {$counts["section"]}, Index: {$arrayResult[0]} {".sizeof($arrayResult)."}).");
                            }
                            break;
                        case 12:
                            //23 season 2
                            //20 97d
                            switch(sizeof($columns)) {
                                case 25:
                                case 24:
                                case 23:
                                    $key = $new;
                                    break;
                                case 20:
                                case 19:
                                    $key = $old;
                                    break;
                                default:
//                                    throw new Exception("O item(kor) n�o � compativel com o sistema ou esta danificado (Categoria: {$counts["section"]}, Index: {$arrayResult[0]} {".sizeof($arrayResult)."}.");
                            }
                            break;
                        case 13:
                            switch(sizeof($columns)) {
                                case 26:
                                case 25:
                                case 24:
                                    $key = $new;
                                    break;
                                case 17:
                                    $key = $old;
                                    break;
                                default:
//                                        throw new Exception("O item(kor) n�o � compativel com o sistema ou esta danificado (Categoria: {$counts["section"]}, Index: {$arrayResult[0]} {".sizeof($arrayResult)."}).");
                            }
                            break;
                        case 14:
                            //11 season 2
                            //9 97d
                            switch(sizeof($columns)) {
                                case 11:
                                    $key = $new;
                                    break;
                                case 9:
                                    $key = $old;
                                    break;
                                default:
//                                        throw new Exception("O item(kor) n�o � compativel com o sistema ou esta danificado (Categoria: {$counts["section"]}, Index: {$arrayResult[0]} {".sizeof($arrayResult)."}).");
                            }
                            break;
                        case 15:
                            //18 season 2
                            //15 97d
                            switch(sizeof($columns)) {
                                case 20:
                                case 19:
                                case 18:
                                    $key = $new;
                                    break;
                                case 15:
                                    $key = $old;
                                    break;
                                default:
//                                        throw new Exception("O item(kor) n�o � compativel com o sistema ou esta danificado (Categoria: {$counts["section"]}, Index: {$arrayResult[0]} {".sizeof($arrayResult)."}).");
                            }
                            break;
                    }

                    $this->_items[$section][$columns[0]] = [
                        'name' => isset($columns[$key['name']]) ? $columns[$key['name']] : '',
                        'width' => isset($columns[$key['x']]) ? $columns[$key['x']] : 1,
                        'height' => isset($columns[$key['y']]) ? $columns[$key['y']] : 1,
                        'id' => $columns[0]
                    ];
                }
            }
        }
        return $this->_items;
    }
}