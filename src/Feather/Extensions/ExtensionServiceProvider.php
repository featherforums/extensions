<?php namespace Feather\Extensions;

use Illuminate\Support\ServiceProvider;

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
	}

}