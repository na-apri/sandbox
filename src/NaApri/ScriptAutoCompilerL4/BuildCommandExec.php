<?php namespace NaApri\ScriptAutoCompilerL4;


trait BuildCommandExec{
	function exec($filename, $output, $command){
		
		if(!file_exists($output) || filemtime($output) < filemtime($filename)){
			$cmd = str_replace('{FILE}', $filename, $command, $c1);
			$cmd = str_replace('{OUTPUT}', $output, $cmd, $c2);
			if($c1 == 0 || $c2 == 0){
				throw new \ErrorException(
					'Command {FILE} or {OUTPUT} not found. check your ScriptAutoCompilerL4 config');
			}
			
			exec($cmd. ' 2>&1', $res, $return_code);
			if($return_code !== 0){
				throw new \ErrorException($return_code.': '.print_r($res, true));
			}
		}
	}
	
}
