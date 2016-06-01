<?php namespace Imvkmark\L5Ip;

use Illuminate\Support\ServiceProvider;

class L5IpServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 * @return void
	 */
	public function boot() {
		// 发布 config 文件, 在命令行中使用 --tag=sour-lemon 来确认配置文件
		$this->publishes([
			__DIR__ . '/../config/config.php' => config_path('l5-ip.php'), // config
		], 'sour-lemon');

	}


	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register() {
		$this->mergeConfig();
		$this->registerIp();
	}

	/**
	 * Merges user's and sl-upload's configs.
	 * @return void
	 */
	private function mergeConfig() {
		$this->mergeConfigFrom(
			__DIR__ . '/../config/config.php', 'l5-ip'
		);
	}

	private function registerIp() {
		$this->app->singleton('l5.ip', function () {
			$type  = ucfirst(camel_case(config('l5-ip.store')));
			$class = 'Imvkmark\\L5Ip\\Repositories\\' . $type;
			$sms   = new $class();
			return $sms;
		});
	}


	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides() {
		return ['l5.ip'];
	}

}
