<?php namespace Imvkmark\L5Ip\Repositories;

use Imvkmark\L5Ip\Contracts\Ip as IpContract;

/**
 * 全球 IPv4 地址归属地数据库(17MON.CN 版)
 * 高春辉(pAUL gAO) <gaochunhui@gmail.com>
 * Build 20141009 版权所有 17MON.CN
 * (C) 2006 - 2014 保留所有权利
 * 请注意及时更新 IP 数据库版本
 * 数据问题请加 QQ 群: 346280296
 * Code for PHP 5.3+ only
 * Class Mon17
 * @package Imvkmark\L5Ip\Repositories
 */
class Mon17 extends Base implements IpContract {

	private $storePath = '';
	private $ip        = null;

	private $fp     = null;
	private $offset = null;
	private $index  = null;

	private $cached = [];

	public function __construct() {
		$this->storePath = dirname(dirname(__DIR__)) . '/attachment/17monipdb.dat';
	}


	public function area($ip) {
		if (empty($ip) === true) {
			return 'N/A';
		}

		$nip   = gethostbyname($ip);
		$ipdot = explode('.', $nip);

		if ($ipdot[0] < 0 || $ipdot[0] > 255 || count($ipdot) !== 4) {
			return 'N/A';
		}

		if (isset($this->cached[$nip]) === true) {
			return $this->cached[$nip];
		}

		if ($this->fp === null) {
			$this->init();
		}

		$nip2 = pack('N', ip2long($nip));

		$tmp_offset = (int) $ipdot[0] * 4;
		$start      = unpack('Vlen', $this->index[$tmp_offset] . $this->index[$tmp_offset + 1] . $this->index[$tmp_offset + 2] . $this->index[$tmp_offset + 3]);

		$index_offset = $index_length = null;
		$max_comp_len = $this->offset['len'] - 1024 - 4;
		for ($start = $start['len'] * 8 + 1024; $start < $max_comp_len; $start += 8) {
			if ($this->index{$start} . $this->index{$start + 1} . $this->index{$start + 2} . $this->index{$start + 3} >= $nip2) {
				$index_offset = unpack('Vlen', $this->index{$start + 4} . $this->index{$start + 5} . $this->index{$start + 6} . "\x0");
				$index_length = unpack('Clen', $this->index{$start + 7});

				break;
			}
		}

		if ($index_offset === null) {
			return 'N/A';
		}

		fseek($this->fp, $this->offset['len'] + $index_offset['len'] - 1024);

		$this->cached[$nip] = explode("\t", fread($this->fp, $index_length['len']));

		return implode(' ', $this->cached[$nip]);
	}

	private function init() {
		if ($this->fp === null) {
			$this->ip = new self();

			$this->fp = fopen($this->storePath, 'rb');
			if ($this->fp === false) {
				throw new \Exception('Invalid 17monipdb.dat file!');
			}

			$this->offset = unpack('Nlen', fread($this->fp, 4));
			if ($this->offset['len'] < 4) {
				throw new \Exception('Invalid 17monipdb.dat file!');
			}

			$this->index = fread($this->fp, $this->offset['len'] - 4);
		}
	}

	public function __destruct() {
		if ($this->fp !== null) {
			fclose($this->fp);
		}
	}
}