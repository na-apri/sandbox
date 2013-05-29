<?php namespace NaApri\ScriptAutoCompilerL4;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class ScriptCompiler {
	use BuildCommandExec;

	protected $tmp = '';
	protected $command = '';

	public function __construct($command, $tmp) {
		$this->command = $command;
		$this->tmp = $tmp;
	}
	
	public function getTmp(){
		return $this->tmp;
	}
	
	public function setTmp($tmp){
		$this->tmp = $tmp;
	}
	
	public function getCommand(){
		return $this->command;
	}

	public function setCommand($command){
		$this->command = $command;
	}
			
	public function compile($file){
		$dir = array_search(
				$file,
				App::make('script-auto-compiler-l4.finder')
				->getScriptUrlTable()
		);

		$output = "{$this->tmp}/src/{$dir}";
		
		if(!file_exists(dirname($output))){
			File::makeDirectory(dirname($output), 0777, true);
		}
		
		if(is_callable($this->command)){
			return call_user_func_array(
				$this->command,
				[$file, $output]
			);
		}

		$this->exec($file, $output, $this->command);
		return file_get_contents($output);
	}
}
