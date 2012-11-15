<?php namespace Feather\Extensions\TestExtension;

class TestExtension extends \Feather\Extensions\Extension {

	public function start($app)
	{
		$this->listen('start_test', function()
		{
			return 'success';
		});
	}

	public function foo()
	{
		return 'bar';
	}

}