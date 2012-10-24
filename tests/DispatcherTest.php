<?php

use Mockery as m;

class DispatcherTest extends PHPUnit_Framework_TestCase {


	public function tearDown()
	{
		m::close();
	}


	public function testDispatcherRegistersExtensionsFromArray()
	{
		define('FEATHER_DATABASE', 'foo');

		$app = new Illuminate\Foundation\Application;

		$app['feather'] = new Feather\Feather($app);
		$app['feather']['path.extensions'] = __DIR__;

		$app['cache'] = m::mock('Illuminate\Cache\FileStore');
		$app['files'] = m::mock('Illuminate\Filesystem');

		$app['cache']->shouldReceive('forget')->once();
		$app['files']->shouldReceive('exists')->once()->andReturn(true);

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
		$app = new Illuminate\Foundation\Application;

		$app['feather'] = new Feather\Feather($app);
		$app['feather']['path.extensions'] = __DIR__;

		$app['files'] = m::mock('Illuminate\Filesystem');
		$app['files']->shouldReceive('exists')->once()->andReturn(true);

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
		$app = new Illuminate\Foundation\Application;
		
		$app['feather'] = new Feather\Feather($app);
		$app['feather']['path.extensions'] = __DIR__;

		$app['files'] = m::mock('Illuminate\Filesystem');
		$app['files']->shouldReceive('exists')->once()->andReturn(true);

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


}

