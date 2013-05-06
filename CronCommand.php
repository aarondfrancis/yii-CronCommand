<?php

// put this in your /protected/commands folder.

/**
 * 
 * This is the cron job command that gets called by heroku (heroku.sh) every 10 minutes.
 * The jobs are queued up in the database and referenced by the CronJob model. Each action
 * should receive an array of parameters and return an array with keys succeeded as bool and
 * execution_result as string or null. Action can also return false to delay execution until
 * next loop if certain conditions aren't met. (like not sending an email during a certain 
 * time period, for example). 
 * 
 */

class CronCommand extends CConsoleCommand
{
	private function getMicrotime(){
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		return $time;
	}
	private function totalTime($start){
		$finish = $this->getMicrotime();
		return round(($finish - $start), 4);	
	}
	
	public function actionIndex(){
		$start = $this->getMicrotime();

		$now = new DateTime('now', new DateTimeZone('GMT'));
		$now = $now->format("Y-m-d H:i:s");

		$jobs = CronJob::model()->findAll('execute_after <:now AND executed_at IS NULL ORDER BY id ASC', array(':now'=>$now));


		for($i=0;$i<count($jobs); $i++){
			$job = $jobs[$i];
			echo "Processing Job " . $job->id . "\r\n";
			
			if(method_exists($this, $job->action)){
				$result = $this->{$job->action}($job->parameters);
				
				if($result === false){
					// do nothing, let the next cycle pick it up
					continue;
				}else{
					$executed_at = new DateTime('now', new DateTimeZone('GMT'));
					$job->succeeded = $result['succeeded'] ? 1 : 0;
					$job->execution_result = array_key_exists('execution_result', $result) ? $result['execution_result'] : "";
					$job->executed_at = $executed_at->format('Y-m-d H:i:s');
					
					$job->save();
				}
			}else{
				$executed_at = new DateTime('now', new DateTimeZone('GMT'));
				$job->executed_at = $executed_at->format('Y-m-d H:i:s');
				$job->succeeded = 0;
				$job->execution_result = 'Action does not exist.';
				$job->save();
			}
			
			// if, God forbid, this script ever run longer than 9 minutes, abort the loop.
			// If you don't heroku will kill the script after 10 (cron runs every 10 minutes).
			// I'd rather end it myself cleanly and let the next iteration pick up whatever is
			// left to be processed.
			if($this->totalTime($start) >= (9*60)) break;
		}
	}	
	
	public function testJob($params){
		// this job doesn't do anything, just returns success.
		return array(
			'succeeded' => true,
			'execution_result' => 'Job succeeded'
		);	
	}
}
