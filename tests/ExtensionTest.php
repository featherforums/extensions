<?php

use Mockery as m;

class ExtensionTest extends PHPUnit_Framework_TestCase {


	public function setUp()
	{
		require_once __DIR__.'/TestExtension/TestExtension.php';
	}


	public function tearDown()
	{
		m::close();
	}


	public function testExtensionsAreBootstrapped()
	{
		$app = $this->getApplication();

		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

		$dispatcher = new Feather\Extensions\Dispatcher($app);

		$dispatcher->register($extension);

		$this->assertEquals('success', $app['events']->first('start_test'));
	}


	public function testExtensionsCanListen()
	{
		$app = $this->getApplication();

		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

		$dispatcher = new Feather\Extensions\Dispatcher($app);

		$dispatcher->register($extension);

		$extension->loaded['Feather\Extensions\TestExtension\TestExtension']->listen('foobar', function()
		{
			return 'barfoo';
		});

		$this->assertEquals('barfoo', $app['events']->first('foobar'));
	}


	public function testExtensionsCanOverride()
	{
		$app = $this->getApplication();

		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

		$dispatcher = new Feather\Extensions\Dispatcher($app);

		$dispatcher->register($extension);

		$extension->loaded['Feather\Extensions\TestExtension\TestExtension']->listen('foobar', function()
		{
			return 'barfoo';
		});

		$extension->loaded['Feather\Extensions\TestExtension\TestExtension']->override('foobar', function()
		{
			return 'barbar';
		});

		$this->assertEquals('barbar', $app['events']->first('foobar'));
	}


	public function testExtensionsCanUseMethods()
	{
		$app = $this->getApplication();

		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

		$dispatcher = new Feather\Extensions\Dispatcher($app);

		$dispatcher->register($extension);

		$extension->loaded['Feather\Extensions\TestExtension\TestExtension']->listen('foobar', 'foo');

		$this->assertEquals('bar', $app['events']->first('foobar'));
	}


	protected function getApplication()
	{
		$app = new Illuminate\Foundation\Application;

		$app['events'] = new Illuminate\Events\Dispatcher;

		$app['feather'] = new Illuminate\Foundation\Application;
		$app['feather']['path.extensions'] = __DIR__;
		
		$app['files'] = m::mock('Illuminate\Filesystem');
		$app['files']->shouldReceive('exists')->once()->andReturn(true);

		return $app;
	}


}