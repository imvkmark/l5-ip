<?php namespace Imvkmark\L5Ip\Repositories;

use App\Lemon\Repositories\Sour\LmStr;
use Imvkmark\L5Ip\Contracts\Ip as IpContract;

/**
 * DZ 数据库
 * Class Tiny
 * @package Imvkmark\L5Ip\Repositories
 */
class Tiny extends Base implements IpContract {

	private $storePath = '';

	public function __construct() {
		$this->storePath = dirname(dirname(__DIR__)) . '/attachment/tiny.dat';
	}

	public function area($ip) {
		if ($this->isLocal($ip)) {
			return $this->localArea;
		}
		static $fp = NULL, $offset = [], $index = NULL;
		$ipdot    = explode('.', $ip);
		$ip       = pack('N', ip2long($ip));
		$ipdot[0] = (int) $ipdot[0];
		$ipdot[1] = (int) $ipdot[1];
		if ($fp === NULL && $fp = @fopen($this->storePath, 'rb')) {
			$offset = unpack('Nlen', fread($fp, 4));
			$index  = fread($fp, $offset['len'] - 4);
		} else if ($fp == false) {
			return 'Invalid IP data file';
		}
		$length = $offset['len'] - 1028;
		$start  = unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);
		for ($start = $start['len'] * 8 + 1024; $start < $length; $start += 8) {
			if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip) {
				$index_offset = unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
				$index_length = unpack('Clen', $index{$start + 7});
				break;
			}
		}
		fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
		return LmStr::convert($index_length['len'], '', 'utf-8') ? LmStr::convert(fread($fp, $index_length['len']), '', 'utf-8') : 'Unknown';
	}
}