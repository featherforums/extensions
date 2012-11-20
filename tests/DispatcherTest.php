<?php

use Mockery as m;

class DispatcherTest extends PHPUnit_Framework_TestCase {


	public function setUp()
	{
		require_once __DIR__.'/TestExtension/TestExtension.php';
	}


	public function tearDown()
	{
		m::close();
	}


	public function testDispatcherRegistersExtensionsFromArray()
	{
		define('FEATHER_DATABASE', 'feather');

		$dispatcher = $this->getDispatcher();

		$extension = new Feather\Models\Extension(array(
			'location' => '/',
			'identifier' => 'foobar',
			'auto' => false
		));

		$dispatcher->registerExtensions(array($extension));

		$this->assertInstanceOf('Feather\Models\Extension', $dispatcher['extension.foobar']);
		$this->assertEquals('foobar', $dispatcher['extension.foobar']->identifier);
	}


	public function testDispatcherRegisterExtension()
	{
		$dispatcher = $this->getDispatcher();

		$extension = new Feather\Models\Extension(array(
			'location' => '/',
			'identifier' => 'foobar',
			'auto' => false
		));

		$dispatcher->register($extension);

		$this->assertInstanceOf('Feather\Models\Extension', $dispatcher['extension.foobar']);
		$this->assertEquals('foobar', $dispatcher['extension.foobar']->identifier);
	}


	public function testDispatcherAutoStartExtension()
	{
		$dispatcher = $this->getDispatcher();

		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

		$dispatcher->register($extension);

		$this->assertArrayHasKey('Feather\Extensions\TestExtension\TestExtension', $extension->loaded);
		$this->assertEquals('bar', $extension->loaded['Feather\Extensions\TestExtension\TestExtension']->foo());
	}


	protected function getDispatcher()
	{
		$app = new Illuminate\Container;

		$app['events'] = new Illuminate\Events\Dispatcher;

		$files = m::mock('Illuminate\Filesystem');
		$files->shouldReceive('exists')->once()->andReturn(true);

		$dispatcher = new Feather\Extensions\Dispatcher($files, __DIR__);

		$dispatcher->setApplication($app);

		return $dispatcher;
	}


}

