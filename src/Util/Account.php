<?php

namespace Mithos\Util;

use Mithos\Core\Config;

class Account {
	
	public static function vipName($code) {
		$vips = Config::get('vip.types', []);
		if (isset($vips[$code])) {
			return $vips[$code];
		}
		return '';
	}

}