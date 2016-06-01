<?php namespace Imvkmark\L5Ip\Repositories;

use Imvkmark\L5Ip\Contracts\Ip as IpContract;

/**
 * Class Taobao
 * http://ip.taobao.com/
 * @package Imvkmark\L5Ip\Repositories
 */
class Taobao extends Base implements IpContract {

	private $url = 'http://ip.taobao.com/service/getIpInfo.php?ip=__IP__';

	public function area($ip) {
		if ($this->isLocal($ip)) {
			return $this->localArea;
		}
		$ipinfo = file_get_contents(str_replace('__IP__', $ip, $this->url));
		$result = json_decode($ipinfo, true);
		return $result;
	}
}