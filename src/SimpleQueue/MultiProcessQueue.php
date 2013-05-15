<?php namespace SimpleQueue;

class MultiProcessQueue{
	protected $config = [];
	
	protected $taskQueue;
	protected $workerQueue;
	
	public function __construct($config) {
		if(is_array($config)){
			$this->config = $config;
		}
		else{
			$this->config = require_once $config;
		}

		$connections = $config['connection'];
		foreach($connections['config'] as $connectionName => $connectionConfig){
			\SimpleQueue\Connection\Connection::setConnectionConfig(
				$connectionName, $connectionConfig);
		}
		
		$keyPrefix = '';
		if(array_key_exists('key_prefix', $config)){
			$keyPrefix = $config['key_prefix'];
		}

		$workerKeySuffix = '';
		if(array_key_exists('worker_key_suffix', $config)){
			$workerKeySuffix = $config['worker_key_suffix'];
		}

		$this->taskQueue = new \SimpleQueue\Queue\TaskQueue(
			$connections['task'],
			"{$keyPrefix}SQ_TASK_LIST"
		);
			
		$this->workerQueue = new \SimpleQueue\Queue\Queue(
			$connections['worker'],
			"{$keyPrefix}SQ_WORKER_LIST{$workerKeySuffix}"
		);
		
		$this->workerQueue->delete();
	}
	
	public function end(){
		$this->workerQueue->push('END');
	}
	
	public function isEnd($msg){
		return ('END' == $msg);
	}
	
	public function childProcessEnd(){
		$this->taskQueue
			->privatePush('END');
	}

	
	public function popFire(){
		list($key, $data) = $this->taskQueue->pop();

		if($key === null){
			return true;
		}

		if($this->taskQueue->isPrivateKey($key)){
			if($data === 'END'){
				return false;
			}
		}

		$this->fire(
			$this->unserialize($data)
		);
		
		return true;
	}
	
	public function fire($data){
		$data();
	}

	public function push($data){
		$this->taskQueue->push(
			$this->serialize($data)
		);		
	}
		
	public function serialize($data){
		return serialize($data);
	}
	
	public function unserialize($data){
		return unserialize($data);
	}
	
	public function clean(){
		$this->taskQueue
			->privateDelete();
	}
		
	public function peekWorkerMessage(){
		$msg = $this->workerQueue->pop(1);
		return ($msg === null) ? null : $msg[1];
	}
	
	public function getTaskQueue(){
		return $this->taskQueue;
	}
	
	public function getWorkerQueue(){
		return $this->workerQueue;
	}
	
	public function getTaskQueueSize(){
		return $this->taskQueue->size();
	}
}


