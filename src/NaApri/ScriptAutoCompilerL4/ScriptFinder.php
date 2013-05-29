<?php namespace NaApri\ScriptAutoCompilerL4;

use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\App;

class ScriptFinder {
	
	protected $scriptUrlTable = null;
	
	protected $directories = null;
	protected $names = [];
	protected $notNames = [];
	protected $depth = 4;
	
	public function __construct(
			$directories, $names = [], $notNames = [], $depth = 4) {

		$this->directories = $directories;

		$this->names = is_array($names)
							? $names
							: [$names];

		$this->notNames = is_array($notNames)
							? $notNames
							: [$notNames];

		$this->depth = $depth;
	}
	
	public function getDirectories(){
		return $this->directories;
	}

	public function setDirectories($directories){
		$this->directories = $directories;
	}

	public function getDepth(){
		return $this->depth;
	}
	
	public function setDepth($depth){
		$this->depth = $depth;
	}
	
	public function getScriptFiles(){
		return array_values($this->getScriptUrlTable());
	}
	
	public function getJavaScriptUrls(){
		return array_keys($this->getScriptUrlTable());		
	}
	
	public function getScriptUrlTable(){
		if($this->scriptUrlTable != null){
			return $this->scriptUrlTable;
		}

		$directories = $this->getDirectories();

		$directories = is_array($directories)
				? $directories
				: [$directories];

		$directories = array_map(
				function($directory){
					$realpath = realpath($directory);
					if($realpath == null){
						throw new \ErrorException(
							"The '{$directory}' directory not found. Check your 'ScriptAutoCompilerL4 config directories parameter'"
						);
					}
					return $realpath;
				}, $directories);

		$finder = (new Finder())
					->files()
					->in($directories)
					->depth(" < {$this->depth}");
	
		foreach($this->names as $name){
			$finder->name("*{$name}");
		}

		foreach($this->notNames as $name){
			$finder->notName("*{$name}");
		}
		

		$this->scriptUrlTable = [];
		foreach($finder as $file){
			$filepath = $file->getRealpath();

			$routeUri = $filepath;
			foreach($this->getDirectories() as $directory){
				$routeUri = str_replace($directory.DIRECTORY_SEPARATOR, '', $routeUri);
			}
			$routeUri = str_replace($this->names, '.js', $routeUri);

			$this->scriptUrlTable[$routeUri] = $filepath;
		}

		return $this->scriptUrlTable;
	}
}
