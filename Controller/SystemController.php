<?php
App::uses('AppController', 'Controller');


class SystemController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'System';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Store', 'StoreProduct', 'StoreAdministrator', 'Log', 'Like', 'Comment', 'EmailQueue', 'UserNotificationAlert', 'Product','User' ,'Transaction');
	
	
	public $qualifyReminder_remindersDays = array(7,14,21);
	public $qualifyReminder_transactionMethods = array('sale', 'purchase');
	
	/**
	 * 
	 * Disable auth for notifications
	 */
	function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allowedActions = array('notificationAlert','forgottenProduct', 'qualifyReminder');
		
	}
		
/**
 * An object was liked.
 *
 * @return array
 */	
	public function like()
	{
		// Gets the liked object. 
		
		$model = $this->__urlToModel();
		
		if (!empty($model))
		{
			// Checks if the user has already liked the object.
			
			$like = $this->Like->find('first',
				array(
					'conditions' => array(
						'Like.model' => $model['model'],
						'Like.foreign_key' => $model['id'],
						'User.id' => $this->loggedUser['User']['id']
					)
				)
			);
			
			if (empty($like))
			{
				// Creates a log entry.
			
				#$this->Log->create();
				#$this->Log->save(
				#	array(
				#		'Log' => array(
				#			'model' => $model['model'],
				#			'foreign_key' => $model['id'],
				#			'action' => 'like'
				#		)
				#	)
				#);
				
			
				// Creates a like entry.
				
				$this->Like->create();
				$this->Like->save(
					array(
						'Like' => array(
							'model' => $model['model'],
							'foreign_key' => $model['id'],
							'user_id' => $this->loggedUser['User']['id']
						)
					)
				);
			}
			
			
			// Updates the object likes count.
			
			$count = $this->Like->find('count',
				array(
					'conditions' => array(
						'Like.model' => $model['model'],
						'Like.foreign_key' => $model['id']
					)
				)
			);
			
			$this->{ucfirst($model['model'])}->id = $model['id'];
			$this->{ucfirst($model['model'])}->saveField('likes', $count);
			
			
			$likes = array(
				'model' => $model['model'],
				'hash' => $model['hash'],
				'likes' => $count
			);
		}
		else
		{
			$likes = array();
		}
		
		
		$this->set(compact('likes'));
		$this->set('_serialize', array('likes'));

		echo (empty($likes) ? false : true);
		exit;
		return $likes;
	}
	
	
/**
 * An object was unliked.
 *
 * @return array
 */	
	public function unlike()
	{
		// Gets the unliked object. 
		
		$model = $this->__urlToModel();
		
		if (!empty($model))
		{
			// Checks if the object has already been liked.
			
			$like = $this->Like->find('first',
				array(
					'conditions' => array(
						'Like.model' => $model['model'],
						'Like.foreign_key' => $model['id'],
						'User.id' => $this->loggedUser['User']['id']
					)
				)
			);
			
			if (!empty($like))
			{
				// Creates a log entry.
		
				#$this->Log->create();
				#$this->Log->save(
				#	array(
				#		'Log' => array(
				#			'model' => $model['model'],
				#			'foreign_key' => $model['id'],
				#			'action' => 'unlike'
				#		)
				#	)
				#);
				
			
				// Deletes the like.
				
				$this->Like->delete($like['Like']['id']);
			}
			
			
			// Updates the object likes count.
			
			$count = $this->Like->find('count',
				array(
					'conditions' => array(
						'Like.model' => $model['model'],
						'Like.foreign_key' => $model['id']
					)
				)
			);
			
			$this->{ucfirst($model['model'])}->id = $model['id'];
			$this->{ucfirst($model['model'])}->saveField('likes', $count);
			
			
			$likes = array(
				'model' => $model['model'],
				'hash' => $model['hash'],
				'likes' => $count
			);
		}
		else
		{
			$likes = array();
		}
		
		
		$this->set(compact('likes'));
		$this->set('_serialize', array('likes'));
		
		echo (empty($likes) ? false : true);
		exit;
	}
	
	
/**
 * An object was commented.
 *
 * @return array
 */	
	public function comment()
	{
		// Indicates if was executed successfully.
		
		$success = false;
		
		
		// Checks if the model was specified.		
		
		if (!empty($this->request->query['model']))
		{
			$model = $this->request->query['model'];
		}
		
		
		// Checks if the hash was specified.
		
		if (!empty($this->request->query['hash']))
		{
			$hash = $this->request->query['hash'];
		}
		
		
		// Checks if the comment_id was specified.
		
		if (!empty($this->request->query['comment_id']))
		{
			$comment_id = $this->request->query['comment_id'];
		}
		
		
		// Checks if the parent_comment_id was specified.
		
		if (!empty($this->request->query['parent_comment_id']))
		{
			$parent_comment_id = $this->request->query['parent_comment_id'];
		}
		
		
		if (!empty($model) && !empty($hash) && !empty($comment_id) && is_numeric($comment_id))
		{
			// Checks if the specified comment_id already exists.
		
			$comment = $this->Comment->find('first',
				array(
					'conditions' => array(
						'Comment.fb_comment_id' => $comment_id
					)
				)
			);
			
			if (empty($comment))
			{
				// Comment default structure.
			
				$data = array(
					'Comment' => array(
						'user_id' => $this->loggedUser['User']['id'],
						'fb_comment_id' => $comment_id
					)
				);
				
				
				// Checks if the parent comment was specified and exists.
				
				if (!empty($parent_comment_id) && is_numeric($parent_comment_id))
				{
					$parent_comment = $this->Comment->find('first',
						array(
							'conditions' => array(
								'Comment.fb_comment_id' => $parent_comment_id
							)
						)
					);	
				
					if (!empty($parent_comment))
					{
						$data['Comment']['parent_id'] = $parent_comment['Comment']['id'];
					}
				}
				
				
				// Checks if the comment is for a product.
				
				if (strcasecmp($model, 'Product') == 0)
				{
					$product = $this->__product($hash);
					
					if (!empty($product))
					{
						$data['Comment']['model'] = 'Product';
						$data['Comment']['foreign_key'] = $product['StoreProduct']['id'];
					}
				}
				
				
				// Stores the comment.
				
				if (!empty($data['Comment']['model']) && !empty($data['Comment']['foreign_key']))
				{
					if ($this->Comment->save($data))
					{
						$success = true;
						
						
						// Checks if the comment is for a product.
						
						if (strcasecmp($model, 'Product') == 0)
						{
							// Gets the product data.
							
							$product = $this->__product($hash);
							
							
							// Checks if the logged user belongs to administrators list of the product.
							
							$administrator = $this->StoreAdministrator->find('first',
								array(
									'conditions' => array(
										'Store.id' => $product['Store']['id'],
										'User.id' => $this->loggedUser['User']['id'],
										'StoreAdministrator.is_deleted' => false
									)
								)
							);
							
							if (empty($administrator))
							{
								// Gets the product administrators and send email to them.
								
								$administrators = $this->StoreAdministrator->find('all',
									array(
										'conditions' => array(
											'Store.id' => $product['Store']['id'],
											'StoreAdministrator.is_deleted' => false
										)
									)
								);
								
								// Send the comment alert
								foreach ($administrators as $key => $value)
								{

									$this->CommunicationQueue->sendCommentAlert( $value['User']['email'], array('productUrl' => $product['StoreProduct']['url'], 'storeTitle' => $product['Store']['title'], 'productTitle' => $product['Product']['title']));
										
									if (!empty($value['User']['username']))
									{
										$this->CommunicationQueue->sendCommentAlert( $value['User']['username'] . '@facebook.com', array('productUrl' => $product['StoreProduct']['url'], 'storeTitle' => $product['Store']['title'], 'productTitle' => $product['Product']['title']));
									}
								}
							}
						}
					}
				}
			}
			else
			{
				$success = true;	
			}
		}
		
		
		$this->set(compact('success'));
		$this->set('_serialize', array('success'));
		
		return $success;
	}
	
	
/**
 * Gets the user Facebook friends.
 *
 * @return array
 */	
	public function facebook_friends()
	{
		// Indicates if was executed successfully.
		
		$success = false;
		
		
		// Gets the user personal store.
		
		$store = $this->Store->find('first',
			array(
				'conditions' => array(
					'Store.user_id' => $this->loggedUser['User']['id'],
					'Store.is_personal' => true,
					'Store.is_deleted' => false
				)
			)
		);
		$store = $this->__store($store['Store']['id']);
		
		try
		{
			// Gets the user Facebook friends.
			
			$fb_user_friends = $this->FB->api(
				array(
					'method' => 'fql.query',
					'query' => 'SELECT uid2 FROM friend WHERE uid1 = me()'
				)
			);
			
			if (is_array($fb_user_friends)) {
			foreach ($fb_user_friends as $key => $value)
			{
				if (!empty($value['uid2']))
				{
					// Checks if the relationship between user and his friend already exists.
				
					$friend = $this->Friend->find('first',
						array(
							'conditions' => array(
								'FacebookUser.fb_user_id' => $this->loggedUser['FacebookUser']['fb_user_id'],
								'FacebookUserFriend.fb_user_id' => $value['uid2']
							)
						)
					);
					
					if (empty($friend))
					{
						// Checks if the friend Facebook user already exists.
					
						$facebook_user = $this->FacebookUser->find('first',
							array(
								'conditions' => array(
									'FacebookUser.fb_user_id' => $value['uid2']
								)
							)
						);
						
						if (empty($facebook_user))
						{
							// Creates a friend Facebook user record.
						
							$this->FacebookUser->create();
							$this->FacebookUser->save(
								array(
									'FacebookUser' => array(
										'fb_user_id' => $value['uid2']
									)
								)
							);
							
							
							// Gets the created Facebook user record.
							
							$facebook_user = $this->FacebookUser->findById($this->FacebookUser->getLastInsertId());
						}
						
						
						// Creates the relationship record.
						
						$data = array(
							'Friend' => array(
								'facebook_user_id' => $this->loggedUser['FacebookUser']['id'],
								'friend_facebook_user_id' => $facebook_user['FacebookUser']['id']
							)
						);
						
						
						$this->Friend->create();
						
						if ($this->Friend->save($data))
						{
							// Send e-mail to friend if already registered at BazzApp.
							
							if ($this->loggedUser['User']['is_new'] && !empty($facebook_user['User']['id']))
							{
								$this->CommunicationQueue->sendFriendJoinedAlert( $facebook_user['User']['email'], array('friendName' => $facebook_user['User']['name'], 'userName' => $this->loggedUser['User']['name'], 'storeUrl' => $store['Store']['url'], 'userPicture' => 'https://graph.facebook.com/' . $this->loggedUser['FacebookUser']['fb_user_id'] . '/picture', 'storeName' => $store['Store']['title'] ));
								$this->CommunicationQueue->sendFriendJoinedFacebookNotification( $value['uid2'], array('friendName' => $facebook_user['User']['name'], 'url' => $store['Store']['url']));
							}
						}
					}
				}
				}
			}
			
			
			// Updates the user record.
			
			$this->User->id = $this->loggedUser['User']['id'];
			$this->User->saveField('is_new', false);
			
			
			// Deletes the Facebook.getFriends session.
			
			$this->Session->delete('Facebook.getFriends');
			
			
			$success = true;
		}
		catch(FacebookApiException $e)
		{
		}		
		
		
		$this->set(compact('success'));
		$this->set('_serialize', array('success'));
		
		return $success;
	}
	
	
	/*
	 * Handles the user notification, showed in ballons over his profile
	 * 
	 */
	public function notificationAlert($command = 'get')
	{

		$cacheKey = $this->request->params['controller'] . '::' . $this->request->params['action'] . '::' . $this->loggedUser['User']['id'];
		
		// Informing user id
		$this->UserNotificationAlert->setUser($this->loggedUser['User']['id']);
		
		
		if (!isset($this->request->query['n']))
		{
			$this->request->query['n'] = 0;
		}
		
		// Check if there is something to delete
		if (isset($this->request->query['rm']))
		{
			$toDelete = json_decode(urldecode($this->request->query['rm']),true);
		
			if (is_array($toDelete) && !empty($toDelete))
			{
				apc_delete($cacheKey);
				foreach ($toDelete as $k=>$v)
				{
					switch ($k)
					{
						case $this->UserNotificationAlert->getConstantValue('TYPE_STORE'): 
						{
								$this->UserNotificationAlert->purgeStore();
						} break;
						case $this->UserNotificationAlert->getConstantValue('TYPE_TRANSACTION'); 
						{
								$this->UserNotificationAlert->purgeTransaction();
						} break;
						case $this->UserNotificationAlert->getConstantValue('TYPE_INVITATION'); 
						{
								$this->UserNotificationAlert->purgeInvitation();
						} break;
						case $this->UserNotificationAlert->getConstantValue('TYPE_FEED'); 
						{
								$this->UserNotificationAlert->purgeFeed();
						} break;
					}
				}
			}
		}
		
		// TODO: Invalidate cache after model update (adding new values to the model)
		if (true)#if ($output = apc_fetch($cacheKey))
		{
			$output = array();
			$output[$this->UserNotificationAlert->getConstantValue('TYPE_STORE')]		= $this->UserNotificationAlert->getStore();
			$output[$this->UserNotificationAlert->getConstantValue('TYPE_TRANSACTION')]	= $this->UserNotificationAlert->getTransaction();
			$output[$this->UserNotificationAlert->getConstantValue('TYPE_INVITATION')]	= $this->UserNotificationAlert->getInvitation();
			$output[$this->UserNotificationAlert->getConstantValue('TYPE_FEED')]		= $this->UserNotificationAlert->getFeed();
			$output = json_encode($output);

			// circunventing apc bug for setting a variable that is already been defined
			@apc_add($cacheKey, $output, 600);
			
			// If the output is the same of last request, response an epty object, to economize
			if ($output == apc_fetch($cacheKey . '.last') && $this->request->query['n'] != '0')
			{
				$output = '{}';
			}
			else
			{
				apc_delete($cacheKey . '.last');
				apc_add($cacheKey . '.last', $output);
			}
		}
		
		header("content-type: application/json");
		echo '{"response":' . $output .'}';															  
		exit;
		
	}
	
	
	public function qualifyReminder()
	{
		
		CakeNumber::addFormat('BRR', array('before' => 'R$', 'thousands' => '', 'decimals' => ','));
		
		$db = $this->Transaction;
		foreach ($this->qualifyReminder_remindersDays as $day) {
		  foreach ($this->qualifyReminder_transactionMethods as $method) {
			
			$sql = "SELECT DISTINCT t.id FROM bazzapp_transactions t WHERE NOT EXISTS (SELECT * FROM bazzapp_qualifications q WHERE q.transaction_id = t.id AND q.method='$method') AND t.created < SUBDATE(NOW(), INTERVAL $day DAY) AND t.created >= SUBDATE(NOW(), INTERVAL $day+1 DAY)";
		
			$result = $db->query($sql);
			$transactionIds = Set::extract('/t/id', $result);
			
			foreach ($transactionIds as $tId) {
				$transaction = $this->Transaction->read(null, $tId);
				$product = $this->Product->read(null, $transaction['StoreProduct']['product_id']);
				
	
				if ($method == 'sale') {
					$sa = $this->StoreAdministrator->find('first', array('conditions' =>  array('StoreAdministrator.store_id' => $transaction['Store']['id'])));
					$userId = Set::extract('/StoreAdministrator/user_id', $sa);
					$user = $this->User->read(null, $userId);
					$to = $user['User']['email'];
				} else {
					$to = $transaction['Buyer']['email']; 
				}
				
				$type = $method == 'sale' ? 'venda' : 'compra';
				$to = $user['User']['email'];
				$productTitle = $product['Product']['title']; 
				$entityType = $method == 'sale' ? 'vendedor' : 'comprador'; 
				$url = Configure::read('baseUrl') . ($method == 'sale' ? '/admin/sales/qualify/transaction:' . $transaction['Transaction']['hash'] : '/purchases/' . $transaction['Transaction']['hash'] . '/qualify'); 
			
				$this->CommunicationQueue->sendQualifyReminder($to, array('productTitle' => $product['Product']['title'], 'days' => $day, 'type'=> $type, 'entityType' => $entityType, 'url'=> $url));
			}					
		  }
		}
		echo 'ok';
		exit;
	
	}	
	
	
	public function forgottenProduct()
	{
		CakeNumber::addFormat('BRR', array('before' => 'R$', 'thousands' => '', 'decimals' => ','));
		
		
		$products = $this->Product->find('all',
				array('fields' => 'Product.id',
					'conditions' => array(
						'Product.created <= SUBDATE(NOW(), INTERVAL 90 DAY)',
						'Product.created > SUBDATE(NOW(), INTERVAL 91 DAY)',
						'Product.is_deleted' => false,
						'Product.quantity_available >' => 0
					)
				)
			);

		$products = Set::extract('/Product/id', $products);
		
		$products = $this->StoreProduct->find('all', array('conditions' => array('Product.id' => $products)));

		foreach ($products as $product) {
			
		
			$product = $this->__product($product['StoreProduct']['hash']);
			$owner = $this->User->find('first', array('conditions' => array('User.id' => $product['Store']['user_id'])));
			
			$this->CommunicationQueue->sendForgottenProductReminder($owner['User']['email'], array('productTitle' => $product['Product']['title'], 'productUrl' => $product['StoreProduct']['url'], 'userName'=> $owner['User']['name'], 'url'=> Configure::read('baseUrl') .  '/my-store/' .  $product['Store']['slug'] . '/' . $product['StoreProduct']['slug'] . '/edit'));					
			
		}
		echo 'ok';
		exit;
	
	}
}
