<?php namespace Feather\Extensions\Providers;

use Feather\Extensions\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Feather\Extensions\Console\FeatherExtensionCommand;

class ExtensionServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 * 
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function register($app)
	{
		$app['feather']['extensions'] = $app->share(function() use ($app)
		{
			return new Dispatcher($app['files'], $app['feather']['path.extensions']);
		});

		$app['feather']['extensions']->setApplication($app);

		$this->registerCommands($app);
	}

	/**
	 * Register the console commands.
	 * 
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	protected function registerCommands($app)
	{
		$app['command.feather.extension'] = $app->share(function()
		{
			return new FeatherExtensionCommand;
		});

		$app['events']->listen('artisan.start', function($artisan)
		{
			$artisan->resolve('command.feather.extension');
		});
	}

}