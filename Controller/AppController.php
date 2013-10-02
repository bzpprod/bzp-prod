<?php
App::uses('Controller', 'Controller');
App::uses('FB', 'Facebook.Lib');
App::uses('Sanitize', 'Utility');
App::uses('String', 'Utility');
App::uses('CakeNumber', 'Utility');
App::uses('HttpSocket', 'Network/Http');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor', 'BrowserDetector', array('file' => 'browser.detector.php'));


class AppController extends Controller
{
/**
 * An array containing the names of helpers this controller uses.
 *
 * @var mixed
 */
	public $helpers = array('Session', 'Html', 'Form', 'Number', 'Facebook.Facebook');

/**
 * Array containing the names of components this controller uses.
 *
 * @var array
 */
	public $components = array('Session', 'Acl', 'Auth', 'RequestHandler', 'Util', 'Amazon',  'CommunicationQueue');

/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('User', 'UserReferral', 'FacebookUser', 'Store', 'StoreAdministrator', 'StoreProduct', 'Friend', 'Log', 'EmailQueue','UserNotificationAlert');

/**
 * Controller actions for which authorization is not required.
 *
 * @var array
 */
	public $allowedActions = array('display');
	
/**
 * Current logged user data.
 *
 * @var boolean
 */
	public $loggedUser = array();

/**
 * Disable login request.
 *
 * @var boolean
 */
	public $loginDisabled = false;

/**
 * Indicates if the current user is a robot.
 *
 * @var boolean
 */
	public $isRobot = false;
	
/**
 * Facebook shared object.
 *
 * @var Facebook
 */
	public $FB;
	
	
	
	/**
	 *  repository version.
	 *
	 *  @ var $__version__
	 */
	private static $__version__;
	
	
	
	
	const HIGHLIGHT_NO			= 0;
	const HIGHLIGHT_YES			= 1;
	const HIGHLIGHT_JUST_STORE	= 2;
	const HIGHLIGHT_STORE_TOO	= 3;	
/**
 * Called before the controller action.
 *
 */
	public function beforeFilter()
	{
		
		// Apply 'BRR' money format.
		
		CakeNumber::addFormat('BRR', array('before' => 'R$', 'thousands' => '', 'decimals' => ','));
		
		// Checks if the current requester is a robot or the facebook server.
		
		$browserDetector = new BrowserDetector(env('HTTP_USER_AGENT'));
		$this->isRobot = $browserDetector->is('robots');
		
		if (!$this->isRobot && strpos(env('HTTP_USER_AGENT'), 'externalhit') !== false)
		{
			$this->isRobot = true;
		}
		
		
		// Starts the Facebook shared object.
		
		$this->FB = new FB();
		$this->FB->setFileUploadSupport(true);
		$FBdata = $this->FB->getSignedRequest();
		
		// Apply the authorization settings.
		if (!$this->Session->read('loginRedirect'))
		{
			$this->Session->write('loginRedirect', $_SERVER["REQUEST_URI"]);
		}
		$this->Auth->userModel = 'User';
		$this->Auth->scope = array('User.is_deleted' => false);
		$this->Auth->authorize = array('Actions' => array('actionPath' => 'controllers'));
		$this->Auth->authenticate = array('Form' => array('fields' => array('username' => 'email')));
		$this->Auth->loginAction = array('controller' => 'products', 'action' => 'home', 'admin' => false);
		$this->Auth->logoutRedirect = '/';
		$this->Auth->loginRedirect = ($this->Session->read('loginRedirect') ? $this->Session->read('loginRedirect') : '/');
		$this->Auth->authError = __d('controller', 'app.beforeFilter.authError');
		$this->Auth->allowedActions = $this->allowedActions;
	
		
		// Set the last version on server;
		header("X-BZ-Version: ". self::getVersion());
		
		// Requests user authentication if necessary.
		# TODO: check why facebook is not redirecing user properly
		if (isset($FBdata['app_data']))
		{
			header("Location: " . (strpos($FBdata['app_data'],'/fp/') == 0 ? '' : '/fp') .$FBdata['app_data']);
			exit;
		}
		// If user is in home of fanpage but facebook address the page id, redirect to the fanpage url
		else if (isset($FBdata['page']) && $_SERVER['REQUEST_URI'] == '/fp/')
		{
			$storeSlug = $this->StoreProduct->query("SELECT slug FROM bazzapp_stores st, bazzapp_stores_facebook_page fp WHERE st.id=fp.store_id  AND fp.fb_page_id=" . $FBdata['page']['id']);
			header("Location: /fp/" . $storeSlug[0]['st']['slug']);
			exit;
		}
		 
		if (!$this->isRobot && !$this->loginDisabled)
		{
			try
			{
				// Logged user settings.
				
				$userId = null;
				$alreadyLogged = false;
				
				
				// Checks the user current status.
				
				$user = $this->Auth->user();
				
				if (!empty($user['User']['id']) && is_array($user['User']))
				{
					$userId = $user['User']['id'];
				}
				else if (!empty($user['User']) && is_numeric($user['User']) && is_array($user))
				{
					$userId = $user['User'];
				}
				else if (!empty($user) && is_numeric($user))
				{
					$userId = $user;
				}
				
				if (is_numeric($userId))
				{
					$alreadyLogged = true;
				}
				else
				{
					
					// Connects to Facebook and tries to obtain logged user data.
						
					$fb_user = $this->FB->api('/me');
					
					if (empty($fb_user['id']))
					{
						throw new Exception('Facebook login required.');
					}
					
					
					// Checks if the user has a Facebook User.
					
					$facebook_user = $this->FacebookUser->find('first',
						array(
							'conditions' => array(
								'FacebookUser.fb_user_id' => $fb_user['id']
							)
						)
					);
					
					if (empty($facebook_user['FacebookUser']['id']))
					{
						$data = array(
							'FacebookUser' => array(
								'fb_user_id' => $fb_user['id']
							)
						);
					
						if ($this->FacebookUser->save($data))
						{
							$facebook_user = $this->FacebookUser->findById($this->FacebookUser->getLastInsertId());
						}
						else
						{
							throw new Exception('Unable to create Facebook User.');
						}
					}
					else if (!empty($facebook_user['User']['id']))
					{
						$userId = $facebook_user['User']['id'];
					}
					
					
					// Creates the user record.
					
					if (!is_numeric($userId))
					{
						$data = array(
							'User' => array(
								'facebook_user_id' => $facebook_user['FacebookUser']['id'],
								'group_id' => 1,
								'username' => (!empty($fb_user['username']) ? $fb_user['username'] : ''),
								'name' => $fb_user['name'],
								'email' => $fb_user['email'],
								'birthday' => (!empty($fb_user['birthday']) ? date('Y-m-d', strtotime($fb_user['birthday'])) : ''),
								'gender' => (!empty($fb_user['gender']) ? $fb_user['gender'] : ''),
								'location' => (!empty($fb_user['location']['name']) ? $fb_user['location']['name'] : '')
							)
						);
						
						
						$this->User->create();
							
						if ($this->User->save($data))
						{
							$userId = $this->User->getLastInsertId();
							
							// Send a welcome email.
							$this->CommunicationQueue->sendWellcomeEmail($fb_user['email']);
							
							if (!empty($this->params->query['referral']))
							{
								$referralUser = $this->User->find('first',
									array(
										'conditions' => array(
											'User.referral_uid' => $this->params->query['referral']
										)
									)
								);
							
								if (!empty($referralUser['User']['id']))
								{
									$data = array(
										'UserReferral' => array(
											'referral_id' => $referralUser['User']['id'],
											'invited_id' => $userId
										)
									);
								
									$this->UserReferral->create();
									$this->UserReferral->save($data);
									// Adds an notification
									$this->UserNotificationAlert->setUser($referralUser['User']['id']);
									$this->UserNotificationAlert->addInvitation();
									
								}
							}
						}
						else
						{
							throw new Exception('Unable to create User.');
						}
					}
				}
				
				
				// Gets the user record.
				
				$user = $this->User->find('first',
					array(
						'contain' => array(
							'FacebookUser',
							'Store' => array(
								'Address',
								'PaypalAccount'
							)
						),
						'conditions' => array(
							'User.id' => $userId
						)
					)
				);
				
				if (empty($user))
				{
					throw new Exception('Invalid logged user.');
				}
				
				
				// Updates the username / email fields if necessary.
				
				if (!empty($fb_user))
				{
					// Checks the username.
					
					if (empty($user['User']['username']) && !empty($fb_user['username']))
					{
						$user['User']['username'] = $fb_user['username'];
						
						$this->User->id = $userId;
						$this->User->saveField('username', $fb_user['username']);
					}
					
					
					// Checks the email.
					
					if (empty($user['User']['email']) || strcasecmp($user['User']['email'], 'user@bazzapp.com') == 0)
					{
						$user['User']['email'] = $fb_user['email'];
						
						$this->User->id = $userId;
						$this->User->saveField('email', $fb_user['email']);
					}
				}
				
				
				// Generates the referral uid if necessary.
				
				if (empty($user['User']['referral_uid']))
				{
					$this->User->id = $user_id;
					$user['User']['referral_uid'] = $this->User->generateReferralUid();
				}
				
				
				// Checks if the user is already logged.
				
				if (!$alreadyLogged)
				{
					// Enables the Facebook friends request.
					
					$this->Session->write('Facebook.getFriends', true);
					$this->facebookFriends = true;
					
					
					// Trys to authenticate.
					
					if ($this->Auth->login($userId))
					{
						// Creates a log entry.
						
						#$this->Log->create();
						#$this->Log->save(
						#	array(
						#		'Log' => array(
						#			'action' => 'login'
						#		)
						#	)
						#);
						
						
						// Checks if the user personal store already exists.
						
						$store = $this->Store->find('first',
							array(
								'conditions' => array(
									'Store.user_id' => $userId,
									'Store.is_personal' => true,
									'Store.is_deleted' => false
								)
							)
						);
						
						if (empty($store))
						{
							$data = array(
								'Store' => array(
									'user_id' => $userId,
									'title' => $user['User']['name'],
									'is_personal' => true,
									'is_enabled' => true,
									'is_deleted' => false
								)
							);
							
							
							$this->Store->create();
							
							if ($this->Store->save($data))
							{
								$storeId = $this->Store->getLastInsertId();
								
								$this->StoreAdministrator->create();
								$this->StoreAdministrator->save(
									array(
										'StoreAdministrator' => array(
											'store_id' => $storeId,
											'user_id' => $userId,
										)
									)
								);
							}
						}
					}
					else
					{
						throw new Exception('Login failed.');
					}
				}
				
				
				// Sets the logged user data.
				
				$this->loggedUser = $user;
			}
			catch (FacebookApiException $e)
			{
				$this->Auth->logout();
				$this->redirect(array('controller' => 'users', 'action' => 'login', 'admin' => false, '?' => array('source' => $this->here)), null, true);
			}
			catch (Exception $e)
			{
				$this->Auth->logout();
				$this->redirect(array('controller' => 'users', 'action' => 'login', 'admin' => false, '?' => array('source' => $this->here)), null, true);
			}
		}
		
		
		// Sets the Facebook login URL.
		
		$this->set('fb_login_url', $this->FB->getLoginUrl(array('scope' => Configure::read('Facebook.Permissions'), 'redirect_uri' => Configure::read('Facebook.redirect') . env('REQUEST_URI'))));
		
		
		parent::beforeFilter();
	}
	
	
/**
 * Called before the controller action view be render.
 *
 */
	public function beforeRender()
	{
		if (!isset($this->facebookFriends))
		{
			$this->facebookFriends = $this->Session->check('Facebook.getFriends');
		}
		
		$this->set('logged_user', $this->loggedUser);
		$this->set('facebook_friends', $this->facebookFriends);
		$this->set('fb_access_token', $this->FB->getAccessToken());
		$this->set('is_robot', $this->isRobot);
		$this->set('is_ajax', $this->RequestHandler->isAjax());
		
		$this->set('__version__', self::getVersion());
			
		parent::beforeRender();
	}
	
	
	
/**
 * Adds the email to be sent on the queue.
 *
 */
	protected function __sendEmail($from, $to, $replyTo, $subject, $template, $layout = 'default', $vars = array(), $format = 'both', $priority = 0)
	{
		$data = array(
			'EmailQueue' => array(
				'from' => $from,
				'to' => $to,
				'replyto' => $replyTo,
				'subject' => $subject,
				'template' => $template,
				'layout' => $layout,
				'vars' => serialize($vars),
				'format' => $format,
				'priority' => $priority,
			)
		);
		
		
		$this->EmailQueue->create();
		
		if ($this->EmailQueue->save($data))
		{
			return true;
		}
		
		
		return false;
	}
	
	
		
/**
 * Gets the model related to a specific url.
 *
 * @return array
 */
	protected function __urlToModel()
	{
		// Data to be returned
		
		$return = array();
		
		
		// Checks if the URL to be parsed was specified.
		
		if (!empty($this->request->query['url']))
		{
			// Parse URL.
		
			$url = $this->request->query['url'];
			
			$path = str_ireplace(FULL_BASE_URL, '', $this->request->query['url']);
			$path_parsed = Router::parse($path);
			
			
			// Checks if has controller and action.
			if (!empty($path_parsed['controller']) && !empty($path_parsed['action']))
			{
				// Checks the parsed URL kind.
			
				if (strtolower($path_parsed['controller']) == 'products' && strtolower($path_parsed['action']) == 'view' && !empty($path_parsed['product']))
				{
					// It's a product URL.
					
					$product = $this->__product($path_parsed['product']);
					
					if (!empty($product))
					{
						$return =  array(
							'id' => $product['StoreProduct']['id'],
							'model' => 'StoreProduct',
							'hash' => $product['StoreProduct']['hash']
						);
					}
				}
				else if (strtolower($path_parsed['controller']) == 'stores' && strtolower($path_parsed['action']) == 'view' && !empty($path_parsed['store']))
				{
					// It's a store URL.
					
					$store = $this->__store($path_parsed['store']);
					
					if (!empty($store))
					{
						$return = array(
							'id' => $store['Store']['id'],
							'model' => 'Store',
							'hash' => $store['Store']['hash']
						);
					}
				}
			}
		}
		
		
		return $return;
	}
	
	
/**
 * Checks the specified store.
 *
 * @param mixed $store
 * @return array
 */
	protected function __store($store)
	{
		// Checks the type of the variable $store to apply the most appropriate conditions.
		
		$storeConditions = array('Store.is_deleted' => false);
		
		if (is_numeric($store))
		{
			$storeConditions['Store.id'] = $store;
		}
		else
		{
			$storeConditions['OR'] = array();
			$storeConditions['OR']['Store.hash'] = $store;
			$storeConditions['OR']['Store.slug'] = $store;
			
			
			// Checks if the store ID is in url.
			
			$storeExploded = explode('-', $store);
			
			if (is_array($storeExploded) && (!empty($storeExploded[0]) && is_numeric($storeExploded[0])))
			{
				$storeConditions['OR']['Store.id'] = $storeExploded[0];
			}
		}
		
		
		// Search the store applying the conditions.
		
		$store = $this->Store->find('first',
			array(
				'contain' => array(
					'Address',
					'Phone',
					'BankAccount',
					'PaypalAccount',
					'FacebookPage',
					'Banner',
					'User' => array(
						'FacebookUser'
					),
					
				),
				'conditions' => $storeConditions
			)
		);

		if ($store)
		{
			$store['Store']['totalFollowers'] = $this->Store->getTotalFollowers($store['Store']['id']);
		
				
			if ($store['Store']['is_personal'] == 1) {
				$url = Configure::read("baseUrl") . '/stores/' . $store['Store']['slug'] ;
				$appUrl = Configure::read("appUrl") . '/?app_data=/stores/' . $store['Store']['slug'];
				
				$opengraphId = $store['User']['FacebookUser']['fb_user_id'];
				
			}
			else
			{
				$url = Configure::read("baseUrl") . '/fp/' . $store['Store']['slug'] ;
				
				if ( !isset($store['FacebookPage']) && !isset($store['FacebookPage']['link']) )
				{
					$appUrl = Configure::read("appUrl") . '/?&app_data=/fp/' . $store['Store']['slug'] ;
				}
				else
				{
					$appUrl = $store['FacebookPage']['link'] . '&app_data=/fp/' . $store['Store']['slug'];
				}
			
				$opengraphId = $store['FacebookPage']['fb_page_id'];
			}
		
		
			$store['Store']['url'] = $url;
			$store['Store']['appUrl'] = $appUrl;
			
			$store['Store']['pictureUrl'] = 'https://graph.facebook.com/'.$opengraphId.'/picture';
		}
		

		return $store;
	}
	
	
/**
 * Checks the specified product.
 *
 * @param mixed $product
 * @return array
 */
	protected function __product($product)
	{
		// Checks the type of the variable $product to apply the most appropriate conditions.
		
		$productConditions = array();
		
		if (is_numeric($product))
		{
			$productConditions['StoreProduct.id'] = $product;
		}
		else
		{
			$productConditions['OR'] = array();
			$productConditions['OR']['StoreProduct.hash'] = $product;
			$productConditions['OR']['StoreProduct.slug'] = $product;
			
			
			// Checks if the product ID is in url.
			
			$productExploded = explode('-', $product);
			
			if (is_array($productExploded) && (!empty($productExploded[0]) && is_numeric($productExploded[0])))
			{
				$productConditions['OR']['StoreProduct.id'] = $productExploded[0];
			}
		}
		
		
		// Search the product applying the conditions.
		
		$product = $this->StoreProduct->find('first',
			array(
				'contain' => array(
					'Product' => array(
						'Photo',
						'Category' => array(
							'ParentCategory'
						)
					),
					'Store' => array(
						'FacebookPage',
						'User' => array(
							'FacebookUser'
						)
					)
				),
				'conditions' => $productConditions
			)
		);
		
		$store = $this->__store($product['Store']['hash']);
		$product['Store'] = $store['Store'];
		
		if ($product['Store']['is_personal'] == 1) {
				$url = Configure::read('baseUrl') . '/products/' . $product['StoreProduct']['slug'];
				$appUrl = Configure::read('appUrl') . '/?app_data=/products/' . $product['StoreProduct']['slug'];
				
		}
		else
		{
				$url = $store['Store']['url'] . '/' . $product['StoreProduct']['slug'];
				$appUrl = $store['Store']['appUrl'] . '/' . $product['StoreProduct']['slug'];
				
		}
		
		$product['StoreProduct']['url'] = $url;
		$product['StoreProduct']['appUrl'] = $appUrl;

		return $product;
	}
	
	
/**
 * Checks the specified category.
 *
 * @param mixed $category
 * @return array
 */
	protected function __category($category)
	{
		// Checks the type of the variable $category to apply the most appropriate conditions.
		
		$categoryConditions = array('Category.is_deleted' => false);
		
		if (is_numeric($category))
		{
			$categoryConditions['Category.id'] = $category;
		}
		else
		{
			$categoryConditions['OR'] = array();
			$categoryConditions['OR']['Category.hash'] = $category;
			$categoryConditions['OR']['Category.slug'] = $category;
			
			
			// Checks if the category ID is in url.
			
			$categoryExploded = explode('-', $category);
			
			if (is_array($categoryExploded) && (!empty($categoryExploded[0]) && is_numeric($categoryExploded[0])))
			{
				$categoryConditions['OR']['Category.id'] = $categoryExploded[0];
			}
		}
		
		
		// Search the category applying the conditions.
		
		$category = $this->Category->find('first',
			array(
				'contain' => array(
					'ParentCategory',
					'ChildCategory'
				),
				'conditions' => $categoryConditions
			)
		);
		
		
		return $category;
	}
	
	
/**
 * Checks the specified transaction.
 *
 * @param mixed $transaction
 * @return array
 */
	protected function __transaction($transaction)
	{
		// Checks the type of the variable $transaction to apply the most appropriate conditions.
		
		$transactionConditions = array();
		
		if (is_numeric($transaction))
		{
			$transactionConditions['Transaction.id'] = $transaction;
		}
		else
		{
			$transactionConditions['Transaction.hash'] = $transaction;
		}
		
		
		// Search the transaction applying the conditions.
		
		$transaction = $this->Transaction->find('first',
			array(
				'contain' => array(
					'Payment',
					'PurchaseQualification',
					'SaleQualification',
					'Delivery' => array(
						'Address'
					),
					'Buyer' => array(
						'FacebookUser'
					),
					'Store' => array(
						'PaypalAccount',
						'BankAccount',
						'FacebookPage',
						'User' => array(
							'FacebookUser'
						)
					),
					'StoreProduct' => array(
						'Product' => array(
							'Photo',
							'Category'
						)
					)
				),
				'conditions' => $transactionConditions
			)
		);
				
			
		return $transaction;
	}
	
	
/**
 * Checks if the user has permission to manage the specified store.
 *
 * @param mixed $store
 * @return array
 */
	protected function __administeredStore($store)
	{
		// Checks if the specified store exists.
		
		$store = $this->__store($store);
		
		if (!empty($store))
		{
			// Checks if the user has permission to manage.
			
			$administrator = $this->StoreAdministrator->find('first',
				array(
					'conditions' => array(
						'StoreAdministrator.store_id' => $store['Store']['id'],
						'StoreAdministrator.user_id' => $this->loggedUser['User']['id'],
						'StoreAdministrator.is_deleted' => false
					)
				)
			);
			
			if (empty($administrator))
			{
				$store = array();
			}
		}
		
		
		return $store;
	}
	
	
/**
 * Checks if the specified transaction belongs to any administered store.
 *
 * @param mixed $transaction
 * @param mixed $store
 * @return array
 */
	protected function __administeredStoreTransaction($transaction, $store = null)
	{
		// Checks if the specified transaction exists.
	
		$transaction = $this->__transaction($transaction);
		
		if (!empty($transaction))
		{
			// Checks if the transaction belongs to any administered store.
		
			$administrator = $this->StoreAdministrator->find('first',
				array(
					'conditions' => array(
						'StoreAdministrator.store_id' => $transaction['Store']['id'],
						'StoreAdministrator.user_id' => $this->loggedUser['User']['id'],
						'StoreAdministrator.is_deleted' => false
					)
				)
			);
			
			if (!empty($administrator))
			{
				// If a store has been specified, check if it's the same of the transaction.
			
				if (!empty($store))
				{
					$store = $this->__administeredStore($store);
					
					if (empty($store) || (!empty($store) && $store['Store']['id'] != $transaction['Store']['id']))
					{
						$transaction = array();
						$store = array();
					}
				}
				else
				{
					$store = $this->__administeredStore($transaction['Store']['id']);
				}
			}
			else
			{
				$transaction = array();
				$store = array();
			}
		}
		else
		{
			$store = array();
		}
		
		
		return array($transaction, $store);
	}
	
	
/**
 * Checks if the specified product belongs to any administered store.
 *
 * @param mixed $product
 * @param mixed $store
 * @return array
 */
	protected function __administeredStoreProduct($product, $store = null)
	{
		// Checks if the specified product exists.
		
		$product = $this->__product($product);
		
		if (!empty($product))
		{
			// Checks if the product belongs to any administered store.
		
			$administrator = $this->StoreAdministrator->find('first',
				array(
					'conditions' => array(
						'StoreAdministrator.store_id' => $product['Store']['id'],
						'StoreAdministrator.user_id' => $this->loggedUser['User']['id'],
						'StoreAdministrator.is_deleted' => false
					)
				)
			);
			
			if (!empty($administrator))
			{
				// If a store has been specified, check if it's the same of the product.
			
				if (!empty($store))
				{
					$store = $this->__administeredStore($store);
					
					if (empty($store) || (!empty($store) && $store['Store']['id'] != $product['Store']['id']))
					{
						$product = array();
						$store = array();
					}
				}
				else
				{
					$store = $this->__administeredStore($product['Store']['id']);
				}
			}
			else
			{
				$product = array();
				$store = array();
			}
		}
		else
		{
			$store = array();
		}
		
		
		return array($product, $store);
	}
	
	
/**
 * Checks if the specified store exists and belongs to a fanpage.
 *
 * @param mixed $store
 * @return array
 */
	protected function __fanpageStore($store)
	{
		// Checks if the specified store exists.
		
		$store = $this->__store($store);
	
		if (empty($store['FacebookPage']['id']))
		{
			$store = array();
		}
		
		
		return $store;
	}
	
	
/**
 * Checks if the specified product exists and belongs to a fanpage store.
 *
 * @param mixed $product
 * @param mixed $store
 * @return array
 */
	protected function __fanpageStoreProduct($product, $store = null)
	{
		// Checks if the specified product exists.
		
		$product = $this->__product($product);
				
		if (!empty($product))
		{
			// If a store has been specified, check if it's the same of the product.
			
			if (!empty($store))
			{
				$store = $this->__fanpageStore($store);
				
				if (!empty($store) && $store['Store']['id'] != $product['Store']['id'])
				{
					$product = array();
					$store = array();
				}
			}
			else
			{
				$store = $this->__fanpageStore($product['Store']['id']);
			}
		}
		else
		{
			$store = array();
		}
		
		if (empty($store))
		{
			$product = array();
		}
				
		
		return array($product, $store);
	}
	
	
/**
 * Checks if the Facebook manage_pages permisson was granted.
 *
 * @return boolean
 */
	protected function __facebookFanpagePermission()
	{
		// Indicates if the permission was granted.
		
		$granted = false;
		
		
		// Stores that user is administrator.
		
		$grantedStores = array();
		
		
		// Communicates to Paypal.
		
		try
		{
			// Checks if the permission was granted.
			
			$permission = $this->FB->api(
				array(
					'method' => 'fql.query',
					'query' => 'SELECT manage_pages FROM permissions WHERE uid = me()'
				)
			);
			
			if (isset($permission[0]['manage_pages']))
			{
				$granted = $permission[0]['manage_pages'];
				
				if ($granted)
				{
					// Loads the administered Facebook pages.
					
					$fb_pages = $this->FB->api('/me/accounts');
					
					if (!empty($fb_pages['data']))
					{
						foreach ($fb_pages['data'] as $key => $value)
						{
							// Checks if it's a valid page.
							
							if (isset($value['perms']) && strcasecmp($value['category'], 'application') != 0)
							{
								// Checks if the user has necessary permissions.
							
								if (in_array('EDIT_PROFILE', $value['perms']) && in_array('CREATE_CONTENT', $value['perms']))
								{
									// Checks if the page is already connected with a store.
									
									$store = $this->Store->find('first',
										array(
											'conditions' => array(
												'FacebookPage.fb_page_id' => $value['id']
											)
										)
									);
									
									if (!empty($store))
									{
										$grantedStores[] = $store['Store']['id'];
										
										if ($store['Store']['is_deleted'])
										{
											$this->Store->id = 	$store['Store']['id'];
											$this->Store->saveField('is_deleted', false);
											
											$this->Store->FacebookPage->id = $store['FacebookPage']['id'];
											$this->Store->FacebookPage->saveField('is_deleted', false);
										}
									}
									else
									{
										// Creates a new store entry.
									
										$data = array(
											'Store' => array(
												'user_id' => 0,
												'title' => $value['name'],
												'is_personal' => false,
												'is_enabled' => false
											),
											
											'FacebookPage' => array(
												'fb_page_id' => $value['id'],
												'title' => $value['name'],
												'category' => $value['category'],
												'is_installed' => false
											)
										);
										
										
										$this->Store->create();
										
										if ($this->Store->saveAll($data))
										{
											$storeId = $this->Store->getLastInsertId();
											$store = $this->__store($storeId);
											
											$grantedStores[] = $store['Store']['id'];
										}
									}
									
									
									// Grants user the store administration.
									
									if (!empty($store))
									{
										// Checks if the user is already administrator.
										
										$administrator = $this->StoreAdministrator->find('first',
											array(
												'conditions' => array(
													'StoreAdministrator.store_id' => $store['Store']['id'],
													'StoreAdministrator.user_id' => $this->loggedUser['User']['id']
												)
											)
										);
										
										if (!empty($administrator))
										{
											if ($administrator['StoreAdministrator']['is_deleted'])
											{
												$this->StoreAdministrator->id = $administrator['StoreAdministrator']['id'];
												$this->StoreAdministrator->saveField('is_deleted', false);
											}
										}
										else
										{
											$this->StoreAdministrator->create();
											$this->StoreAdministrator->save(
												array(
													'StoreAdministrator' => array(
														'store_id' => $store['Store']['id'],
														'user_id' => $this->loggedUser['User']['id'],
													)
												)
											);
										}
										
										
										// Checks if the application is still installed on Facebook page.
										
										if (!empty($store['FacebookPage']['id']))
										{
											// Loads the Facebook page.
					
											$fb_page = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'], 'GET', array('fields' => array('access_token', 'link')));
											
											if (!empty($fb_page['access_token']))
											{
												// Gets the application tab.
													
												$fb_page_tab = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'] . '/tabs/' . Configure::read('Facebook.AppId'), 'GET', array('access_token' => $fb_page['access_token']));
												
												if (!empty($fb_page_tab['data'][0]['id']))
												{
													if (strcasecmp($fb_page_tab['data'][0]['link'], $store['FacebookPage']['link']) != 0)
													{
														// Updates the application Facebook page link.
														
														$this->Store->FacebookPage->id = $store['FacebookPage']['id'];
														$this->Store->FacebookPage->saveField('link', $fb_page_tab['data'][0]['link']);
													}
												}
												else
												{
													// Updates the store record.
													
													$this->Store->id = $store['Store']['id'];
													$this->Store->saveField('is_enabled', false);
													
													$this->Store->FacebookPage->id = $store['FacebookPage']['id'];
													$this->Store->FacebookPage->saveField('is_installed', false);
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		catch(FacebookApiException $e)
		{
		}
		
		
		// Gets the user administered stores.
		
		$administeredStores = $this->StoreAdministrator->find('all',
			array(
				'conditions' => array(
					'Store.user_id <>' => $this->loggedUser['User']['id'],
					'StoreAdministrator.user_id' => $this->loggedUser['User']['id'],
					'StoreAdministrator.is_deleted' => false
				)
			)
		);
		
		if ($granted !== false) {
			foreach ($administeredStores as $key => $value)
			{
				if (!in_array($value['Store']['id'], $grantedStores))
				{
					$this->StoreAdministrator->id = $value['StoreAdministrator']['id'];
					$this->StoreAdministrator->saveField('is_deleted', true);
				}
			}
		}
		
		
		return $granted;
	}
	
	public static function getVersion() {
		
		$varName = '__version__';
		if (self::$__version__ !== null)
		{
			return self::$__version__;
			
		}
		else if (isset($GLOBALS[$varName]) && $GLOBALS[$varName] !== null)
		{
			self::$__version__ = $GLOBALS[$varName];
			return $GLOBALS[$varName];
			
		}
		else if (apc_fetch($varName) !== false)
		{
			$GLOBALS[$varName] = apc_fetch($varName);
			return apc_fetch($varName);
		}
		else
		{
			$log = explode("\n",file_get_contents(dirname(dirname(__FILE__)) . DS . '.git' . DS . 'logs' . DS . 'HEAD'));
			$gitVersion =  substr($log[count($log)-2], 0, 10);
			
			$version = $gitVersion;
			apc_add($varName, $version, 3600);
			
			return $version;
		}
		
	}
	
	
	public function checkHasPaypal() {
		if (!isset($this->loggedUser['User'])) {
			return false;
		}

		$userId = $this->loggedUser['User']['id'];
		
		$stores = $this->StoreAdministrator->find('all',
			array(
				'conditions' => array(
					'StoreAdministrator.user_id' => $userId
				),
			)
		);
		
		$paypalEmail = null;
		$hasAnyEmptyPaypal = null;
		
		foreach ($stores as $store) {
			$store = $this->Store->find('first',array('conditions'=> array('Store.id' => $store['Store']['id'])));
			
			if (isset($store['PaypalAccount']) && $store['PaypalAccount']['email'] != null && $paypalEmail === null) {
				$paypalEmail = $store['PaypalAccount']['email'];

			} else
			if (isset($store['Store']['PaypalAccount']) && $store['Store']['PaypalAccount']['email'] = '') {
				$hasAnyEmptyPaypal = true;
				
			}
		}
		
		if ($hasAnyEmptyPaypal && $paypalEmail) {
			$this->fillPaypalEmail($paypalEmail);
		}
		
		return  $paypalEmail !== null ? true : false;
	}	
	
	/**
	 * Spread paypal email throuth stores that have no paypal.
	 */
	
	public function fillPaypalEmail($email = null) {
		if (!isset($this->loggedUser['User'])) {
			return false;
		}

		$userId = $this->loggedUser['User']['id'];
		
		$stores = $this->StoreAdministrator->find('all',
			array(
				'conditions' => array(
					'StoreAdministrator.user_id' => $userId
				),
			)
		);
		
		if ($email === null) {
			foreach ($stores  as $store) {
				$store = $this->Store->find('first',array('conditions'=> array('Store.id' => $store['Store']['id'])));
				
				if ($store['Store']['PaypalAccount']['email'] != '' && $email === null) {
					$email = $store['Store']['PaypalAccount']['email'];
					break;
				}
			}
		}
		
		
		if ($email == null) {
			return false;
		}
		
		foreach ($stores as $store) {
			
			if (!isset($store['Store']['PaypalAccount']) || $store['Store']['PaypalAccount']['email'] == '') {
				#$store = $this->Store->find('first',array('conditions'=> array('Store.id' => $store['Store']['id'])));
				#$storeId = $store['Store']['id'];
			
				#$paypalAccount = $store['Store']['PaypalAccount'];
			
				$paypalAccount['model'] = 'Store';
				$paypalAccount['foreign_key'] = $store['Store']['id'];
				$paypalAccount['email'] = $email;
			
				$this->PaypalAccount->create();
				$this->PaypalAccount->saveAll($paypalAccount);
			}
		}

		return true;
	}

	
	/*
	 * Check if the informed email can receive payments on paypal
	 */
	public function checkIfPaypalEmailIsBillable($email = null) {
		
		if ($email == null) {
			return null;
			
		}
		
		
		$paypayCreateAccount = curl_init();
		$cURL_header = array(
					'X-PAYPAL-APPLICATION-ID: ' . Configure::read('Paypal.API.appId'),
					'X-PAYPAL-SECURITY-USERID:' . Configure::read('Paypal.API.username'),
					'X-PAYPAL-SECURITY-PASSWORD:' . Configure::read('Paypal.API.password'),
					'X-PAYPAL-SECURITY-SIGNATURE:' . Configure::read('Paypal.API.signature'),
					'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
					'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
		);
				
		curl_setopt($paypayCreateAccount, CURLOPT_URL, Configure::read('Paypal.API.endpoint') . '/AdaptivePayments/Pay');
		curl_setopt($paypayCreateAccount, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($paypayCreateAccount, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($paypayCreateAccount, CURLOPT_POST, 1);
		curl_setopt($paypayCreateAccount, CURLOPT_HTTPHEADER, $cURL_header);
				
			
		$hash = 'checkValidEmail-' . $email .'-'. sha1(microtime(true).mt_rand(10000,90000));
		$newPaypalData = array(
							'actionType' => 'PAY',
							'currencyCode' => 'BRL',
							'feesPayer' => 'SECONDARYONLY',
							'cancelUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'cancel', 'admin' => false, 'payment' => $hash), true),
							'returnUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'finish', 'admin' => false, 'payment' => $hash), true),
							'ipnNotificationUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'notification', 'admin' => false, 'payment' => $hash, 'ext' => 'json'), true),
							'trackingId' => $hash,
							'requestEnvelope.errorLanguage' => 'pt_BR',
							'receiverList.receiver(0).email' => $email,
							'receiverList.receiver(0).amount' => 1,
							'receiverList.receiver(0).primary' => true,
							'receiverList.receiver(1).email' => Configure::read('Paypal.payment.BazzApp.email'),
							'receiverList.receiver(1).amount' => 1,
							'receiverList.receiver(1).primary' => false
					);

		curl_setopt($paypayCreateAccount, CURLOPT_POSTFIELDS, http_build_query($newPaypalData));
				$paypayCreateAccount_response = json_decode(curl_exec($paypayCreateAccount));
				curl_close($paypayCreateAccount);

				if (isset($paypayCreateAccount_response->responseEnvelope->ack) && $paypayCreateAccount_response->responseEnvelope->ack == 'Success') {
					return true;
					
				} else {
					return false;
					
				}
	}
	
	
	/*
	 * Checks if the store has no address
	 * 
	 * $store multiple, empty - int - array of int
	 */
	public function checkStoreHasNoAddress($storesId = null) {
		
		if ($storesId == null || (!is_int($storesId) && !is_array($storesId))) {
			return true;
			
		} else if (is_int($storesId)) {
			
			$storesId = array($storesId);
		}

		$hasAddress = false;
		foreach ($storesId as $storeId) {
			
			 $store = $this->Store->find('first',array('conditions'=> array('Store.id' => $storeId)));
			 if ($store['Address']['id'] !== null) {
			 	$hasAddress = true;
			 	break;
			 }
		}

		return  $hasAddress == true ? false : true;
	}		
	
	
	
	
	
	/**
 	* Return the premium stores
 	*
 	*/
   	public function getPremiumStores($limit = 10, $totalProducts = 15)
	{
	
		$premiumStore = array();
		$premiumStoreDB = $this->Store->find('all',
			array(
				'fields' => array('Store.hash','Store.id'),
				'conditions' => array(
					'Store.highlight_level' => self::HIGHLIGHT_YES,
					'Store.is_deleted' => false
				),
				'order' => 'RAND()',
				'limit' => $limit
			)
		);
		
		
		foreach ($premiumStoreDB as $store) {
			
			$products = $this->getPremiumProducts($totalProducts, $store['Store']['id']);
			$store = $this->__store($store['Store']['hash']);
			$store['products'] = $products;

			$premiumStore[] = $store;
		} 

		return $premiumStore;
	}

	
	/**
 	* Return the premium products
 	*
	*/
	public function getPremiumProducts($limit = 15, $store_id = null)
	{
		$premiumProduct = array();
		
		if ($store_id !== null) {
			$conditions = array(
							'StoreProduct.highlight_level IN ('.self::HIGHLIGHT_JUST_STORE.','. self::HIGHLIGHT_STORE_TOO.') AND 1=' => 1,
							'StoreProduct.store_id' => $store_id,
							'(Product.quantity - Product.quantity_sold) >' => 0,
							'Product.is_deleted' => false
							);
							
		} else {
			$conditions = array(
							'StoreProduct.highlight_level IN ('.self::HIGHLIGHT_YES .','. self::HIGHLIGHT_STORE_TOO.') AND 1=' => 1,
							'(Product.quantity - Product.quantity_sold) >' => 0,
							'Product.is_deleted' => false
							);
			
		}
		$premiumProductDB = $this->StoreProduct->find('all',
			array(
				'fields' => 'StoreProduct.hash',
				'conditions' => $conditions,
				'limit' => $limit
			)
		);
		foreach ($premiumProductDB as $product) {
			$premiumProduct[] = $this->__product($product['StoreProduct']['hash']);
		} 

		return $premiumProduct;
	}	
	
		
}
