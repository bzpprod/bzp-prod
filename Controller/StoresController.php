<?php
App::uses('AppController', 'Controller');

class StoresController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Stores';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Store', 'StoreAdministrator', 'Follow');
	
/**	
 * Controller actions for which authorization is not required.
 *
 * @var array
 */
	public $allowedActions = array('view', 'following');
	
/**	
 * Paginator settings.
 *
 * @var array
 */
	public $paginate = array(
		'StoreProduct' => array(
			'contain' => array(
				'Product' => array(
					'Photo',
					'Category'
				),
				'Store' => array(
					'User' => array(
						'FacebookUser'
					)
				)
			),
			'order' => array(
				'Product.created' => 'desc'
			),
			'limit' => 16
		)
	);
	
	
	const HIGHLIGHT_NO			= 0;
	const HIGHLIGHT_YES			= 1;
	
/**
 * Lists the specified store products.
 *
 * @param string $store
 */
	public function view($store = null)
	{
		// Apply 'store' layout.
		
		$this->layout = 'store';
		
		
		// Checks if the specified store exists.
		
		$store = $this->__store($store);
		
		if (empty($store))
		{
			$this->redirect('/', null, true);
		}else{
			$store['Store']['totalFollowers'] = $this->StoreProduct->getTotalFollowers($store['Store']['id']);				
		}
		
		
		// Finds the available store products.
		
		$products = $this->Paginate('StoreProduct',
			array(
				'StoreProduct.store_id' => $store['Store']['id'],
				'(Product.quantity - Product.quantity_sold) >' => 0,
				'Product.is_deleted' => false,
				'StoreProduct.is_deleted' => false
			)
		);
		
		foreach ($products as $k=>$v) {
			$products[$k] = $this->__product($v['StoreProduct']['hash']);
		}
		$this->set(compact('store', 'products'));
		$this->set('title_for_layout', $store['Store']['title']);
	}
	
	
/**
 * Follow the specified store.
 *
 * @param string $store
 */
	public function follow($store)
	{
		// Checks if the specified store exists.
		
		$store = $this->__store($store);
		
		if (!empty($store))
		{
			// Checks if the user is already following the store.
		
			$follow = $this->Follow->find('first',
				array(
					'conditions' => array(
						'Follow.model' => 'Store',
						'Follow.foreign_key' => $store['Store']['id'],
						'Follow.user_id' => $this->loggedUser['User']['id']
					)
				)
			);
			
			if (!empty($follow))
			{
				 $this->Follow->id = $follow['Follow']['id'];
				 $this->Follow->saveField('is_deleted', false);
			}
			else
			{
				$this->Follow->save(
					array(
						'Follow' => array(
							'model' => 'Store',
							'foreign_key' => $store['Store']['id'],
							'user_id' => $this->loggedUser['User']['id']
						)
					)
				);
				
				
				$loggedUserStore = $this->Store->find('first',
					array(
						'conditions' => array(
							'Store.user_id' => $this->loggedUser['User']['id'],
							'Store.is_personal' => true,
							'Store.is_deleted' => false
						)
					)
				);
				
				
				$vars = array('userName' => $this->loggedUser['User']['name'], 'userUrl' => Configure::read('baseUrl') . '/stores/' . $this->loggedUser['Store']['slug'], 'url' => Configure::read('baseUrl') . '/stores/' . $this->loggedUser['Store']['slug'], 'ownerName' => $store['User']['name']);
				$this->CommunicationQueue->sendStoreFollowed($store['User']['email'], $vars);
				$this->CommunicationQueue->sendStoreFollowedFacebookNotification($opengraphId, $vars);
			}
		}
		
		echo $this->following($store['Store']['slug']);
		exit;
	}
	
	
/**
 * Unfollow the specified store.
 *
 * @param string $store
 */
	public function unfollow($store)
	{
		// Checks if the specified store exists.
		
		$store = $this->__store($store);
		
		if (!empty($store))
		{
			$follow = $this->Follow->find('first',
				array(
					'conditions' => array(
						'Follow.model' => 'Store',
						'Follow.foreign_key' => $store['Store']['id'],
						'Follow.user_id' => $this->loggedUser['User']['id'],
						'Follow.is_deleted' => false
					)
				)
			);
			
			if (!empty($follow))
			{
				 $this->Follow->delete($follow['Follow']['id']);
			}
		}
		
		echo $this->following($store['Store']['slug']);
		exit;
	}
	
	
/**
 * Displays if the user is following the specified store.
 *
 * @param string $store
 */
	public function following($store)
	{
		// Checks if the specified store exists.
		
		$store = $this->__store($store);
		
		if (!empty($store) && !empty($this->loggedUser['User']['id']))
		{
			$follow = $this->Follow->find('first',
				array(
					'conditions' => array(
						'Follow.model' => 'Store',
						'Follow.foreign_key' => $store['Store']['id'],
						'Follow.user_id' => $this->loggedUser['User']['id'],
						'Follow.is_deleted' => false
					)
				)
			);
			
			if (!empty($follow))
			{
				$following = true;
			}
			else
			{
				$following = false;
			}
		}
		else
		{
			$following = false;
		}
		
		
		$this->set(compact('following'));
		$this->set('_serialize', array('following'));
		
		return $following;
	}
	
	
/**
 * Displays the products of administered store.
 *
 * @param string $store
 */
	public function admin_view($store = null)
	{
		// Apply 'store' layout.
		
//		$this->layout = 'store';
		$this->layout = 'admin';		
		
		
		// Gets the Facebook manage_pages permisson status.
		
		$fb_manage_pages = $this->__facebookFanpagePermission();
		
		
		// Checks if the user has administrator permissions to view the store.
		
		$store = $this->__administeredStore($store);
		
		if (empty($store))
		{
			// Gets the personal store.
			
			$store = $this->Store->find('first',
				array(
					'conditions' => array(
						'Store.user_id' => $this->loggedUser['User']['id'],
						'Store.is_personal' => true,
						'Store.is_deleted' => false
					)
				)
			);
		
			$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true, 'store' => $store['Store']['slug']), null, true);
		}else{
			$store['Store']['totalFollowers'] = $this->StoreProduct->getTotalFollowers($store['Store']['id']);				
		}

		
		
		// Finds the store available products and extract the products ids.
		
		$products = $this->Paginate('StoreProduct',
			array(
				'StoreProduct.store_id' => $store['Store']['id'],
				'(Product.quantity - Product.quantity_sold) >' => 0,
				'Product.is_deleted' => false,
				'StoreProduct.is_deleted' => false
			)
		);
		
		$productsIds = Set::extract('/StoreProduct/id', $products);
		
		
		// Gets the products according to extracted ids, but now formatted with the necessary data.
		
		$products = array();
		
		foreach ($productsIds as $key => $value)
		{
			$products[] = $this->__product($value);
		}
		
		
		$this->set(compact('store', 'products'));
		$this->set('title_for_layout', String::insert(__d('controller', 'Stores.admin_view.title'), array('store' => $store['Store']['title'])));
	}
	
	
/**
 * Displays the sold out products of administered store.
 *
 * @param string $store
 */
	public function admin_view_archive($store)
	{
		// Apply 'store' layout.
		
//		$this->layout = 'store';
		$this->layout = 'admin';		
		
		
		// Gets the Facebook manage_pages permisson status.
		
		$fb_manage_pages = $this->__facebookFanpagePermission();
		
		
		// Checks if the user has administrator permissions to view the store.
		
		$store = $this->__administeredStore($store);
		
		if (empty($store))
		{
			// Gets the personal store.
			
			$store = $this->Store->find('first',
				array(
					'conditions' => array(
						'Store.user_id' => $this->loggedUser['User']['id'],
						'Store.is_personal' => true,
						'Store.is_deleted' => false
					)
				)
			);
		
			$this->redirect(array('controller' => 'stores', 'action' => 'sold_out', 'admin' => true, 'store' => $store['Store']['slug']), null, true);
		}else{
			$store['Store']['totalFollowers'] = $this->StoreProduct->getTotalFollowers($store['Store']['id']);				
		}

		
		
		// Finds the store sold out products and extract the products ids.
		
		$products = $this->Paginate('StoreProduct',
			array(
				'StoreProduct.store_id' => $store['Store']['id'],
				'OR' => array(
					'(Product.quantity - Product.quantity_sold) <=' => 0,
					'Product.is_deleted' => true,
					'StoreProduct.is_deleted' => true
				)
			)
		);
		
		$productsIds = Set::extract('/StoreProduct/id', $products);
		
		
		// Gets the products according to extracted ids, but now formatted with the necessary data.
		
		$products = array();
		
		foreach ($productsIds as $key => $value)
		{
			$products[] = $this->__product($value);
		}
		
		
		$this->set(compact('store', 'products'));
		$this->set('title_for_layout', String::insert(__d('controller', 'Stores.admin_view_archive.title'), array('store' => $store['Store']['title'])));
	}
	
	
/**
 * Updates the specified store information.
 *
 * @param string $store
 */
	public function admin_edit($store)
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		
		
		// Gets the Facebook manage_pages permisson status.
		
		$fb_manage_pages = $this->__facebookFanpagePermission();
		
		
		// Checks if the user has administrator permissions to view the store.
		
		$store = $this->__administeredStore($store);
		
		if (empty($store))
		{
			// Gets the personal store.
			
			$store = $this->Store->find('first',
				array(
					'conditions' => array(
						'Store.user_id' => $this->loggedUser['User']['id'],
						'Store.is_personal' => true,
						'Store.is_deleted' => false
					)
				)
			);
		
			$this->redirect(array('controller' => 'stores', 'action' => 'edit', 'admin' => true, 'store' => $store['Store']['slug']), null, true);
		}
		
		
		// Checks if the store updated data was submitted.
		
		if (($this->request->is('post') || $this->request->is('put')) && (!empty($this->request->data['Store'])))
		{
			$clean_data = Sanitize::clean($this->request->data, array('encode' => false));
			
			$data = array(
				'Store' => $clean_data['Store'],
				'BankAccount' => $clean_data['BankAccount'],
				'PaypalAccount' => $clean_data['PaypalAccount'],
				'Address' => $clean_data['Address'],
				'Phone' => $clean_data['Phone']
			);
			
			$data['Store']['id'] = $store['Store']['id'];
			
			
			if (!empty($clean_data['Banner']['filename']['name']))
			{
				$data['Banner'] = $clean_data['Banner'];
			}
			
			
			if ($this->Store->saveAll($data, array('validate' => 'first')))
			{
				if (!empty($this->Store->Banner->id))
				{
					$this->Store->Banner->updateAll(
						array(
							'Banner.is_deleted' => true
						),
						array(
							'Banner.model' => 'StoreBanner',
							'Banner.foreign_key' => $store['Store']['id'],
							'Banner.id <>' => $this->Store->Banner->id
						)
					);
				}
				
				$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true, 'store' => $store['Store']['slug']), null, true);
			}
		}
		else
		{
			$this->request->data = $store;
		}
		
		
		$this->set(compact('store'));
		$this->set('title_for_layout', __d('controller', 'Stores.admin_edit.title'));
	}
	
	
/**
 * Installs the application on the Facebook store page.
 *
 */
	public function api_install()
	{
		// Checks if the store to be installed was specified.
	
		if (!empty($this->request->query['store']))
		{
			$stores = explode(',',$this->request->query['store']);
			
			foreach ($stores as $key => $value)
			{
				// Checks if the store exists and user has permission to manage it.
			
				$store = $this->__administeredStore($value);
				
				if (!empty($store['FacebookPage']['id']) && !$store['FacebookPage']['is_installed'])
				{
					try
					{				
						// Loads the Facebook page.
					
						$fb_page = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'], 'GET', array('fields' => array('access_token', 'link')));
						
						if (!empty($fb_page['access_token']))
						{
							// Install application tab.
							
							$installed = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'] . '/tabs', 'POST', array('access_token' => $fb_page['access_token'], 'app_id' => Configure::read('Facebook.AppId')));
							
							if ($installed)
							{
								// Updates the store record.
								
								$this->Store->id = $store['Store']['id'];
								$this->Store->saveField('is_enabled', true);
								
								$this->Store->FacebookPage->id = $store['FacebookPage']['id'];
								$this->Store->FacebookPage->saveField('is_installed', true);
								
								
								// Gets the application tab.
								
								$fb_page_tab = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'] . '/tabs/' . Configure::read('Facebook.AppId'), 'GET', array('access_token' => $fb_page['access_token']));
								
								if (!empty($fb_page_tab['data'][0]['id']))
								{
									// Updates the application Facebook page link.
									
									$this->Store->FacebookPage->id = $store['FacebookPage']['id'];
									$this->Store->FacebookPage->saveField('link', $fb_page_tab['data'][0]['link']);
									
									
									// Gets the installed applications.
									
									$fb_page_tabs = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'] . '/tabs', 'GET', array('access_token' => $fb_page['access_token']));
									
									if (!empty($fb_page_tabs['data']))
									{
										// Gets the application tab sorted by tab position and uninstall the application.
										
										$tabs = array();
										
										foreach ($fb_page_tabs['data'] as $key => $value)
										{
											if (!$value['is_permanent'] && (!empty($value['application']['id']) && $value['application']['id'] != Configure::read('Facebook.AppId')))
											{
												$tabs[$value['position']] = $value;
												
												$this->FB->api('/' . $value['id'], 'DELETE', array('access_token' => $fb_page['access_token']));
											}
										}
										
										
										// Installs back the removed apps.
										
										foreach ($tabs as $key => $value)
										{
											$installed = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'] . '/tabs', 'POST', array('access_token' => $fb_page['access_token'], 'app_id' => $value['application']['id']));
											
											if ($installed)
											{
												// Gets the installed application tab.
												
												$fb_page_tab = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'] . '/tabs/' . $value['application']['id'], 'GET', array('access_token' => $fb_page['access_token']));
												
												if (!empty($fb_page_tab['data'][0]['id']))
												{
													// Updates the tab information.
													
													$this->FB->api('/' . $fb_page_tab['data'][0]['id'], 'POST', array('access_token' => $fb_page['access_token'], 'custom_name' => $value['name'], 'custom_image_url' => $value['image_url'], 'is_non_connection_landing_tab' => $value['is_non_connection_landing_tab']));	
												}
											}
										}
									}
								}
							}
						}
					}
					catch (FacebookApiException $e)
					{
					}
				}
			}
		}
	
	
		// Gets the administered stores.
		
		$stores = $this->api_index();
		
		
		$this->set(compact('stores'));
		$this->set('_serialize', array('stores'));
		
		return $stores;
	}
	
	
/**
 * Displays the administered stores.
 *
 * @param string $store
 */
	public function api_index()
	{
		// Gets the administered stores and extracts the stores ids.
		
		$stores = $this->StoreAdministrator->find('all',
			array(
				'conditions' => array(
					'User.id' => $this->loggedUser['User']['id'],
					'StoreAdministrator.is_deleted' => false,
					'Store.is_enabled' => true,
					'Store.is_deleted' => false
				)
			)
		);
		
		$storesIds = Set::extract('/Store/id', $stores);
		
		// Gets the stores according to extracted ids, but now formatted with the necessary data.
		
		$stores = array();
		
		foreach ($storesIds as $key => $value)
		{
			$stores[] = $this->__store($value);
		}
		
		
		$this->set(compact('stores'));
		$this->set('_serialize', array('stores'));
		
		return $stores;
	}


}
