<?php
    
namespace Mithos\Util;

class Inflector {
    
    public static function slug($string, $options = array()) {
    	$characters = array(
    		'Á' => 'A', 'Ç' => 'c', 'É' => 'e', 'Í' => 'i', 'Ñ' => 'n', 'Ó' => 'o', 'Ú' => 'u', 
    		'á' => 'a', 'ç' => 'c', 'é' => 'e', 'í' => 'i', 'ñ' => 'n', 'ó' => 'o', 'ú' => 'u',
    		'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u'
    	);
	
    	$string = strtr($string, $characters);
    	$string = strtolower(trim($string));
    	$string = preg_replace('/[^a-z0-9-]/', '-', $string);
    	$string = preg_replace('/-+/', '-', $string);
	
    	if(substr($string, strlen($string) - 1, strlen($string)) === '-') {
    		$string = substr($string, 0, strlen($string) - 1);
    	}
	
    	return $string;
    }

    public static function humanize($string) {
        return ucwords(str_replace(['_', '-'], ' ', $string));
    }

    public static function classify($string) {
        return static::camelize($string);
    }

    public static function camelize($string) {
        return str_replace(' ', '', static::humanize($string));
    }
    
}