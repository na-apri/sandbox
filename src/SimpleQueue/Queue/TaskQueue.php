<?php namespace SimpleQueue\Queue;


class TaskQueue extends \SimpleQueue\Queue\Queue {
	protected $privateKey;
	
	public function __construct($connectionName, $key) {
		$this->privateKey = 'SQ_'.md5(mt_rand().getmypid()).'_'.getmypid();
		parent::__construct($connectionName, $key);
	}
	
	public function privatePush($data){
		$this->getConnection()
			->rpush($this->privateKey, $data);		
	}

	public function privateRange(){
		return $this->getConnection()
			->lRange($this->privateKey, 0, -1);		
	}
	
	public function getPrivateKey(){
		return $this->privateKey;
	}
	
	public function isPrivateKey($key){
		return ($this->privateKey === $key);
	}
	
	public function privateDelete() {
		return $this->getConnection()->delete(
			$this->privateKey);
	}
	
	public function pop($timeout = 0){
		$data = $this->getConnection()
			->blpop([$this->privateKey, $this->key], $timeout);

		if(!is_array($data) || count($data) == 0){
			return [null, null];
		}
		return $data;
	}
	
}


