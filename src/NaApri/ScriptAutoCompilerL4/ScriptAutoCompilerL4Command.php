<?php namespace NaApri\ScriptAutoCompilerL4;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class ScriptAutoCompilerL4Command extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'sac';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		switch ($this->argument('mode')){

			case 'build':
				App::make('script-auto-compiler-l4')->build();
				break;
			case 'clear':
				File::deleteDirectory(
					Config::get('script-auto-compiler-l4::config.tmp')
				);
				break;
			default :
				break;
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			['mode', InputArgument::REQUIRED, 'mode {build|clear}'],
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

}
