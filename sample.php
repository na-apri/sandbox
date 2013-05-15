<?php

function simpleClassLoader($c) {
    $lastSlash = strrpos($c, '\\');
    if (false !== $lastSlash) {
        $lastSlash++;
        $ns = substr($c, 0, $lastSlash);
        $class = substr($c, $lastSlash);
        $path = strtr($ns, '\\', '/') . strtr($class, '_', '/');
    } else {
        $path = strtr($c, '_', '/');
    }
    @include_once 'src/'.$path . '.php';
}

spl_autoload_register('simpleClassLoader');


$queue = new \SimpleQueue\SimpleQueue([
	'connection' => [
		'config' => [
			'default' => [
				'host' => '127.0.0.1',
				'port' => '6379',
				'database' => '0',
			],
		],
		'task' => 'default',
		'worker' => 'default',
	],
	'key_prefix' => 'SAMPLE_',
]);


class SayTime{
	public function fire($msg1, $msg2){
		sleep(rand(1, 4));
		echo 'IN: '.$msg1.$msg2.'OUT: '.date('Y-m-d H:i:s').PHP_EOL;
	}
}

if(count($argv) == 1){
	for($i = 0; $i < 128; $i++){
		$queue->push([
			new SayTime(), 'fire', [date('Y-m-d H:i:s'), ' -> ']
		]);
		
		sleep(1);
	}
	exit;
}

if($argv[1] == 'stop'){
	$queue->end();
	exit;
}

if($argv[1] == 'queue'){
	print_r($queue->getTaskQueue()->range());
	print_r($queue->getWorkerQueue()->range());
	print_r($queue->getTaskQueue()->privateRange());
	exit;
}

if($argv[1] == 'clear'){
	$queue->getTaskQueue()->delete();
	$queue->getWorkerQueue()->delete();
	exit;
}


$queue->run();
