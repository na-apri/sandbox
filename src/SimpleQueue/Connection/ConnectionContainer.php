<?php namespace SimpleQueue\Connection;

class ConnectionContainer{
	protected $connections = [];

	public function hasConnection($connectionName = 'default'){
		return array_key_exists($connectionName, $this->connections);
	}

	public function setConnection($redis, $connectionName = 'default'){
		if(! $redis->IsConnected() == 1){
			throw Exception('Not Connected', 1);
		}

		static::setConnectionConfig(
			$connectionName,
			$redis->GetHost(),
			$redis->GetPort(),
			$redis->getDBNum(),
			$redis->GetAuth()
		);

		$this->connections[$connectionName] = $redis;
	}

	public function getConnection($connectionName = 'default'){
		if(! $this->hasConnection($connectionName)){
			if(! array_key_exists($connectionName, static::$connectionConfig)){
				throw Exception('ConnectionName Not Found', 1);
			}

			$config = static::$connectionConfig[$connectionName];

			$redis = new \Redis();
			$redis->connect($config['host'], $config['port']);

			if(array_key_exists('auth', $config)
				&& $config['auth'] !== null){

				if(! $redis->auth($config['auth'])){
					throw new Exception("Error Redis Auth Password", 1);
				}
			}

	 		$redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);
	 		$redis->select($config['database']);

	 		$connections[$connectionName] = $redis;
		}
		return $connections[$connectionName];
	}

	public function close(){
		foreach($this->connections as $redis){
			$redis->close();
		}
		$this->connections = [];
	}

	static protected $connectionConfig = [];
	static public function setConnectionConfig(
		$connectionName, $host, $port = 6379, $database = 0, $auth = null){

		if(is_array($host)){
			static::$connectionConfig[$connectionName] = $host;
			return;
		}

		static::$connectionConfig[$connectionName] = [
			'host' => $host,
			'port' => $port,
			'database' => $database,
			'auth' => $auth,
		];
	}

}



