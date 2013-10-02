<?php
/**
 * @class CommunicationQueueComponent
 * Implementação da régua de comunicação
 *
 * @author: Carlos Rios
 * @version: 1.0
 */
class CommunicationQueueComponent extends AmazonComponent {

	var $name = 'CommunicationQueue';
	
	const EMAIL_FROM = 'BazzApp <atendimento@bazzapp.com>';
	const NO_REPLY = 'noreply@bazzapp.com';
	
	const MAIN_QUEUE = 'ReguaComunicacao';
	const WELCOME_EMAIL_QUEUE				= 'RC-welcomeEmail';
	const COMMENT_ALERT_QUEUE				= 'RC-commentReceived';
	const FRIEND_JOINED_QUEUE				= 'RC-friendJoined';
	const FRIEND_JOINED_FBNOTIFICATION		= 'FB-friendJoined';
	const NEW_PRODUCT_FROM_STORE_QUEUE		= 'RC-productFromStore';
	const STORE_FOLLOWED					= 'RC-storeFollowed';
	const STORE_FOLLOWED_FBNOTIFICATION		= 'FB-storeFollowed';
	const PRODUCT_ADDED						= 'RC-productAdded' ;
	const PRODUCT_ADDED_FBNOTIFICATION		= 'FB-productAdded' ;
	const QUALIFICATION_RECEIVED			= 'RC-qualificationReceived';
	const TRANSACTION_BUYER					= 'RC-transactionBuyer';
	const TRANSACTION_BUYER_FBNOTIFICATION	= 'FB-transactionBuyer';
	const TRANSACTION_SELLER				= 'RC-transactionSeller';
	const TRANSACTION_NOPAYPAL				= 'RC-transactionNoPaypal';
	const QUALIFY_REMINDER					= 'RC-qualifyReminder';
	const FORGOTTEN_PRODUCT					= 'RC-forgottenProduct';
	
	const DEBUG_LEVEL						= 0;
	private $queue =  null;
	
	
	private function doLog($queue, $data) {
		CakeLog::write('communicationQueue', $queue . ' - ' . serialize($data));
	}
	
	
	public function initialize(Controller $controller) {
		if ($this->queue == null && Configure::read('debug') <= self::DEBUG_LEVEL) {
			$response = $this->SQS->get_queue_url( self::MAIN_QUEUE );
			$this->queue = (string) $response->body->GetQueueUrlResult->QueueUrl;
		}
	}
	
	public function sendWellcomeEmail($to) {
		
		$type = self::WELCOME_EMAIL_QUEUE;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => null);

		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, $to);

			return false;
			
		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}

	
	
	public function sendCommentAlert($to, $vars) {
		$type = self::COMMENT_ALERT_QUEUE;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}

	
	
	public function sendFriendJoinedAlert($to, $vars) {
		$type = self::FRIEND_JOINED_QUEUE;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}

	
	
	public function sendNewProductFromStore($to, $vars) {
		$type = self::NEW_PRODUCT_FROM_STORE_QUEUE;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}


	public function sendStoreFollowed($to, $vars) {
		$type = self::STORE_FOLLOWED;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}
	
	public function sendProductAdded($to, $vars) {
		$type = self::PRODUCT_ADDED;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
			
	}
	
	public function sendQualificationReceived($to, $vars) {
		$type = self::QUALIFICATION_RECEIVED;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}

	public function sendBuyerNotification($to, $vars) {
		$type = self::TRANSACTION_BUYER;

		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}
	
	public function sendSellerNotification($to, $vars) {
		$type = self::TRANSACTION_SELLER;

		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}
	
	public function sendBuyerNoPaypal($to, $vars) {
		$type = self::TRANSACTION_NOPAYPAL;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}
	
	public function sendQualifyReminder($to, $vars) {
		$type = self::QUALIFY_REMINDER;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}

	public function sendForgottenProductReminder($to, $vars) {
		$type = self::FORGOTTEN_PRODUCT;
		
		$data = array('type'=> $type, 'to'=>$to, 'from' => self::EMAIL_FROM, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}
	
	
	public function sendFriendJoinedFacebookNotification($to, $vars) {
		$type = self::FRIEND_JOINED_FBNOTIFICATION;
		
		$data = array('type'=> $type, 'to'=>$to, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}


	public function sendStoreFollowedFacebookNotification($to, $vars) {
		$type = self::STORE_FOLLOWED_FBNOTIFICATION;
		
		$data = array('type'=> $type, 'to'=>$to, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
	}
	
	
	public function sendProductAddedFacebookNotification($to, $vars) {
		$type = self::PRODUCT_ADDED_FBNOTIFICATION;
		
		$data = array('type'=> $type, 'to'=>$to, 'vars' => $vars);
		
		if (Configure::read('debug') > self::DEBUG_LEVEL) {
			$this->doLog($type, array($to, $vars));	
			
			return false;

		} else {
			return $this->SQS->send_message($this->queue, base64_encode(json_encode($data)));
			
		}
			
	}
	
}	
