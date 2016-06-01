<?php namespace Imvkmark\L5Ip\Repositories;

use Imvkmark\L5Ip\Contracts\Ip as IpContract;

class Sina extends Base implements IpContract {

	private $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=__IP__';

	public function area($ip) {
		if ($this->isLocal($ip)) {
			return $this->localArea;
		}
		$sinaapi = str_replace('__IP__', $ip, $this->url);
		$ipdata  = file_get_contents($sinaapi);
		if ($ipdata) {
			$ipdata = str_replace(['var remote_ip_info = ', ';'], ['', ''], $ipdata);
			$arr    = json_decode($ipdata, true);
			$area   = '';
			if (isset($arr['country']) && strpos($ipdata, "\u4e2d\u56fd") === false) $area .= $arr['country'];
			if (isset($arr['province'])) $area .= $arr['province'];
			if (isset($arr['city'])) $area .= $arr['city'];
			if (isset($arr['isp'])) $area .= $arr['isp'];
			if ($area) {
				return $area;
			} else {
				return 'Unknown';
			}

		}
		return 'API Error';
	}
}