<?php namespace Feather\Extensions\Console;

use Illuminate\Filesystem;
use Illuminate\Console\Command;

class FeatherExtensionCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'feather:extension';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Perform tasks related to Feather extensions.';

}