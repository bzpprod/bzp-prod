<?php
App::uses('CakeEmail', 'Network/Email');
App::uses('CakeNumber', 'Utility');
App::uses('String', 'Utility');


class EmailQueueShell extends AppShell
{
	public $uses = array('EmailQueue');
	public $sendLimitPerSecond = 5;
	public $executionTime = 60;
	
	
	public function main()
	{
		CakeNumber::addFormat('BRR', array('before' => 'R$', 'thousands' => '', 'decimals' => ','));
		
		
		set_time_limit($this->executionTime);
		
		
		$timestamp_begin = strtotime('now');
		
		while (strtotime('now') - $timestamp_begin <= $this->executionTime)
		{
			$emails = $this->EmailQueue->find('all',
				array(
					'conditions' => array(
						'EmailQueue.is_mailed' => 0,
						'EmailQueue.is_deleted' => 0
					),
					
					'order' => array(
						'EmailQueue.priority DESC',
						'EmailQueue.attempts ASC',
						'EmailQueue.created ASC'
					),
					
					'limit' => $this->sendLimitPerSecond
				)
			);
			
			
			foreach ($emails as $key => $value)
			{
				$cakeEmail = new CakeEmail('amazonSES');
				$cakeEmail->helpers(array('Html'));
				$cakeEmail->from($value['EmailQueue']['from']);
				$cakeEmail->to($value['EmailQueue']['to']);
				$cakeEmail->replyTo($value['EmailQueue']['replyto']);
				$cakeEmail->subject($value['EmailQueue']['subject']);
				$cakeEmail->template($value['EmailQueue']['layout'] . DS . $value['EmailQueue']['template'], $value['EmailQueue']['layout']);
				$cakeEmail->viewVars(unserialize($value['EmailQueue']['vars']));
				$cakeEmail->emailFormat($value['EmailQueue']['format']);
				
				try
				{
					$cakeEmail->send();
					
					$this->EmailQueue->id = $value['EmailQueue']['id'];
					$this->EmailQueue->saveField('is_mailed', 1);
				}
				catch (Exception $e)
				{
				}
				
				
				$attempts = $value['EmailQueue']['attempts'];
				$attempts++;
				
				$this->EmailQueue->id = $value['EmailQueue']['id'];
				$this->EmailQueue->saveField('attempts', $attempts);
				
				if ($attempts >= 3)
				{
					$this->EmailQueue->saveField('is_deleted', 1);
				}				
			}
					
			
			sleep(2);
		}
	}
}