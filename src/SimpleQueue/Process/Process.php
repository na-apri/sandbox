<?php namespace SimpleQueue\Process;

 class Process {
	protected $currentPid;
	protected $childProcessPidList = [];
	protected $endFunction = null;

	public function __construct(){
		$this->currentPid = getmypid();
				
		// シグナルハンドラ設定
		pcntl_signal(SIGTERM, [$this, 'sig_handler']);
		pcntl_signal(SIGINT, [$this, 'sig_handler']);
	}
	
	public function sig_handler($signo){
		switch ($signo) {
			case SIGTERM:
			case SIGINT:
				$this->endFunctionFire();
				exit;
			default:
		}
	}
	
	public function signalDispatch(){
		pcntl_signal_dispatch();
	}
	
	public function endFunctionFire(){
		if($this->endFunction != null){
			$processEndFunction = $this->endFunction;
			$processEndFunction();
		}
	}
	
	public function setEndFunction($processEndFunction){
		$this->endFunction
			= $processEndFunction;
	}

	public function fork($function){
		if(! $this->currentPid == getmypid()){
			throw new Exception("Error Not CurrentProcess Request Fork", 1);
		}

		$pid = pcntl_fork();

		if($pid == -1){
			exit;
		}
		if($pid == 0){
			$function();
			exit;
		}

		// PIDを登録
		$this->childProcessPidList[$pid] = $pid;
	}

	public function getChildProcessPidList(){
		return $this->childProcessPidList;
	}

	public function getChildProcessCount(){
		return count($this->childProcessPidList);
	}

	public function childProcessEndCheck(){
		while(true){
			$pid = pcntl_wait($status, WNOHANG);

			if(array_key_exists($pid, $this->childProcessPidList)){
				unset($this->childProcessPidList[$pid]);
			}

			if($pid == -1 || $pid == 0){
				break;
			}
		}
		return $pid;
	}	
	
	public function childProcessEndWait(){
		$pid = pcntl_wait($status);
		if(array_key_exists($pid, $this->childProcessPidList)){
			unset($this->childProcessPidList[$pid]);
		}
		return $pid;
	}		
}