<?php namespace Feather\Extensions\TestExtension;

use Feather\Extensions\Extension;

class TestExtension extends Extension {

	public function __construct($app)
	{
		parent::__construct($app);
	}

	public function foo()
	{
		return 'bar';
	}

}