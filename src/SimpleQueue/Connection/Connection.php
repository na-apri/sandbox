<?php namespace SimpleQueue\Connection;

class Connection{
	static protected $connectionContainers = [];

	static public function setConnection(
		$redis, $connectionName = 'default'){
		static::getConnectionContainer()
			->setConnection($redis, $connectionName);
	}

	static public function getConnection(
		$connectionName = 'default'){
		return static::getConnectionContainer()
			->getConnection($connectionName);
	}

	static public function hasConnection(
		$connectionName = 'default'){
		return static::getConnectionContainer()
			->hasConnection($connectionName);
	}

	static protected function getConnectionContainer(){
		$pid = getmypid();
		if(! array_key_exists($pid, static::$connectionContainers)){
			static::$connectionContainers[$pid]
				= new ConnectionContainer();
		}
		return static::$connectionContainers[$pid];
	}

	static public function setConnectionConfig(
		$connectionName, $host, $port = 6379, $database = 0, $auth = null){
		ConnectionContainer::setConnectionConfig(
			$connectionName, $host, $port, $database, $auth
		);
	}

	static public function close(){
		static::getConnectionContainer()->close();
	}
}


