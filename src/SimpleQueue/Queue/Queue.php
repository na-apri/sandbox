<?php namespace SimpleQueue\Queue;

class Queue {
	protected $connectionName;
	protected $key;

	public function __construct(
		$connectionName, $key) {
		
		$this->connectionName = $connectionName;
		$this->key = $key;
	}
	
	public function getConnectionName(){
		return $this->connectionName;
	}

	public function getKey(){
		return $this->key;
	}
	
	protected function getConnection(){
		return \SimpleQueue\Connection\Connection::getConnection(
				$this->connectionName);
	}
	
	public function push($data){
		$this->getConnection()
			->rpush($this->key, $data);		
	}

	public function lPush($data){
		$this->getConnection()
			->lpush($this->key, $data);		
	}
	
	public function pop($timeout = 0){
		$data = $this->getConnection()
			->blpop([$this->key], $timeout);

		if(!is_array($data) || count($data) == 0){
			return null;
		}
		return $data;
	}
	
	public function size(){
		return $this->getConnection()
			->lSize($this->key);		
	}

	public function range(){
		return $this->getConnection()
			->lRange($this->key, 0, -1);		
	}

	public function delete(){
		return $this->getConnection()
			->delete($this->key);		
	}
}