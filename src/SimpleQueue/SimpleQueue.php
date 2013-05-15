<?php namespace SimpleQueue;

class SimpleQueue extends \SimpleQueue\MultiProcessQueue {
	
	protected $function;
	protected $serialize;
	protected $unserialize;

	public function __construct($config,
			$serialize = null, $unserialize = null) {

		$this->serialize = $serialize;
		$this->unserialize = $unserialize;

		parent::__construct($config);
	}

	public function serialize($data) {
		if($this->serialize == null){
			return parent::serialize($data);
		}
		$serialize = $this->serialize;
		return $serialize($data);
	}
	
	public function unserialize($data) {
		if($this->unserialize == null){
			return parent::unserialize($data);
		}
		$unserialize = $this->unserialize;
		return $unserialize($data);
	}

	public function fire($data){
		$function = $this->function;
		$function($data);
	}

	public function run($function = null,
			$minChildProcess = 4,
			$maxChildProcess = 8,
			$threshold = 4){
		if($function == null){
			$function = function($data){
				return call_user_func_array(
					[$data[0], $data[1]], $data[2]);
			};
		}
		$this->function = $function;
		
		(new \SimpleQueue\Process\MultiProcess(
				$minChildProcess,
				$maxChildProcess,
				$threshold)
		)->run($this);
	}
}
