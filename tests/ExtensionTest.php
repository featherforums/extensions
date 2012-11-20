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
		list($app, $dispatcher) = $this->getApplicationAndDispatcher();

		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

		$dispatcher->register($extension);

		$this->assertEquals('success', $app['events']->first('start_test'));
	}


	public function testExtensionsCanListen()
	{
		list($app, $dispatcher) = $this->getApplicationAndDispatcher();

		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

		$dispatcher->register($extension);

		$extension->loaded['Feather\Extensions\TestExtension\TestExtension']->listen('foobar', function()
		{
			return 'barfoo';
		});

		$this->assertEquals('barfoo', $app['events']->first('foobar'));
	}


	public function testExtensionsCanOverride()
	{
		list($app, $dispatcher) = $this->getApplicationAndDispatcher();

		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

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
		list($app, $dispatcher) = $this->getApplicationAndDispatcher();
		
		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

		$dispatcher->register($extension);

		$extension->loaded['Feather\Extensions\TestExtension\TestExtension']->listen('foobar', 'foo');

		$this->assertEquals('bar', $app['events']->first('foobar'));
	}


	protected function getApplicationAndDispatcher()
	{
		$app = new Illuminate\Container;

		$app['events'] = new Illuminate\Events\Dispatcher;

		$app['files'] = m::mock('Illuminate\Filesystem');
		$app['files']->shouldReceive('exists')->once()->andReturn(true);

		$dispatcher = new Feather\Extensions\Dispatcher($app['files'], __DIR__);

		$dispatcher->setApplication($app);

		return array($app, $dispatcher);
	}


}