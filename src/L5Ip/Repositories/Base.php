<?php namespace Imvkmark\L5Ip\Repositories;

use App\Lemon\Repositories\Sour\LmUtil;

class Base {

	protected $localArea = '';

	protected function isLocal($ip) {
		if (LmUtil::isIp($ip)) {
			$tmp = explode('.', $ip);
			if ($tmp[0] == 10 || $tmp[0] == 127 || ($tmp[0] == 192 && $tmp[1] == 168) || ($tmp[0] == 172 && ($tmp[1] >= 16 && $tmp[1] <= 31))) {
				$this->localArea = 'LAN';
				return true;
			} elseif ($tmp[0] > 255 || $tmp[1] > 255 || $tmp[2] > 255 || $tmp[3] > 255) {
				$this->localArea = 'Unkonw';
				return true;
			} else {
				return false;
			}
		} else {
			$this->localArea = 'unvalid ip4 address';
			return true;
		}
	}
}
