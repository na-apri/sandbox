<?php namespace NaApri\ScriptAutoCompilerL4;


class ScriptMinify{
	use BuildCommandExec;

	protected $command;
	protected $tmp;
	
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
	
	public function minify($filename){
		
		$output = "{$this->tmp}/bin/";
		
		if(!file_exists(dirname($output))){
			File::makeDirectory(dirname($output), 0777, true);
		}

		$output .= pathinfo($filename, PATHINFO_FILENAME) . '.min.js';
		
		if(is_callable($this->command)){
			return call_user_func_array(
				$this->command,
				[$filename, $output]
			);
		}
		
		$this->exec($filename, $output, $this->command);

		return $output;
	}
	
}