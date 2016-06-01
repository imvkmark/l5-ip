<?php namespace Imvkmark\L5Ip\Support;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade {

	protected static function getFacadeAccessor() {
		return 'l5.ip';
	}

}