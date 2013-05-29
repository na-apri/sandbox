<?php namespace NaApri\ScriptAutoCompilerL4;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;

class ScriptAutoCompilerL4{
	protected $tmp;
	protected $url;
	protected $filename;
	protected $isMinify;
	protected $isDevelop;

	public function __construct($filename, $tmp, $url,$isMinify, $isDevelop) {
		$this->filename = $filename;
		$this->tmp = $tmp;
		$this->url = $url;
		$this->isMinify = $isMinify;
		$this->isDevelop = $isDevelop;
	}
	
	public function build(){
		$script = '';
		foreach($this->getUrlToFileTable() as $path){
			$script .= $this->compile($path).PHP_EOL;
		}

		if(!$this->isMinify){
			file_put_contents($this->filename, $script);
			return;
		}

		$output = "{$this->tmp}/bin/";
		
		if(!file_exists($output)){
			File::makeDirectory($output, 0777, true);
		}
		if(!file_exists(dirname($this->filename))){
			ld($this->filename);
			File::makeDirectory(dirname($this->filename), 0777, true);
		}

		$output .= basename($this->filename);
		file_put_contents($output, $script);
		File::copy(
			$this->minify($output),
			$this->filename
		);
	}

	public function minify($filepath){
		return App::make('script-auto-compiler-l4.minify')
				->minify($filepath);
	}

	public function compile($filepath){
		return App::make('script-auto-compiler-l4.compiler')
				->compile($filepath);
	}

	public function getUrls(){
		if($this->isDevelop){
			return App::make('script-auto-compiler-l4.finder')
					->getJavaScriptUrls();
		}
		return [$this->url];
	}
	
	public function getFiles(){
		return App::make('script-auto-compiler-l4.finder')
				->getScriptFiles();
	}

	public function getUrlToFileTable(){
		return App::make('script-auto-compiler-l4.finder')
				->getScriptUrlTable();
	}

}
