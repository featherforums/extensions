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

		$app = $this->getApplication();

		$extension = new Feather\Models\Extension(array(
			'location' => '/',
			'identifier' => 'foobar',
			'auto' => false
		));

		$dispatcher = new Feather\Extensions\Dispatcher($app);

		$dispatcher->registerExtensions(array($extension));

		$this->assertInstanceOf('Feather\Models\Extension', $dispatcher['extension.foobar']);
		$this->assertEquals('foobar', $dispatcher['extension.foobar']->identifier);
	}


	public function testDispatcherRegisterExtension()
	{
		$app = $this->getApplication();

		$extension = new Feather\Models\Extension(array(
			'location' => '/',
			'identifier' => 'foobar',
			'auto' => false
		));

		$dispatcher = new Feather\Extensions\Dispatcher($app);

		$dispatcher->register($extension);

		$this->assertInstanceOf('Feather\Models\Extension', $dispatcher['extension.foobar']);
		$this->assertEquals('foobar', $dispatcher['extension.foobar']->identifier);
	}


	public function testDispatcherAutoStartExtension()
	{
		$app = $this->getApplication();
		
		$app['events'] = new Illuminate\Events\Dispatcher;

		$extension = new Feather\Models\Extension(array(
			'location' => 'TestExtension',
			'identifier' => 'testextension',
			'auto' => true
		));

		$dispatcher = new Feather\Extensions\Dispatcher($app);

		$dispatcher->register($extension);

		$this->assertArrayHasKey('Feather\Extensions\TestExtension\TestExtension', $extension->loaded);
		$this->assertEquals('bar', $extension->loaded['Feather\Extensions\TestExtension\TestExtension']->foo());
	}


	protected function getApplication()
	{
		$app = new Illuminate\Foundation\Application;

		$app['feather'] = new Illuminate\Foundation\Application;
		$app['feather']['path.extensions'] = __DIR__;

		$app['cache'] = m::mock('Illuminate\Cache\FileStore');
		$app['files'] = m::mock('Illuminate\Filesystem');

		$app['files']->shouldReceive('exists')->once()->andReturn(true);

		return $app;
	}


}

