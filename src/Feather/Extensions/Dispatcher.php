<?php namespace Feather\Extensions;

use FilesystemIterator;
use Illuminate\Container;
use Illuminate\Foundation\Application;
use Feather\Models\Extension as FeatherExtension;

class Dispatcher extends Container {

	/**
	 * Laravel application instance.
	 * 
	 * @var Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * Started extensions.
	 * 
	 * @var array
	 */
	protected $started = array();

	/**
	 * Create a new extension dispatcher instance.
	 * 
	 * @param  Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Register an array of extensions with the dispatcher.
	 * 
	 * @param  array  $extensions
	 * @return void
	 */
	public function registerExtensions($extensions)
	{
		foreach ($extensions as $extension)
		{
			$this->register($extension);
		}
	}

	/**
	 * Register an extension with the dispatcher.
	 * 
	 * @param  Feather\Models\Extension  $extension
	 * @return void
	 */
	public function register(FeatherExtension $extension)
	{
		$path = $this->app['feather']['path.extensions'].'/'.$extension->location;

		if ($this->app['files']->exists($path))
		{
			$extension->path = $path;

			$extension->loaded = array();

			$this["extension.{$extension->identifier}"] = $extension;

			// If an extension is set to be automatically started then we'll hand it off to
			// the starting method.
			if ($extension->auto)
			{
				$this->start($extension->identifier);
			}
		}
	}

	/**
	 * Start an extension.
	 * 
	 * @param  string  $extension
	 * @return void
	 */
	public function start($extension)
	{
		if (in_array($extension, $this->started) or ! isset($this["extension.{$extension}"]))
		{
			return;
		}
		
		$extension = $this["extension.{$extension}"];

		foreach (new FilesystemIterator($extension->path) as $file)
		{
			$name = $file->getBasename(".{$file->getExtension()}");

			if (ends_with($name, 'Extension'))
			{
				$location = str_replace('/', '\\', $extension->location);

				$class = "Feather\\Extensions\\{$location}\\{$name}";

				// Instantiate the new extension class and assign it to the extensions loaded classes. The class
				// receives an instance of the Laravel application.
				$extension->loaded = array_merge($extension->loaded, array($class => new $class($this->app)));

				// If the extension has the start method we'll fire it, this is a great place for extensions
				// to do any bootstrapping or listening to events without having to override the constructor.
				if (method_exists($extension->loaded[$class], 'start'))
				{
					$extension->loaded[$class]->start($this->app);
				}
			}
		}
	}

}