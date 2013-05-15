<?php namespace SimpleQueue\Process;

class MultiProcess extends \SimpleQueue\Process\Process {
	protected $maxChildProcessNum = 8;
	protected $minChildProcessNum = 4;
	protected $childProcessLifeSpan = 128;
	protected $threshold = 3;

	public function __construct(
			$minChildProcessNum = 4,
			$maxChildProcessNum = 8,
			$threshold = 3,
			$childProcessLifeSpan = 512) {

		$this->minChildProcessNum = $minChildProcessNum;
		$this->maxChildProcessNum = $maxChildProcessNum;
		$this->threshold = $threshold;
		$this->childProcessLifeSpan = $childProcessLifeSpan;

		parent::__construct();
	}

	public function run(\SimpleQueue\MultiProcessQueue $queue){
		gc_enable();

		$this->setEndFunction(function() use($queue){
			$count = $this->getChildProcessCount();
			
			for($i = $count; $i > 0; $i--){
				$queue->childProcessEnd();
			}
			for($i = $count; $i > 0; $i--){
				if($this->childProcessEndWait() == -1){
					break;
				}
			}

			while($this->childProcessEndWait() != -1){
				$queue->childProcessEnd();
			}

			$queue->clean();
		});

		for($i = 0; $i < $this->minChildProcessNum; $i++){
			$this->fork($queue, $this->childProcessLifeSpan);
		}
		
		while(true){
			gc_collect_cycles();

			$msg = $queue->peekWorkerMessage();

			if($msg == null){
				$this->signalDispatch();

				$this->childProcessEndCheck();

				$toChildProcessNum = $this->scaleChildProcessNum($queue->getTaskQueueSize());
				
				if($this->getChildProcessCount() > $toChildProcessNum){
					$queue->childProcessEnd();
					$this->childProcessEndCheck();
				}

				if($this->getChildProcessCount() < $toChildProcessNum){
					$this->fork($queue, $this->childProcessLifeSpan);
				}
			}
			elseif($queue->isEnd($msg)){
				$this->endFunctionFire();
				break;
			}
		}
	}

	protected function scaleChildProcessNum($queueSize){
		$processCount = $queueSize / $this->threshold;
		if($processCount == false){
			$processCount = $this->minChildProcessNum;
		}
		else{
			$processCount = round($processCount);
		}
		if($processCount > $this->maxChildProcessNum){
			$processCount = $this->maxChildProcessNum;
		}
		if($processCount < $this->minChildProcessNum){
			$processCount = $this->minChildProcessNum;
		}
		return $processCount;
	}
	
	public function fork($queue, $childProcessLifeSpan){
		parent::fork(function() use($queue, $childProcessLifeSpan){
			for($count = 0; $count < $childProcessLifeSpan; $count++){
				if(!$queue->popFire()){
					break;
				}
			}
		});
	}
}