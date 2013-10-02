<?php
App::uses('AppController', 'Controller');


class ProductsController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Products';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('User', 'Product', 'ViewStoreProductCategory', 'Store', 'StoreProduct', 'Address', 'Category', 'Transaction', 'TransactionUpdate', 'Log', 'LogSearch', 'StoreFacebookPage','UserNotificationAlert', 'PaypalAccount', 'Phone', 'Payment', 'Like', 'Follow', 'Friend', 'FacebookUser');
	
/**
 * Controller actions for which authorization is not required.
 *
 * @var array
 */
	public $allowedActions = array('home', 'index', 'view', 'fanpage_home', 'fanpage_index', 'fanpage_view', 'paypalAdaptiveAccountAction');
	
/**
 * Paginator settings.
 *
 * @var array
 */
	public $paginate = array(
		'ViewStoreProductCategory' => array(
			'order' => array('ViewStoreProductCategory.created' => 'desc'),
			'limit' => 15
		)
	);
	
	
/**
 * Lists the products according to applied filter.
 *
 */
	public function home()
	{
		// Products list limit.
		$limit = 15;
		
		
		// Products list starting from a specific point.
		
		$offset = 0;
		
		if (!empty($this->request->query['offset']) && is_numeric($this->request->query['offset']))
		{
			$offset = $this->request->query['offset'];
		}
		
		
		// Generates the query according to selected filter.
		if (isset($this->request->query['vtype']))
		{
			$vtype = $this->request->query['vtype'];

			if($vtype == "store"){
				$this->set('premiumStore', $this->getPremiumStores());
			}else{
				$this->set('premiumProduct', $this->getPremiumProducts());
			}

		}
		else
		{
			$vtype = null;
			$this->set('premiumProduct', $this->getPremiumProducts());
		}
		
		switch ($vtype)
		{
			// List products
			default: 
			{
					
		if (isset($this->params['named']['filter']))
		{
			$filter = $this->params['named']['filter'];
		}
		else
		{
			$filter = null;
		}
		
		
		if ($filter !== null && $filter != '')
		{

			if (strpos('friends-like', $filter) === 0 && !empty($this->loggedUser['FacebookUser']['id']))
			{
				$title_for_layout = 'Products.home.title.friends_like';
				$filter = $filter.':'.$this->loggedUser['FacebookUser']['id'];
			}
			else if (strpos('friends-products', $filter) === 0 && !empty($this->loggedUser['FacebookUser']['id']))
			{
				$title_for_layout = 'Products.home.title.friends_products';
				$filter = $filter.':'.$this->loggedUser['FacebookUser']['id'];
			}
			else if (strpos('follow-stores', $filter) === 0 && !empty($this->loggedUser['User']['id']))
			{
				$title_for_layout = 'Products.home.title.follow_stores';
				$filter = $filter.':'.$this->loggedUser['User']['id'];
			}
			else
			{
				$title_for_layout = 'Products.home.title.created';
				
				if (strpos('cheaper', $filter) === 0)
				{
					$title_for_layout = 'Products.home.title.cheaper';
				}
				else if (strpos('expensive', $filter) === 0)
				{
					$title_for_layout = 'Products.home.title.expensive';
				}
				else if (strpos('likes', $filter) === 0)
				{
					$title_for_layout = 'Products.home.title.likes';
				}
			}
		}
		else
		{
			$title_for_layout = 'Products.home.title';
		}

		// Runs the generated query and extracts the products ids.
		$productsIds = $this->StoreProduct->findList($filter, $offset, $limit);
		
		
		// Gets the products according to extracted ids, but now formatted with the necessary data.
		
		$itens = array();
		
		foreach ($productsIds as $key => $value)
		{
			$itens[] = $this->__product($value);
		}
		
		
			} break;
					
			// List stores
			case 'store': {
				
				if (isset($this->params['named']['filter']))
				{
					$filter = $this->params['named']['filter'];
				}
				else
				{
					$filter = null;
				}
		
				
				if ($filter !== null)
				{
			
					switch ($filter)
					{
						case 'friends-like':
						{
							$title_for_layout = 'Products.home.title.friends_like';
							$filter = $filter.':'.$this->loggedUser['FacebookUser']['id'];
							
						} break;

						case 'friends-products':
						{
							$title_for_layout = 'Products.home.title.friends_products';
							$filter = $filter.':'.$this->loggedUser['FacebookUser']['id'];
														
						} break;

						case 'follow-stores':
						{
							$title_for_layout = 'Products.home.title.friends_products';
							$filter = $filter.':'.$this->loggedUser['User']['id'];
														
						} break;

						case 'cheaper':
						{
							$title_for_layout = 'Products.home.title.cheaper';
														
						} break;

						case 'expensive':
						{
							$title_for_layout = 'Products.home.title.expensive';
														
						} break;

						case 'likes':
						{
							$title_for_layout = 'Products.home.title.likes';
														
						} break;

						default:
						{
							$title_for_layout = 'Products.home.title.created';			
						} break;
					}
				}	
				else
				{
					$title_for_layout = 'Products.home.title';
				}

		
		
				// Runs the generated query and extracts the products ids.
				$storesIds = $this->Store->findHomeList($filter, $offset, $limit);
		
		
				// Gets the products according to extracted ids, but now formatted with the necessary data.
		
				$itens = array();
				foreach ($storesIds as $key => $value)
				{
					$products = $this->StoreProduct->find('all',array('limit'=>10, 'conditions'=>array('StoreProduct.store_id'=>$value)));
					$productsOnSteroids = array();
					
					foreach ($products as $product)
					{
						$productsOnSteroids[] = $this->__product($product['StoreProduct']['id']);
					}
					
					$store = $this->__store($value);
					$store['Store']['isFollowing'] = $this->requestAction(array('controller' => 'stores', 'action' => 'following', 'store' => $store['Store']['slug']));
					
					$store['Store']['follow']['action'] = ($store['Store']['isFollowing'] ? 'un': '') . 'follow';
					$store['Store']['follow']['text'] = __d('view','Layouts.default.controls.' . $store['Store']['follow']['action']);
					$store['Store']['follow']['link'] = Router::url(array('controller' => 'stores', 'action' => $store['Store']['follow']['action'], 'store' => $store['Store']['slug']));				
					$store['Store']['follow']['value'] = $store['Store']['follow']['text'];
					$store['Store']['totalFollowers'] = $this->StoreProduct->getTotalFollowers($store['Store']['id']);
					
					$store['products'] = $productsOnSteroids;
					
					$itens[] = $store;
				}
				
			} break;
		}
		
		
		
		$this->set(compact('intens'));
		// legacy view variable
		$products = $itens;
		$this->set(compact('products'));
		
		$this->set('title_for_layout', __d('controller', $title_for_layout));
	}
	
	
/**
 * Lists the products according to search term and/or selected category.
 *
 */
	public function index()
	{
		$filter = (isset($this->request->params['named']['filter']) ? $this->request->params['named']['filter'] : null);
		$page = (isset($this->request->params['named']['page']) ? $this->request->params['named']['page'] : 0);
		$category = (isset($this->request->params['named']['category']) ? $this->__category($this->request->params['named']['category']) : null);
		$search = (isset($this->request->data['Search']['search']) ? $this->request->data['Search']['search'] : null);

		// Products list limit.
		$limit = 15;
		
		
		// Products list starting from a specific point.
		
		$offset = 0;
		
		if (!empty($this->request->query['offset']) && is_numeric($this->request->query['offset']))
		{
			$offset = $this->request->query['offset'];
		}
		
		
		
		if ($filter !== null)
				{
			
					switch ($filter)
					{
						case 'friends-like':
						{
							$title_for_layout = 'Products.home.title.friends_like';
							$filter = $filter.':'.$this->loggedUser['FacebookUser']['id'];
							
						} break;

						case 'friends-products':
						{
							$title_for_layout = 'Products.home.title.friends_products';
							$filter = $filter.':'.$this->loggedUser['FacebookUser']['id'];
														
						} break;

						case 'follow-stores':
						{
							$title_for_layout = 'Products.home.title.friends_products';
							$filter = $filter.':'.$this->loggedUser['User']['id'];
														
						} break;

						case 'cheaper':
						{
							$title_for_layout = 'Products.home.title.cheaper';
														
						} break;

						case 'expensive':
						{
							$title_for_layout = 'Products.home.title.expensive';
														
						} break;

						case 'likes':
						{
							$title_for_layout = 'Products.home.title.likes';
														
						} break;

						default:
						{
							$title_for_layout = 'Products.home.title.created';			
						} break;
					}
				}	
				else
				{
					$title_for_layout = 'Products.home.title';
				}
		
		if ($search === null && isset($this->request->params['named']['search']))
		{
			$search = $this->request->params['named']['search'];
		}
		if ($search !== null)
		{
			$this->request->params['named']['search'] = $search;
		}
		
		$productsIds = $this->StoreProduct->findList($filter, $offset, $limit, $category, $search);
		
		// Saves the search for creating the sitemap
		// It must be a temporary implementation. Real work must be done using queue (Amazon or Gearman)
		if (isset($this->request->params['named']['search']) && !empty($productsIds))
		{
			$searchPhrase = $this->Util->slugify($this->request->params['named']['search']);
			if ($this->LogSearch->isUnique(array('slug'=> $searchPhrase)))
			{
				$this->LogSearch->save(array('LogSearch'=>array('slug'=>$searchPhrase, 'created'=>date('Y-m-d H:i:s') , 'active'=>1)));
			}
		}

		// Gets the products according to extracted ids, but now formatted with the necessary data.
		
		$products = array();
		
		foreach ($productsIds as $key => $value)
		{
			$products[] = $this->__product($value);
		}
		
		
		$this->set(compact('category', 'products'));
		$this->set('title_for_layout', __d('controller', 'Products.index.title'));
	}
	
	
/**
 * Displays the specified product.
 *
 * @param string $product
 */
	public function view($product)
	{
		// Apply 'store' layout.
		
		$this->layout = 'store';
		
		// Checks if the specified product exists.
		
		$product = $this->__product($product);
		
		if (empty($product))
		{
			$this->redirect($this->referer(array('controller' => 'products', 'action' => 'index')), null, true);
		}
		
		
		// Gets the store information.
		
		$store = $this->__store($product['StoreProduct']['store_id']);
		$store['Store']['totalFollowers'] = $this->StoreProduct->getTotalFollowers($store['Store']['id']);		
		
		// Gets the related products.
		
		$related_products = $this->StoreProduct->find('all',
			array(
				'contain' => array(
					'Product' => array('Photo')
				),
				'conditions' => array(
					'StoreProduct.id NOT' => array($product['StoreProduct']['id']),
					'StoreProduct.store_id' => $store['Store']['id'],
					'(Product.quantity - Product.quantity_sold) >' => 0,
					'Product.is_deleted' => false
				),
				'order' => 'RAND()',
				'limit' => 4
			)
		);
		for ($i=0, $t=count($related_products); $i<$t; $i++ )
		{
			$related_products[$i] = $this->__product($related_products[$i]['StoreProduct']['hash']);
		}

		$this->set(compact('product', 'store', 'related_products'));
		$this->set('title_for_layout', $product['Product']['title']);
	}
	
	
/**
 * Performs the specified product purchase.
 *
 * @param string $product
 */
	public function buy($product)
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		

		// Checks if the specified product exists.
		
		$product = $this->__product($product);
		if(isset($this->request->query['action']) && $this->request->query['action'] == 'ppReturn') {
			
			$transaction = $this->Transaction->find('first', array('conditions' =>  array('Transaction.created >= SUBDATE(NOW(), INTERVAL 30 MINUTE)' ,'Transaction.buyer_id' => $this->loggedUser['User']['id'], 'Transaction.product_id' => $product['StoreProduct']['id'])));
			$payKey = $this->Session->read('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash']);

			if ($transaction || !$payKey) {
					$url = explode('/buy', $this->here);
					$this->redirect($url[0], null, true);
					
			}

			
			$cURL_header = array(
				'X-PAYPAL-APPLICATION-ID: ' . Configure::read('Paypal.API.appId'),
				'X-PAYPAL-SECURITY-USERID:' . Configure::read('Paypal.API.username'),
				'X-PAYPAL-SECURITY-PASSWORD:' . Configure::read('Paypal.API.password'),
				'X-PAYPAL-SECURITY-SIGNATURE:' . Configure::read('Paypal.API.signature'),
				'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
				'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
			);
			
			
			// Establishes communication with Paypal server and get the current status of the payment.
			
			$paymentDetails_cURL = curl_init();
			
			curl_setopt($paymentDetails_cURL, CURLOPT_URL, Configure::read('Paypal.API.endpoint') . '/AdaptivePayments/PaymentDetails');
			curl_setopt($paymentDetails_cURL, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($paymentDetails_cURL, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($paymentDetails_cURL, CURLOPT_POST, 1);
			curl_setopt($paymentDetails_cURL, CURLOPT_HTTPHEADER, $cURL_header);
			curl_setopt($paymentDetails_cURL, CURLOPT_POSTFIELDS,
				http_build_query(
					array(
						'payKey' => $payKey,
						'requestEnvelope.errorLanguage' => 'pt_BR'
					)
				)
			);
			
			$paymentDetails_response = json_decode(curl_exec($paymentDetails_cURL));
			curl_close($paymentDetails_cURL);

			if ($paymentDetails_response->responseEnvelope->ack == 'Success') {
				
				// Gets the product store.
				$store = $this->__store($product['Store']['id']);
		
		
				// Executes the purchase.
		
				list($success, $transaction) = $this->api_buy($product['StoreProduct']['id']);
				if ($success)
				{
					
					// Creates a new payment record.
					$data = array(
						'Payment' => array(
							'model' => 'Transaction',
							'foreign_key' => $transaction['Transaction']['id'],
							'method' => 'paypal',
							'status' => 'created',
							'key' => $payKey
						)
					);
			

					$this->Transaction->Payment->create();
					$this->Transaction->Payment->save($data);
					$payment = $this->Transaction->Payment->findById($this->Transaction->Payment->getLastInsertId());
				
					CakeLog::write('paypal', 'PAYMENT - ' . $payment['Payment']['hash']);
					CakeLog::write('paypal', serialize($paymentDetails_response));
					CakeLog::write('paypal', '');						
					
					// Adds a notification to the seller
					$this->UserNotificationAlert->setUser($store['User']['id']);
					$this->UserNotificationAlert->addTransaction();

						
					$this->Transaction->Payment->id = $payment['Payment']['id'];
					$this->Transaction->Payment->saveField('status', 'completed');
					$this->Transaction->Payment->saveField('is_finished', 1);
						
					$this->Transaction->id = $payment['Transaction']['id'];
					$this->Transaction->saveField('status', 'payed');
					$this->Transaction->saveField('is_paid', 1);
						
					$this->TransactionUpdate->create();
					$this->TransactionUpdate->save(
							array(
								'TransactionUpdate' => array(
									'transaction_id' => $payment['Transaction']['id'],
									'status' => 'payment completed'
								)
							)
					);
					
					$this->Session->delete('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash']);
					$this->Session->delete('ppFakeTransactionId-' . $product['StoreProduct']['hash']);
					$this->Session->delete('ppFakeTransactionData-' . $product['StoreProduct']['hash']);
										
					$this->redirect(Router::url(array('controller' => 'purchases', 'action' => 'view', 'admin' => false, 'transaction' => $transaction['Transaction']['hash'])), null, true);
				} else {
					
					mail("carlos@bazzapp.com","[Sistema] Problema com identificacao de transacao", $this->here . "\n" . serialize($transaction));
					
					$url = explode('/buy', $this->here);
					
					$this->redirect($url[0], null, true);
				}
			} else {
				
				
				$this->set('payKey', $this->Session->read('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash']));
				$this->render('/Products/buy_error');
			}
		

		} else
		if (empty($product))
		{
			$this->redirect('/', null, true);

		}
		else if ($product['Product']['is_deleted'] || $product['StoreProduct']['is_deleted'] || $product['Product']['quantity_available'] <= 0)
		{
			$this->redirect(array('controller' => 'products', 'action' => 'view', 'admin' => false, 'product' => $product['StoreProduct']['slug']), null, true);
			
		} else
		if(isset($this->request->query['action']) && $this->request->query['action'] == 'ppPay') {
			$data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));

			$this->Session->write('ppFakeTransactionData-' . $product['StoreProduct']['hash'], $data, 1200);
			$store			= $this->Store->find('first',array('conditions'=>array('Store.id' => $product['Store']['id']) ));
			 
			$qty 			= $data['Product']['quantity'];
			$price			= $product['Product']['price'];
			$totalPrice		= str_replace(',' ,'.', $price*$qty + str_replace(',' ,'.', $data['Delivery']['price']));
			$shippingCost	= str_replace(',' ,'.', $data['Delivery']['price']);

			$payKey = $this->Session->read('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash']);

			if ($payKey === null) {
				$cURL_header = array(
					'X-PAYPAL-APPLICATION-ID: ' . Configure::read('Paypal.API.appId'),
					'X-PAYPAL-SECURITY-USERID:' . Configure::read('Paypal.API.username'),
					'X-PAYPAL-SECURITY-PASSWORD:' . Configure::read('Paypal.API.password'),
					'X-PAYPAL-SECURITY-SIGNATURE:' . Configure::read('Paypal.API.signature'),
					'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
					'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
				);
				
				
				$fakeTransactionId = $this->loggedUser['User']['id'] .'|'. $product['StoreProduct']['hash'] . '|'.rand(1000,1000000);
				
				$this->Session->write('ppFakeTransactionId-' . $product['StoreProduct']['hash'], $fakeTransactionId, 1200);
				
				// Establishes communication with Paypal server and generate a payment key.
				$createPayKey_cURL = curl_init();

				curl_setopt($createPayKey_cURL, CURLOPT_URL, Configure::read('Paypal.API.endpoint') . '/AdaptivePayments/Pay');
				curl_setopt($createPayKey_cURL, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($createPayKey_cURL, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($createPayKey_cURL, CURLOPT_POST, 1);
				curl_setopt($createPayKey_cURL, CURLOPT_HTTPHEADER, $cURL_header);
				curl_setopt($createPayKey_cURL, CURLOPT_POSTFIELDS,
					http_build_query(
						array(
							'actionType'   => 'PAY',
							'currencyCode' => 'BRL',
							'feesPayer' => 'SECONDARYONLY',
							'cancelUrl' => Configure::read('baseUrl') . $this->here . '?action=ppCancel',
							'returnUrl' => Configure::read('baseUrl') . $this->here . '?action=ppReturn',
							'ipnNotificationUrl' => Configure::read('baseUrl') . $this->here . '?action=ppNotification',
							'trackingId' => $fakeTransactionId,
							'requestEnvelope.errorLanguage' => 'pt_BR',
							'receiverList.receiver(0).email' => $store['PaypalAccount']['email'],
							'receiverList.receiver(0).amount' => $totalPrice,
							'receiverList.receiver(0).primary' => true,
							'receiverList.receiver(1).email' => Configure::read('Paypal.payment.BazzApp.email'),
							'receiverList.receiver(1).amount' => number_format(($totalPrice * (Configure::read('Paypal.payment.tax') / 100)) + (($totalPrice * $qty) * (Configure::read('Paypal.payment.BazzApp.tax') / 100)), 2, '.', ''),
							'receiverList.receiver(1).primary' => false,
							'receiverOptions(0).receiver.email' => $store['PaypalAccount']['email'],
							'receiverOptions(0).invoiceData.totalShipping' => $shippingCost,
							'receiverOptions(0).invoiceData.item(0).name' => $product['Product']['title'],
							'receiverOptions(0).invoiceData.item(0).identifier' => $product['Product']['hash'],
							'receiverOptions(0).invoiceData.item(0).price' => $price*$qty,
							'receiverOptions(0).invoiceData.item(0).itemPrice' => $price,
							'receiverOptions(0).invoiceData.item(0).itemCount' => $qty
						)
					)
				);
				
				$createPayKey_response = json_decode(curl_exec($createPayKey_cURL));
				curl_close($createPayKey_cURL);

				if ($createPayKey_response->responseEnvelope->ack == 'Success') {
					$payKey = $createPayKey_response->payKey;
					$this->Session->write('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash'], $payKey, 1200);
				
				} else
				if ($createPayKey_response->responseEnvelope->ack == 'Failure') {
					
					foreach ($createPayKey_response->error as $error) {
						
						if ($error->errorId == '569042') {

							$to = $product['Store']['User']['email'];
							$subject = '[BazzApp] Alerta de venda';
							
							$msg = "Olá ". $product['Store']['title'] .",\n\nSeu produto \"". $product['Product']['title'] ."\" acaba de tentar ser comprado, porém a transação não pode ser efetuada pois a sua conta PayPal ainda não foi ativada para receber pagamentos.\n\nPor favor, acesse www.paypal.com.br e ative sua conta para fazer novas vendas!\n\nObrigado, Equipe BazzApp!";
							
							@mail($to, $subject, $msg);
							
							$errorMsg = "Desculpe, houve um erro ao processar a sua compra, já avisamos o problema ao vendedor desse produto. Por favor tente mais tarde, Obrigado.";
						}
					}
					
					mail('carlos@zanaca.com','[BazzApp] Erro Paypal', serialize($createPayKey_response));
				} 
			}
					
			if ($payKey !== null) {
				$this->redirect(Configure::read('Paypal.Flow.endpoint') .'/webscr?cmd=_ap-payment&country.x=br&locale=pt_BR&change_locale=1&paykey=' . $payKey, null, true);
				
			} else {
				
				if (!isset($errorMsg)) {
					$errorMsg = "Desculpe, houve um erro ao processar a sua compra. Por favor tente mais tarde, obrigado.";
				}
				
				$this->set('errorMsg', $errorMsg);
				$this->render('/Products/buy_error');
			}
		}
		// Gets the product store.
		
		$store = $this->__store($product['Store']['id']);
		

		// Executes the purchase.
		
		list($success, $transaction) = $this->api_buy($product['StoreProduct']['id']);
				
		if ($success)
		{
			// Adds a notification to the seller
			$this->UserNotificationAlert->setUser($store['User']['id']);
			$this->UserNotificationAlert->addTransaction();
			
			if ($transaction['Store']['PaypalAccount']['email'] != '') {
				$this->redirect(array('controller' => 'purchases', 'action' => 'view', 'admin' => false, 'transaction' => $transaction['Transaction']['hash'], '?'=> 'goPaypal=1'), null, true);
				
			} else {
				$this->redirect(array('controller' => 'purchases', 'action' => 'view', 'admin' => false, 'transaction' => $transaction['Transaction']['hash']), null, true);
				
			}
		}
		
		$hasPaypal  = $store['PaypalAccount']['id'] !== null;
		$this->set(compact('product', 'store', 'hasPaypal'));
		$this->set('title_for_layout', $product['Product']['title']);
	}
	
	
/**
 * Identifies the store associated with fanpage.
 *
 */
	public function fanpage_home()
	{
		// Apply 'empty' layout.
		
		$this->layout = 'empty';
		
	
		// Checks if is being called by a fanpage.
		
		$fb_signed_request = $this->FB->getSignedRequest();
		
		if (!empty($fb_signed_request['page']['id']))
		{
			// Gets the store associated with fanpage.
			
			$store = $this->Store->find('first',
				array(
					'conditions' => array(
						'FacebookPage.fb_page_id' => $fb_signed_request['page']['id'],
						'FacebookPage.is_installed' => true,
						'FacebookPage.is_deleted' => false,
						'Store.is_enabled' => true,
						'Store.is_deleted' => false
					)
				)
			);
			
			if (!empty($store))
			{
				// Checks if was specified a path for which should be redirected.
				
				if (!empty($fb_signed_request['app_data']))
				{
					$parsed_path = Router::parse($fb_signed_request['app_data']);
					
					if ((!empty($parsed_path['prefix']) && strcasecmp($parsed_path['prefix'], 'fanpage') == 0) && (!empty($parsed_path['store']) && in_array($parsed_path['store'], array($store['Store']['id'], $store['Store']['slug'], $store['Store']['hash']))))
					{
						
						#$this->redirect($fb_signed_request['app_data'], null, true);
					}
				}
				
				#$this->redirect(array('controller' => 'products', 'action' => 'index', 'fanpage' => true, 'store' => $store['Store']['slug']), null, true);
			}
		}
		
		
		$this->set('title_for_layout', __d('controller', 'Products.fanpage_home.title'));
	}
	
	
/**
 * Lists the products according to search term and/or selected category.
 *
 * @param string $store
 */
	public function fanpage_index($store)
	{
		// Apply 'fanpage_default' layout.
		
		$this->layout = 'fanpage_default';
		
		
		// Checks if the specified store is valid.
		
		$store = $this->__fanpageStore($store);
		
		if (empty($store))
		{
			$this->redirect(array('controller' => 'products', 'action' => 'home', 'fanpage' => true), null, true);
		}
		
		
		// Default conditions.
		
		$productsConditions = array(
			'ViewStoreProductCategory.store_id' => $store['Store']['id'],
			'(Product.quantity - Product.quantity_sold) >' => 0,
			'Product.is_deleted' => false,
			'ViewStoreProductCategory.is_deleted' => false
		);
		
		
		// Search term conditions.
		
		if (!empty($this->request->params['named']['search']) || !empty($this->request->data['Search']['search']))
		{
			if (!empty($this->request->data['Search']['search']))
			{
				$this->request->params['named']['search'] = $this->request->data['Search']['search'];
			}
			
			
			$search = $this->request->params['named']['search'];
			
			$productsConditions['OR'] = array();
			$productsConditions['OR']['Product.title LIKE'] = '%' . Sanitize::clean($search, array('encode' => false)) . '%';
			$productsConditions['OR']['Product.description LIKE'] = '%' . Sanitize::clean($search, array('encode' => false)) . '%';
			$productsConditions['OR']['Store.title LIKE'] = '%' . Sanitize::clean($search, array('encode' => false)) . '%';
		}
		
		
		// Category conditions.
		
		if (!empty($this->request->params['named']['category']) || !empty($this->request->data['Search']['category']))
		{
			if (!empty($this->request->data['Search']['category']))
			{
				$this->request->params['named']['category'] = $this->request->data['Search']['category'];
			}
			
			
			$category = $this->__category($this->request->params['named']['category']);
			
			if (!empty($category))
			{
				$productsConditions['ViewStoreProductCategory.category_lft >='] = $category['Category']['lft'];
				$productsConditions['ViewStoreProductCategory.category_rght <='] = $category['Category']['rght'];
			}
		}
		else
		{
			$category = array();
		}
		
		
		// Finds the products applying conditions and extract product ids.
		
		$products = $this->paginate('ViewStoreProductCategory', $productsConditions);
		$productsIds = Set::extract('/ViewStoreProductCategory/id', $products);
		
		
		// Gets the products according to extracted ids, but now formatted with the necessary data.
		
		$products = array();
		
		foreach ($productsIds as $key => $value)
		{
			$products[] = $this->__product($value);
		}
		
		
		$this->set(compact('category', 'products', 'store'));
		$this->set('title_for_layout', __d('controller', 'Products.index.title'));
	}
	
	
/**
 * Displays the specified product.
 *
 * @param string $product
 * @param string $store
 */
	public function fanpage_view($product, $store)
	{
		// Apply 'fanpage_default' layout.
		
		$this->layout = 'fanpage_default';
		
		
		// Checks if the specified store and product are valid.
		
		list($product, $store) = $this->__fanpageStoreProduct($product, $store);
		
		if (empty($store))
		{
			$this->redirect(array('controller' => 'products', 'action' => 'home', 'fanpage' => true), null, true);
		}
		else if (empty($product))
		{
			$this->redirect(array('controller' => 'products', 'action' => 'index', 'fanpage' => true, 'store' => $store['Store']['slug']), null, true);
		}
		
		
		// Gets the related products.
		
		$related_products = $this->StoreProduct->find('all',
			array(
				'contain' => array(
					'Product' => array('Photo')
				),
				'conditions' => array(
					'StoreProduct.id NOT' => array($product['StoreProduct']['id']),
					'StoreProduct.store_id' => $store['Store']['id'],
					'(Product.quantity - Product.quantity_sold) >' => 0,
					'Product.is_deleted' => false
				),
				'order' => 'RAND()',
				'limit' => 4
			)
		);
		
		for ($i=0, $t=count($related_products); $i<$t; $i++)
		{
			$related_products[$i] = $this->__product($related_products[$i]['StoreProduct']['slug']);
		}
		
		$this->set(compact('product', 'store', 'related_products'));
		$this->set('title_for_layout', $product['Product']['title']);
	}
	
	
/**
 * Performs the specified product purchase.
 *
 * @param string $product
 */
	public function fanpage_buy($product, $store)
	{
		// Apply 'fanpage_default' layout.
		
		$this->layout = 'fanpage_default';
		
		// Checks if the specified product exists.
		
		list($product, $store) = $this->__fanpageStoreProduct($product, $store);
		
		if(isset($this->request->query['action']) && $this->request->query['action'] == 'ppReturn') {
			
			$transaction = $this->Transaction->find('first', array('conditions' =>  array('Transaction.created >= SUBDATE(NOW(), INTERVAL 30 MINUTE)' ,'Transaction.buyer_id' => $this->loggedUser['User']['id'], 'Transaction.product_id' => $product['StoreProduct']['id'])));
			$payKey = $this->Session->read('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash']);
			
			if ($transaction || !$payKey) {
					$url = explode('/buy', $this->here);
					$this->redirect($url[0], null, true);
					
			}

			$cURL_header = array(
				'X-PAYPAL-APPLICATION-ID: ' . Configure::read('Paypal.API.appId'),
				'X-PAYPAL-SECURITY-USERID:' . Configure::read('Paypal.API.username'),
				'X-PAYPAL-SECURITY-PASSWORD:' . Configure::read('Paypal.API.password'),
				'X-PAYPAL-SECURITY-SIGNATURE:' . Configure::read('Paypal.API.signature'),
				'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
				'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
			);
			
			
			// Establishes communication with Paypal server and get the current status of the payment.
			
			$paymentDetails_cURL = curl_init();
			
			curl_setopt($paymentDetails_cURL, CURLOPT_URL, Configure::read('Paypal.API.endpoint') . '/AdaptivePayments/PaymentDetails');
			curl_setopt($paymentDetails_cURL, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($paymentDetails_cURL, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($paymentDetails_cURL, CURLOPT_POST, 1);
			curl_setopt($paymentDetails_cURL, CURLOPT_HTTPHEADER, $cURL_header);
			curl_setopt($paymentDetails_cURL, CURLOPT_POSTFIELDS,
				http_build_query(
					array(
						'payKey' => $payKey,
						'requestEnvelope.errorLanguage' => 'pt_BR'
					)
				)
			);
			
			$paymentDetails_response = json_decode(curl_exec($paymentDetails_cURL));
			curl_close($paymentDetails_cURL);

			if ($paymentDetails_response->responseEnvelope->ack == 'Success') {
				
				// Gets the product store.
				$store = $this->__store($product['Store']['id']);
		
		
				// Executes the purchase.
		
				list($success, $transaction) = $this->api_buy($product['StoreProduct']['id']);
				if ($success)
				{
					
					// Creates a new payment record.
					$data = array(
						'Payment' => array(
							'model' => 'Transaction',
							'foreign_key' => $transaction['Transaction']['id'],
							'method' => 'paypal',
							'status' => 'created',
							'key' => $payKey
						)
					);
			

					$this->Transaction->Payment->create();
					$this->Transaction->Payment->save($data);
					$payment = $this->Transaction->Payment->findById($this->Transaction->Payment->getLastInsertId());
				
					CakeLog::write('paypal', 'PAYMENT - ' . $payment['Payment']['hash']);
					CakeLog::write('paypal', serialize($paymentDetails_response));
					CakeLog::write('paypal', '');						
					
					// Adds a notification to the seller
					$this->UserNotificationAlert->setUser($store['User']['id']);
					$this->UserNotificationAlert->addTransaction();

						
					$this->Transaction->Payment->id = $payment['Payment']['id'];
					$this->Transaction->Payment->saveField('status', 'completed');
					$this->Transaction->Payment->saveField('is_finished', 1);
						
					$this->Transaction->id = $payment['Transaction']['id'];
					$this->Transaction->saveField('status', 'payed');
					$this->Transaction->saveField('is_paid', 1);
						
					$this->TransactionUpdate->create();
					$this->TransactionUpdate->save(
							array(
								'TransactionUpdate' => array(
									'transaction_id' => $payment['Transaction']['id'],
									'status' => 'payment completed'
								)
							)
					);
					
					$this->Session->delete('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash']);
					$this->Session->delete('ppFakeTransactionId-' . $product['StoreProduct']['hash']);
					$this->Session->delete('ppFakeTransactionData-' . $product['StoreProduct']['hash']);
										
					$this->redirect(Router::url(array('controller' => 'purchases', 'action' => 'view', 'fanpage' => true, 'admin' => false, 'transaction' => $transaction['Transaction']['hash'])), null, true);
				} else {
					
					mail("carlos@bazzapp.com","[Sistema] Problema com identificacao de transacao", $this->here . "\n" . serialize($transaction));
					
					$url = explode('/buy', $this->here);
					
					$this->redirect($url[0], null, true);
				}
			} else {
				
				$payKey = $this->Session->read('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash']);
				
				$this->set('payKey', $payKey);
				$this->render('/Products/buy_error');
			}
		

		} else
		if (empty($store))
		{
			$this->redirect(array('controller' => 'products', 'action' => 'home', 'fanpage' => true), null, true);
		}
		else if (empty($product))
		{
			$this->redirect(array('controller' => 'products', 'action' => 'index', 'fanpage' => true, 'store' => $store['Store']['slug']), null, true);
		}
		else if ($product['Product']['is_deleted'] || $product['StoreProduct']['is_deleted'] || $product['Product']['quantity_available'] <= 0)
		{
			$this->redirect(array('controller' => 'products', 'action' => 'view', 'fanpage' => true, 'store' => $store['Store']['slug'], 'product' => $product['StoreProduct']['slug']), null, true);
			
		} else
		if(isset($this->request->query['action']) && $this->request->query['action'] == 'ppPay') {
			$data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));

			$this->Session->write('ppFakeTransactionData-' . $product['StoreProduct']['hash'], $data, 1200);
			$store			= $this->Store->find('first',array('conditions'=>array('Store.id' => $product['Store']['id']) ));
			 
			$qty 			= $data['Product']['quantity'];
			$price			= $product['Product']['price'];
			$totalPrice		= str_replace(',' ,'.', $price*$qty + str_replace(',' ,'.', $data['Delivery']['price']));
			$shippingCost	= str_replace(',' ,'.', 1);#$data['Delivery']['price']);

			
			$payKey = $this->Session->read('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash']);

			if ($payKey === null) {
				$cURL_header = array(
					'X-PAYPAL-APPLICATION-ID: ' . Configure::read('Paypal.API.appId'),
					'X-PAYPAL-SECURITY-USERID:' . Configure::read('Paypal.API.username'),
					'X-PAYPAL-SECURITY-PASSWORD:' . Configure::read('Paypal.API.password'),
					'X-PAYPAL-SECURITY-SIGNATURE:' . Configure::read('Paypal.API.signature'),
					'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
					'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
				);
				
				
				$fakeTransactionId = $this->loggedUser['User']['id'] .'|'. $product['StoreProduct']['hash'] . '|'.rand(1000,1000000);
				
				$this->Session->write('ppFakeTransactionId-' . $product['StoreProduct']['hash'], $fakeTransactionId, 1200);
				
				// Establishes communication with Paypal server and generate a payment key.
				$createPayKey_cURL = curl_init();

				curl_setopt($createPayKey_cURL, CURLOPT_URL, Configure::read('Paypal.API.endpoint') . '/AdaptivePayments/Pay');
				curl_setopt($createPayKey_cURL, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($createPayKey_cURL, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($createPayKey_cURL, CURLOPT_POST, 1);
				curl_setopt($createPayKey_cURL, CURLOPT_HTTPHEADER, $cURL_header);
				curl_setopt($createPayKey_cURL, CURLOPT_POSTFIELDS,
					http_build_query(
						array(
							'actionType'   => 'PAY',
							'currencyCode' => 'BRL',
							'feesPayer' => 'SECONDARYONLY',
							'cancelUrl' => Configure::read('baseUrl') . $this->here . '?action=ppCancel',
							'returnUrl' => Configure::read('baseUrl') . $this->here . '?action=ppReturn',
							'ipnNotificationUrl' => Configure::read('baseUrl') . $this->here . '?action=ppNotification',
							'trackingId' => $fakeTransactionId,
							'requestEnvelope.errorLanguage' => 'pt_BR',
							'receiverList.receiver(0).email' => $store['PaypalAccount']['email'],
							'receiverList.receiver(0).amount' => $totalPrice,
							'receiverList.receiver(0).primary' => true,
							'receiverList.receiver(1).email' => Configure::read('Paypal.payment.BazzApp.email'),
							'receiverList.receiver(1).amount' => number_format(($totalPrice * (Configure::read('Paypal.payment.tax') / 100)) + (($totalPrice * $qty) * (Configure::read('Paypal.payment.BazzApp.tax') / 100)), 2, '.', ''),
							'receiverList.receiver(1).primary' => false,
							'receiverOptions(0).receiver.email' => $store['PaypalAccount']['email'],
							'receiverOptions(0).invoiceData.totalShipping' => $shippingCost,
							'receiverOptions(0).invoiceData.item(0).name' => $product['Product']['title'],
							'receiverOptions(0).invoiceData.item(0).identifier' => $product['Product']['hash'],
							'receiverOptions(0).invoiceData.item(0).price' => $price*$qty,
							'receiverOptions(0).invoiceData.item(0).itemPrice' => $price,
							'receiverOptions(0).invoiceData.item(0).itemCount' => $qty
						)
					)
				);
				
				$createPayKey_response = json_decode(curl_exec($createPayKey_cURL));
				curl_close($createPayKey_cURL);

				if ($createPayKey_response->responseEnvelope->ack == 'Success') {
					$payKey = $createPayKey_response->payKey;
					$this->Session->write('ppFakeTransactionPayKey-' . $product['StoreProduct']['hash'], $payKey, 1200);
				}
			}
					
			
			$this->redirect(Configure::read('Paypal.Flow.endpoint') .'/webscr?cmd=_ap-payment&country.x=br&locale=pt_BR&change_locale=1&paykey=' . $payKey, null, true);
		}
		
		
		// Executes the purchase.
		
		list($success, $transaction) = $this->api_buy($product['StoreProduct']['id']);

		
		if ($success)
		{
			// Adds a notification to the seller
			$this->UserNotificationAlert->setUser($store['User']['id']);
			$this->UserNotificationAlert->addTransaction();
			
			if ($transaction['Store']['PaypalAccount']['email'] != '') {
				$this->redirect(($transaction['Store']['is_personal'] == 0 ? '/fp' : '/') . "purchases/" . $transaction['Transaction']['hash'] . "/finished?goPaypal=1", null, true);
				
			} else {
				$this->redirect(($transaction['Store']['is_personal'] == 0 ? '/fp' : '/') . "purchases/" . $transaction['Transaction']['hash'] . "/finished", null, true);
				
			}
		}
		
		$hasPaypal  = $store['PaypalAccount']['id'] !== null;
		$this->set(compact('product', 'store', 'hasPaypal'));
		$this->set('title_for_layout', $product['Product']['title']);
		
	}
	
	
/**
 * Creates the submitted product and associate to selected stores.
 *
 * @var array
 */
	public function admin_add()
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		
		
		if (isset($this->request->query['action']) && $this->request->query['action'] == 'checkEmail' && isset($this->request->query['email'])) {
			$email = $this->request->query['email'];
			
			if ($this->checkIfPaypalEmailIsBillable($email)) {
				$this->fillPaypalEmail($email);
				$status = 1;
					
			} else {
				$status = 0;
					
			}
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header("Content-type: application/json");
			echo "{\"emailStatus\":$status}";
			exit;
			
			
		}
		
		
		// Gets the Facebook manage_pages permisson status.
		
		$fb_manage_pages = $this->__facebookFanpagePermission();
		
		if ($fb_manage_pages)
		{
			// Gets the administered stores and extracts the stores ids.
			
			$administeredStores = $this->StoreAdministrator->find('all',
				array(
					'conditions' => array(
						'User.id' => $this->loggedUser['User']['id'],
						'StoreAdministrator.is_deleted' => false,
						'Store.is_deleted' => false
					)
				)
			);
			
			$administeredStoresIds = Set::extract('/Store/id', $administeredStores);
			
			
			// Gets the Facebook page stores waiting to install the application.
			
			$stores = array();
			$stores_waiting_install = array();
			
			foreach ($administeredStoresIds as $key => $value)
			{
				$store = $this->__store($value);
				
				if (!empty($store['FacebookPage']['id']) && !$store['FacebookPage']['is_installed'])
				{
					$stores_waiting_install[] = $store;
				}
				else if (!empty($store) && $store['Store']['is_enabled'])
				{
					$stores[] = $store;
				}
			}
		}
		else
		{
			$stores = array();
			$stores_waiting_install = array();
		}
		
		// Checks if the product data was submitted and if so, stores it.
		
		if (($this->request->is('post') || $this->request->is('put')) && (!empty($this->request->data['Product']) && !empty($this->request->data['Photo'])))
		{
			// Clean up submitted data.
			
			$clean_data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));
			
			$data = array(
				'Product' => $clean_data['Product'],
				'Photo' => $clean_data['Photo']
			);
			
			
			// Removes the photos fields that was submitted in blank.
			
			foreach ($data['Photo'] as $key => $value)
			{
				if ($key > 0 && empty($value['filename']['name']))
				{
					unset($data['Photo'][$key]);
				}
			}
			
			
			// Store address
			if (isset($data['Product']['zipcode']) && isset($data['Product']['address']) && isset($data['Product']['addressLine2']) && isset($data['Product']['district']) && isset($data['Product']['city']) && isset($data['Product']['state'])) { 
				
				$address = array();
				
				$address['zipcode'] = $data['Product']['zipcode'];
				$address['address'] = $data['Product']['address'];
				$address['address_line2'] = $data['Product']['addressLine2'];
				$address['district'] = $data['Product']['district'];
				$address['city'] = $data['Product']['city'];
				$address['state'] = $data['Product']['state']; 
				
				$address['model'] = 'Store';
				
				if (isset($this->request->data['Store']))
				foreach ($this->request->data['Store'] as $storeId) {
					$address['foreign_key'] = $storeId;
					
					$store = $this->Store->find('first',array('conditions' => array('Store.id' => $storeId)));
					if (isset($store) && !isset($store['Address']['id'])) {					
						$this->Address->create();
						$this->Address->save($address);
			
					}
				}
				
			}
			
			// Validates and saves the submitted data.
			
			$this->Product->create();
			$data['Product']['price'] = str_replace('.','', $data['Product']['price']);
			if ($this->Product->saveAll($data, array('validate' => 'first')))
			{
				$productId = $this->Product->getLastInsertId();
				
				
				// Checks if was selected a store to publish the product.
				
				$storesIds = array();
				
				if (!empty($this->request->data['Store']))
				{
					foreach ($this->request->data['Store'] as $key => $value)
					{
						$store = $this->__administeredStore($value);
						
						if (!empty($store))
						{
							$storesIds[] = $store['Store']['id'];
						}
					}
				}
				
				
				// Selects the personal store if no store has been selected.
				
				if (empty($storesIds))
				{
					$store = $this->Store->find('first',
						array(
							'conditions' => array(
								'Store.user_id' => $this->loggedUser['User']['id'],
								'Store.is_personal' => true,
								'Store.is_deleted' => false
							)
						)
					);
					
					$storesIds[] = $store['Store']['id'];
				}
				
				
				// Connects the product to selected stores.
				
				foreach ($storesIds as $key => $value)
				{
					$this->StoreProduct->create();
					$this->StoreProduct->save(
						array(
							'store_id' => $value,
							'product_id' => $productId
						)
					);
				}
				
				
				$storeProducts = $this->StoreProduct->find('all',
						array(
							'conditions' => array(
								'StoreProduct.product_id' => $productId,
								'StoreProduct.is_deleted' => false
							)
						)
				);
					
				// Publishes the product photo.
				
				if (isset($data['Product']['publish']) && $data['Product']['publish'] == 1)
				{
					foreach ($storeProducts as $key => $value)
					{
						$this->__publishPhoto($value['StoreProduct']['id']);
					}
				}
				
				$product = $this->__product($storeProducts[0]['StoreProduct']['slug']);
				// Send the email alerts
				foreach ($storesIds as $storeId) {
					$usersLiked = $this->Follow->find('all',array('fields' => 'Follow.user_id', 'conditions' => array('Follow.model' => 'Store', 'Follow.foreign_key' => $storeId)));
					$usersLikedId = Set::extract('/Follow/user_id', $usersLiked);
					
					foreach ($usersLikedId as $userId) {
						$user = $this->User->find('first',
									array(
										'conditions' => array(
											'User.id' => $userId
										)
									)
								);
						
						$to = $user['User']['email'];
						$vars = array('userName' => $user['User']['name'], 'productTitle' => $product['Product']['title'], 'productUrl' => $product['StoreProduct']['url'], 'productPrice' => $product['Product']['price']);
						$this->CommunicationQueue->sendNewProductFromStore($to, $vars);	
					}

				}
				
				
				$this->set('body_class', 'buy thanks'); 
				
				$this->set('product', $product);
				$this->set('userEmail', $this->loggedUser['User']['email']);
				
				$this->CommunicationQueue->sendProductAdded($this->loggedUser['User']['email'], array('productTitle' => $product['Product']['title']));

				
				$friends = $this->Friend->find('all', array('conditions' => array('Friend.facebook_user_id' => $this->loggedUser['User']['facebook_user_id'])));
				$friendsFBId = Set::extract('/Friend/friend_facebook_user_id', $friends);
				
				foreach ($friendsFBId as $friendFBId) {
					$opengraph = $this->FacebookUser->find('first', array('conditions' => array('FacebookUser.id' => $friendFBId)));
					$opengraphId = Set::extract('/FacebookUser/fb_user_id', $opengraph);
					
					$this->CommunicationQueue->sendProductAddedFacebookNotification($opengraphId , array('productTitle' => $product['Product']['title'], 'friendName' => $this->loggedUser['User']['name'], 'url' => $product['StoreProduct']['url']));
				}
				
				$this->set('page','admin_add_ok');
				$this->render('/Products/admin_add_ok');
			}
			
		}
		// In case we are just opening the popup after an successful new product
		elseif ($this->request->is('get') && isset($this->request->query['pId']))
		{
			$this->layout = 'empty';
			
			$clean_data = Sanitize::clean($this->request->query['pId']);
			$productId =  $clean_data;
			
			$product = $this->StoreProduct->findById($productId);
			$this->set('product', $product);

			$this->render('/Products/admin_add_ok_popup');
		}
		
		$storeIds = array();
		
		if (is_array($stores)) {
			foreach ($stores as $store) {
				$storeIds[] = $store['Store']['id'];
			}
		}

		if (isset($this->loggedUser['Store']['Address']['id']) && $this->loggedUser['Store']['Address']['id'] != null) {
			$storeIds[] = $this->loggedUser['Store']['Address']['id'];
		}

		$hasAddress = ! $this->checkStoreHasNoAddress($storeIds);
		$hasPaypal  = $this->checkHasPaypal();
		$userEmail  = $this->loggedUser['User']['email'];

		$this->set(compact('stores', 'stores_waiting_install', 'fb_manage_pages', 'hasPaypal', 'userEmail', 'hasAddress'));
		$this->set('title_for_layout', __d('controller', 'Products.admin_add.title'));
	}
	
	
/**
 * Creates the submitted products and associate to selected stores.
 *
 * @var array
 */
	public function admin_import()
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		
		
		// Gets the Facebook manage_pages permisson status.
		
		$fb_manage_pages = $this->__facebookFanpagePermission();
		
		if ($fb_manage_pages)
		{
			// Gets the administered stores and extracts the stores ids.
			
			$administeredStores = $this->StoreAdministrator->find('all',
				array(
					'conditions' => array(
						'User.id' => $this->loggedUser['User']['id'],
						'StoreAdministrator.is_deleted' => false,
						'Store.is_deleted' => false
					)
				)
			);
			
			$administeredStoresIds = Set::extract('/Store/id', $administeredStores);
			
			
			// Gets the Facebook page stores waiting to install the application.
			
			$stores = array();
			$stores_waiting_install = array();
			
			foreach ($administeredStoresIds as $key => $value)
			{
				$store = $this->__store($value);
				
				if (!empty($store['FacebookPage']['id']) && !$store['FacebookPage']['is_installed'])
				{
					$stores_waiting_install[] = $store;
				}
				else if (!empty($store) && $store['Store']['is_enabled'])
				{
					$stores[] = $store;
				}
			}
		}
		else
		{
			$stores = array();
			$stores_waiting_install = array();
		}
		
		
		// Checks if the product data was submitted and if so, stores it.
		
		if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data['Product']))
		{
			// Clean up submitted data.
			
			$clean_data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));
			
			$data = array(
				'Product' => $clean_data['Product']
			);
			
			
			// Removes the products that was submitted in blank.
			
			foreach ($data['Product'] as $key => $value)
			{
				if (empty($value['title']) || !is_numeric($key))
				{
					unset($data['Product'][$key]);
				}
			}
			
			
			// Validates the submitted data.
			
			$this->Product->create();
			
			if ($this->Product->saveAll($data['Product'], array('validate' => 'only')))
			{
				$HttpSocket = new HttpSocket();
				
				
				// Writes the submitted products photo to disc.
				
				foreach ($data['Product'] as $key => $value)
				{
					// Gets the photo data.
					
					$photo_data = file_get_contents('https://graph.facebook.com/' . $value['fb_photo_id'] . '/picture?access_token=' . $this->FB->getAccessToken());
					
					
					// Creates the temporary file.
					
					$photo_temp = tempnam('/tmp', 'BazzApp');
					
					
					// Writes the data to temporary file.
					
					$file = fopen($photo_temp, 'w');
					fwrite($file, $photo_data);
					fclose($file);
					
					
					// Saves the product.
					
					$product_data = array(
						'Product' => $value,
						'Photo' => array(
							array(
								'filename' => array(
									'name' => pathinfo($photo_temp, PATHINFO_FILENAME) . '.' . image_type_to_extension(exif_imagetype($photo_temp), false),
									'type' => mime_content_type($photo_temp),
									'tmp_name' => $photo_temp,
									'size' => filesize($photo_temp),
									'error' => 0
								)
							)
						)
					);
					
					$this->Product->create();
					
					if ($this->Product->saveAll($product_data, array('validate' => 'first')))
					{
						$productId = $this->Product->getLastInsertId();
						
						
						// Checks if was selected a store to publish the product.
						
						$storesIds = array();
						
						if (!empty($this->request->data['Store']))
						{
							foreach ($this->request->data['Store'] as $key => $value)
							{
								$store = $this->__administeredStore($value);
								
								if (!empty($store))
								{
									$storesIds[] = $store['Store']['id'];
								}
							}
						}
						
						
						// Selects the personal store if no store has been selected.
						
						if (empty($storesIds))
						{
							$store = $this->Store->find('first',
								array(
									'conditions' => array(
										'Store.user_id' => $this->loggedUser['User']['id'],
										'Store.is_personal' => true,
										'Store.is_deleted' => false
									)
								)
							);
							
							$storesIds[] = $store['Store']['id'];
						}
						
						
						// Connects the product to selected stores.
						
						foreach ($storesIds as $key => $value)
						{
							$this->StoreProduct->create();
							$this->StoreProduct->save(
								array(
									'store_id' => $value,
									'product_id' => $productId
								)
							);
						}
						
						
						// Publishes the product photo.
						
						if (isset($this->request->data['Product']['publish']) && $this->request->data['Product']['publish'] == 1)
						{
							$storeProducts = $this->StoreProduct->find('all',
								array(
									'conditions' => array(
										'StoreProduct.product_id' => $productId,
										'StoreProduct.is_deleted' => false
									)
								)
							);
							
							foreach ($storeProducts as $key => $value)
							{
								$this->__publishPhoto($value['StoreProduct']['id']);
							}
						}
					}
					
					
					// Deletes the temporary file.
					
					unlink($photo_temp);
				}
				
				$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true), null, true);
			}
		}
		
		
		// Defines the album variable.
		
		$albums = array();
		
		
		// Gets the personal Facebook albums.
		
		try
		{
			$fb_albums = $this->FB->api('/me/albums');
		
			if (!empty($fb_albums['data']))
			{
				$albums[$this->loggedUser['User']['name']] = array();
			
				foreach ($fb_albums['data'] as $key => $value)
				{
					$albums[$this->loggedUser['User']['name']][$value['id']] = $value['name'];
				}
			}
		}
		catch (FacebookApiException $e)
		{
		}
		
		
		// Gets the managed pages Facebook albums.
		
		if ($fb_manage_pages)
		{
			try
			{
				$fb_pages = $this->FB->api('/me/accounts');
				
				if (!empty($fb_pages['data']))
				{
					foreach ($fb_pages['data'] as $key => $value)
					{
						if (!empty($value['perms']))
						{
							// Loads the Facebook page albums.
			
							$fb_albums = $this->FB->api('/' . $value['id'] . '/albums', 'GET', array('access_token' => $value['access_token']));
							
							if (!empty($fb_albums['data']))
							{
								$albums[$value['name']] = array();
				
								foreach ($fb_albums['data'] as $key_2 => $value_2)
								{
									$albums[$value['name']][$value_2['id']] = $value_2['name'];
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
		
		
		$this->set(compact('stores', 'stores_waiting_install', 'fb_manage_pages', 'albums'));
		$this->set('title_for_layout', __d('controller', 'Products.admin_import.title'));
	}
	
	
/**
 * Updates the specified product information.
 *
 * @param mixed $product
 * @param mixed $store
 */
	public function admin_edit($product, $store)
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		
		
		// Checks if the specified product exists and user has administrator permissions.
		
		list($product, $store) = $this->__administeredStoreProduct($product, $store);
		
		if (empty($store))
		{
			$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true), null, true);
		}
		else if (empty($product))
		{
			$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true, 'store' => $store['Store']['slug']), null, true);
		}
		
		
		// Gets the Facebook manage_pages permisson status.
		
		$fb_manage_pages = $this->__facebookFanpagePermission();
		
		if ($fb_manage_pages)
		{
			// Gets the administered stores and extracts the stores ids.
			
			$administeredStores = $this->StoreAdministrator->find('all',
				array(
					'conditions' => array(
						'User.id' => $this->loggedUser['User']['id'],
						'StoreAdministrator.is_deleted' => false,
						'Store.is_deleted' => false
					)
				)
			);
			
			$administeredStoresIds = Set::extract('/Store/id', $administeredStores);
			
			
			// Gets the Facebook page stores waiting to install the application.
			
			$stores = array();
			$stores_waiting_install = array();
			
			foreach ($administeredStoresIds as $key => $value)
			{
				$administeredStore = $this->__store($value);
				
				if (!empty($administeredStore['FacebookPage']['id']) && !$administeredStore['FacebookPage']['is_installed'])
				{
					$stores_waiting_install[] = $administeredStore;
				}
				else if (!empty($administeredStore) && $administeredStore['Store']['is_enabled'])
				{
					$stores[] = $administeredStore;
				}
			}
		}
		else
		{
			$stores = array();
			$stores_waiting_install = array();
		}
		
		
		// Checks if the updated product has been submitted.
		
		if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data['Product']))
		{
			// Clean up submitted data.
			
			$clean_data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));
			
			$data = array(
				'Product' => $clean_data['Product'],
				'Photo' => $clean_data['Photo']
			);
			
			$data['Product']['id'] = $product['Product']['id'];
			$data['Product']['is_deleted'] = false;
			
			
			// Removes the photos fields that was submitted in blank.
			
			foreach ($data['Photo'] as $key => $value)
			{
				if (empty($value['filename']['name']))
				{
					unset($data['Photo'][$key]);
				}
			}
			
			
			// Validates and saves the submitted data.
			
			if ($this->Product->saveAll($data, array('validate' => 'first')))
			{
				// Checks if was selected a store to publish the product.
				
				$storesIds = array();
				
				if (!empty($this->request->data['Store']))
				{
					foreach ($this->request->data['Store'] as $key => $value)
					{
						$administeredStore = $this->__administeredStore($value);
						
						if (!empty($administeredStore))
						{
							$storesIds[] = $administeredStore['Store']['id'];
						}
					}
				}
				
				
				// Selects the personal store if no store has been selected.
				
				if (empty($storesIds))
				{
					$personalStore = $this->Store->find('first',
						array(
							'conditions' => array(
								'Store.user_id' => $this->loggedUser['User']['id'],
								'Store.is_personal' => true,
								'Store.is_deleted' => false
							)
						)
					);
					
					$storesIds[] = $personalStore['Store']['id'];
				}
				
				
				// Checks if the product was already published in a store that has not been selected and removes.
				
				$unselectedStores = $this->StoreProduct->find('all',
					array(
						'conditions' => array(
							'NOT' => array(
								'StoreProduct.store_id' => $storesIds
							),
							'StoreProduct.product_id' => $product['Product']['id'],
							'StoreProduct.is_deleted' => false
						)
					)
				);
				
				foreach ($unselectedStores as $key => $value)
				{
					$this->StoreProduct->id = $value['StoreProduct']['id'];
					$this->StoreProduct->saveField('is_deleted', true);
				}
				
				
				// Connects the product to selected stores.
				
				foreach ($storesIds as $key => $value)
				{
					$storeProduct = $this->StoreProduct->find('first',
						array(
							'conditions' => array(
								'StoreProduct.store_id' => $value,
								'StoreProduct.product_id' => $product['Product']['id']
							)
						)
					);
				
					if (empty($storeProduct))
					{
						$this->StoreProduct->create();
						$this->StoreProduct->save(
							array(
								'store_id' => $value,
								'product_id' => $product['Product']['id']
							)
						);
					}
					else
					{
						$this->StoreProduct->id = $storeProduct['StoreProduct']['id'];
						$this->StoreProduct->saveField('is_deleted', false);
					}
				}
				
				
				// Publishes the product photo.
				
				if (isset($data['Product']['publish']) && $data['Product']['publish'] == 1)
				{
					$storeProducts = $this->StoreProduct->find('all',
						array(
							'conditions' => array(
								'StoreProduct.product_id' => $product['Product']['id'],
								'StoreProduct.is_deleted' => false
							)
						)
					);
					
					foreach ($storeProducts as $key => $value)
					{
						$this->__publishPhoto($value['StoreProduct']['id']);
					}
				}
				
				
				$this->redirect(array('controller' => 'stores', 'action' => 'view', 'store' => $store['Store']['slug']), null, true);
			}
		}
		else
		{
			$this->request->data = $product;
			$this->request->data['Category'] = $product['Product']['Category']['ParentCategory'];
			$this->request->data['Product']['quantity'] = $product['Product']['quantity_available'];
			
			if ($this->request->data['Product']['quantity'] <= 0)
			{
				$this->request->data['Product']['quantity'] = 1;
			}
			
			
			// Gets all the stores where the product was published.
			
			$storesProduct = $this->StoreProduct->find('all',
				array(
					'conditions' => array(
						'StoreProduct.product_id' => $product['Product']['id'],
						'StoreProduct.is_deleted' => false
					)
				)
			);
			
			$this->request->data['Store'] = Set::extract('/Store/id', $storesProduct);
		}
		
		
		$this->set(compact('product', 'stores', 'stores_waiting_install', 'fb_manage_pages'));
		$this->set('title_for_layout', $product['Product']['title']);
	}
	
	
/**
 * Deletes the specified product.
 *
 * @param mixed $product
 * @param mixed $store
 */
	public function admin_delete($product, $store)
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		
		
		// Gets the Facebook manage_pages permisson status.
		
		$fb_manage_pages = $this->__facebookFanpagePermission();
		
		
		// Checks if the specified product exists and user has administrator permissions.
		
		list($product, $store) = $this->__administeredStoreProduct($product, $store);
		
		if (empty($store))
		{
			$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true), null, true);
		}
		else if (empty($product))
		{
			$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true, 'store' => $store['Store']['slug']), null, true);
		}
		
		
		// Checks if the user has confirmed the deletion.
		
		if (!empty($this->request->data['confirm']))
		{
			// Deletes the product.
			
			$this->Product->id = $product['Product']['id'];
			$this->Product->saveField('is_deleted', true);
			
			
			$this->redirect(array('controller' => 'stores', 'action' => 'view', 'admin' => true, 'store' => $store['Store']['slug']), null, true);
		}
		
		
		$this->set(compact('product'));
		$this->set('title_for_layout', $product['Product']['title']);
	}
	
	
/**
 * Performs the specified product purchase.
 *
 * @param mixed $product
 */
	public function api_buy($product)
	{
		// Reports that was executed successfully.
		
		$success = false;
		
		
		// Created transaction.
		
		$transaction = array();
		
		
		// Checks if the specified product is valid.
		
		$product = $this->__product($product);
		
		if (empty($product) || (!empty($product) && ($product['Product']['is_deleted'] || $product['StoreProduct']['is_deleted'] || $product['Product']['quantity_available'] <= 0)))
		{
			return array($success, $transaction);
		}

		// Gets the product store.
		
		$store = $this->__store($product['Store']['id']);
		
		// Checks if the product store is administered by user.
		
		$administeredStore = $this->__administeredStore($product['Store']['id']);
		
		if (!empty($administeredStore))
		{
			return array($success, $transaction);
		}
		
		$paypalData = $this->Session->read('ppFakeTransactionData-' . $product['StoreProduct']['hash']);
		// Gets the specified product quantity.
		
		if (!empty($this->request->data['Product']['quantity']))
		{
			$quantity = intval($this->request->data['Product']['quantity']);
		}
		else
		{
			$quantity = 1;
		}
		
		if ($product['Product']['quantity_available'] < $quantity)
		{
			$quantity = $product['Product']['quantity_available'];
		}
		
		if (empty($this->request->data['Product']))
		{
			$this->request->data['Product'] = array();
		}
		
		$this->request->data['Product']['quantity'] = $quantity;
		
		
		// Checks if the purchase confirmation was submitted.
		
		if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data['confirm']) || ($this->request->is('get') && isset($this->request->query['action']) && $this->request->query['action'] == 'ppReturn'))
		{
			$clean_data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));

			$session_data = $this->Session->read('ppFakeTransactionData-' . $product['StoreProduct']['hash']);
			
			if ($session_data) {
				$clean_data = $session_data;
			}

			if (isset($clean_data['Delivery']['Address']))
			{
				$clean_data['Delivery']['Address']['address'] = $clean_data['Delivery']['Address']['address'] . ', ' . $clean_data['Delivery']['Address']['addressNumber'];
				unset($clean_data['Delivery']['Address']['addressNumber']);
			}
			
			$data = array(
				'Buyer' => $clean_data['Buyer']
			);
			
			
			// Gets the delivery price for specified delivery address.
			
			$delivery_price = 0;
			
			if (isset($clean_data['Delivery']['Address']) && (!empty($clean_data['Delivery']['Address']['zipcode']) || !empty($clean_data['Delivery']['Address']['id'])))
			{
				if (!empty($clean_data['Delivery']['Address']['id']))
				{
					$address = $this->Address->find('first',
						array(
							'fields' => array('addressee', 'address', 'district', 'state', 'city', 'zipcode'),
							'conditions' => array(
								'Address.id' => $clean_data['Delivery']['Address']['id'],
								'Address.model' => 'User',
								'Address.foreign_key' => $this->loggedUser['User']['id'],
								'Address.is_deleted' => false
							)
						)
					);
					
					if (!empty($address))
					{
						$data['Delivery'] = array(
							'Address' => $address['Address']
						);
					}
				}
				else
				{
					$data['Delivery'] = array(
						'Address' => $clean_data['Delivery']['Address']
					);
				}
				
				if (!empty($data['Delivery']['Address']['zipcode']) && !empty($clean_data['Delivery']['service']))
				{
					$delivery = $this->requestAction(array('controller' => 'delivery', 'action' => 'index', 'api' => true), array('pass' => array($product['StoreProduct']['hash']), 'named' => array('zipcode' => $data['Delivery']['Address']['zipcode'], 'quantity' => $quantity, 'company' => 'Correios', 'service' => $clean_data['Delivery']['service'])));
					
					if (!empty($delivery))
					{
						$delivery_price = floatval(str_replace(',', '.', $delivery['price']));
						
						$data['Delivery']['company'] = 'Correios';
						$data['Delivery']['service'] = $delivery['service'];
						$data['Delivery']['service_code'] = $delivery['id'];
					}
				}
				
				$data['Delivery']['price'] = $delivery_price;
			}
			
			
			// Sets the additional transaction attributes.
			
			$data['Buyer']['id'] = $this->loggedUser['User']['id'];
			$data['Transaction']['product_id'] = $product['StoreProduct']['id'];
			$data['Transaction']['store_id'] = $store['Store']['id'];
			$data['Transaction']['price'] = $product['Product']['price'];
			$data['Transaction']['delivery'] = $delivery_price;
			$data['Transaction']['quantity'] = $quantity;
			
			
			// Stores the transaction.
			
			$this->Transaction->contain(array('Buyer', 'Delivery' => array('Address')));
			$this->Transaction->create();
			
			if ($this->Transaction->saveAll($data, array('validate' => 'first', 'deep' => true)))
			{
				$success = true;
				$transactionId = $this->Transaction->getLastInsertId();
				
				
				// Updates the product sold quantity.
				
				$this->Product->id = $product['Product']['id'];
				$this->Product->saveField('quantity_sold', $product['Product']['quantity_sold'] + $quantity);
				
				
				// Creates the transaction update entry.
				
				$this->TransactionUpdate->create();
				$this->TransactionUpdate->save(
					array(
						'TransactionUpdate' => array(
							'transaction_id' => $transactionId,
							'status' => 'started'
						)
					)
				);
				
				
				// Adds the delivery address to user.
				
				if (empty($clean_data['Delivery']['Address']['id']) && !empty($data['Delivery']['Address']))
				{
					$address = array(
						'Address' => $data['Delivery']['Address']
					);
					
					$address['Address']['foreign_key'] = $this->loggedUser['User']['id'];
												
					$this->User->Address->save($address);
				}
				
				
				// Sends the transaction.
				
				$transaction = $this->__transaction($transactionId);
				
				if (!empty($transaction))
				{
					// Sends the buyer email.
					
					$this->CommunicationQueue->sendBuyerNotification($transaction['Buyer']['email'], array('productTitle' => $product['Product']['title'], 'productUrl' => $product['StoreProduct']['url'], 'buyerName'=> $transaction['Buyer']['name'], 'sellerEmail' => $this->loggedUser['User']['email'], 'sellerName' => $this->loggedUser['User']['name']));
					
					if (!empty($transaction['Buyer']['username']))
					{
						$this->CommunicationQueue->sendBuyerNotification($transaction['Buyer']['username'] . '@facebook.com', array('productTitle' => $product['Product']['title'], 'productUrl' => $product['StoreProduct']['url'], 'buyerName'=> $transaction['Buyer']['name'], 'sellerEmail' => $this->loggedUser['User']['email'], 'sellerName' => $this->loggedUser['User']['name']));
					}
					
					
					// Gets the product administrators and send email to them.
								
					$administrators = $this->StoreAdministrator->find('all',
						array(
							'conditions' => array(
								'Store.id' => $store['Store']['id'],
								'StoreAdministrator.is_deleted' => false
							)
						)
					);

					if (empty($transaction['Delivery']['Address'])) {
						$deliveryAddress = '';
					
					} else {
						$deliveryAddress = "O comprador solicitou que o produto seja entregue no seguinte endereço:

CEP: ".$transaction['Delivery']['Address']['zipcode'] ."
Endereço: ". $transaction['Delivery']['Address']['address'] . ' - ' . $transaction['Delivery']['Address']['address_line2'] ."
Bairro: ". $transaction['Delivery']['Address']['district'] ."
Cidade: ". $transaction['Delivery']['Address']['city'] ."
Estado: ". $transaction['Delivery']['Address']['state'];
					}
					
					foreach ($administrators as $key => $value)
					{
						$this->CommunicationQueue->sendSellerNotification($value['User']['email'], array('storeTitle' => $product['Store']['title'], 'productTitle' => $product['Product']['title'], 'productUrl' => $product['StoreProduct']['url'], 'buyerName'=> $transaction['Buyer']['name'], 'buyerEmail'=> $transaction['Buyer']['email'], 'sellerEmail' => $this->loggedUser['User']['email'], 'deliveryAddress' => $deliveryAddress));
						if (!empty($value['User']['username']))
						{
							$this->CommunicationQueue->sendSellerNotification($value['User']['username'] . '@facebook.com', array('storeTitle' => $product['Store']['title'], 'productTitle' => $product['Product']['title'], 'productUrl' => $product['StoreProduct']['url'], 'buyerName'=> $transaction['Buyer']['name'], 'buyerEmail'=> $transaction['Buyer']['email'], 'sellerEmail' => $this->loggedUser['User']['email'], 'deliveryAddress' => $deliveryAddress));
						}
					}
				}
			}
		}
		
		
		$this->set(compact('success', 'transaction', 'product', 'store', 'quantity'));
		$this->set('_serialize', array('success', 'transaction', 'product', 'store', 'quantity'));
		
		return array($success, $transaction);
	}
	
	
	
/**
 * Publishes the product in the store's album.
 *
 * @param mixed $product
 */
	private function __publishPhoto($product)
	{
		// Gets the product and store.
		
		list($product, $store) = $this->__administeredStoreProduct($product);
		
		if (!empty($store))
		{
			// Defines the photo settings.
			
			$photo_settings = array(
				'image' => '@' . WWW_ROOT . $product['Product']['Photo'][0]['dir'] . DS . $product['Product']['Photo'][0]['filename'],
				'title' => $product['Product']['title'],
				'price' => CakeNumber::currency($product['Product']['price'], 'BRR'),
				'condition' => __d('controller', 'Products.admin_add.product.' . $product['Product']['condition']),
				'description' => $product['Product']['description']
			);
			
			
			// Defines the album settings.
			
			$album_settings = array(
				'description' => $store['Store']['description']
			);
			
			
			// Sets the publish settings and gets the access token.
			
			if (!empty($store['FacebookPage']['id']))
			{
				$photo_settings['path'] = $store['FacebookPage']['link'] . '&app_data=/fp/' . $store['Store']['slug'] . '/'. $product['StoreProduct']['slug'];
				$album_settings['path'] = $store['FacebookPage']['link'] . '&app_data=/fp/' . $store['Store']['slug'];
			
				try
				{
					// Loads the Facebook page.
				
					$fb_page = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'], 'GET', array('fields' => array('access_token')));
					
					if (!empty($fb_page['access_token']))
					{
						$access_token = $fb_page['access_token'];
					}
				}
				catch (FacebookApiException $e)
				{
				}
			}
			else if ($store['Store']['is_personal'])
			{
				$photo_settings['path'] = Configure::read('Facebook.redirect') . '/products/' . $product['StoreProduct']['slug'];
				$album_settings['path'] = Configure::read('Facebook.redirect') . '/stores/' . $store['Store']['slug'];
				
				$access_token = $this->FB->getAccessToken();
			}
			
			if (!empty($access_token))
			{
				// Checks if an album has been previously created and is still valid.
			
				if (!empty($store['Store']['fb_album_id']))
				{
					$fb_album = $this->FB->api('/' . $product['Store']['fb_album_id'], 'GET', array('access_token' => $access_token));
				}
				
				
				// Creates a new album if necessary.
				
				if (!empty($fb_album))
				{
					$fb_album_id = $product['Store']['fb_album_id'];
				}
				else
				{
					try
					{
						if (!empty($store['FacebookPage']['id']))
						{
							$name = String::insert(__d('controller', 'Products.publishPhoto.fb_album.page.name'), array('name' => $store['Store']['title']));
							$message = String::insert(__d('controller', 'Products.publishPhoto.fb_album.page.message'), $album_settings);
							
							$fb_album = $this->FB->api('/' . $store['FacebookPage']['fb_page_id'] . '/albums', 'POST', array('access_token' => $access_token, 'name' => $name, 'message' => $message));
						}
						else
						{
							$name = String::insert(__d('controller', 'Products.publishPhoto.fb_album.personal.name'), array('name' => $store['Store']['title']));
							$message = String::insert(__d('controller', 'Products.publishPhoto.fb_album.personal.message'), $album_settings);
							
							$fb_album = $this->FB->api('/me/albums', 'POST', array('access_token' => $access_token, 'name' => $name, 'message' => $message));
						}
						
						
						// Checks if the Facebook album was created and stores the reference id.
						
						if (!empty($fb_album))
						{
							$fb_album_id = $fb_album['id'];
							
							$this->Store->id = $store['Store']['id'];
							$this->Store->saveField('fb_album_id', $fb_album_id);
						}
					}
					catch (FacebookApiException $e)
					{
					}
				}
				
				
				// Checks if the product has already been published previously and excludes existing photo.
				
				if (!empty($product['StoreProduct']['fb_photo_id']))
				{
					try
					{
						$this->FB->api('/' . $product['StoreProduct']['fb_photo_id'], 'DELETE', array('access_token' => $access_token));
					}
					catch (FacebookApiException $e)
					{
					}
				}
				
				
				// Publishes the new photo.
				
				if (!empty($fb_album_id))
				{
					try
					{
						if (!empty($store['FacebookPage']['id']))
						{
							$message = String::insert(__d('controller', 'Products.publishPhoto.fb_photo.page.message'), $photo_settings);
						}
						else
						{
							$message = String::insert(__d('controller', 'Products.publishPhoto.fb_photo.personal.message'), $photo_settings);
						}
						
						$fb_photo = $this->FB->api('/' . $fb_album_id . '/photos', 'POST', array('access_token' => $access_token, 'image' => $photo_settings['image'], 'message' => $message));
						
						
						// Checks if the Facebook photo was created and stores the reference id.
						
						if (!empty($fb_photo))
						{
							$fb_photo_id = $fb_photo['id'];
							
							$this->StoreProduct->id = $product['StoreProduct']['id'];
							$this->StoreProduct->saveField('fb_photo_id', $fb_photo_id);
						}
					}
					catch (FacebookApiException $e)
					{
					}
				}
			}
		}
	}

	

	
	public function paypalAdaptiveAccountAction() {
		
		$this->layout = 'empty';
		$returnUrl = $this->here . '?action=ppReturn'; 	
		
		
		if (isset($this->request->query['action']) && $this->request->query['action'] == 'checkVerified' && isset($this->loggedUser['User']['id'])) {

			$paypalData = $this->Session->read('paypalData');

			// User does not have the paypaldata in session, so he should not be here.
			if (empty($paypalData)) {
				$here = explode('/paypalAccount', $this->here);
				
				$this->redirect($here[0], null, true);
			}
			
			
			$cURL_header = array(
					'X-PAYPAL-APPLICATION-ID: ' . Configure::read('Paypal.API.appId'),
					'X-PAYPAL-SECURITY-USERID:' . Configure::read('Paypal.API.username'),
					'X-PAYPAL-SECURITY-PASSWORD:' . Configure::read('Paypal.API.password'),
					'X-PAYPAL-SECURITY-SIGNATURE:' . Configure::read('Paypal.API.signature'),
					'X-PAYPAL-DEVICE-IPADDRESS:' . $this->RequestHandler->getClientIp(),
					'X-PAYPAL-REQUEST-DATA-FORMAT: JSON',
					'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
				);
				
				
			$paypalData["firstName"]		= $paypalData['name']['firstName'];
			$paypalData["lastName"]			= $paypalData['name']['lastName'];
			$paypalData["matchCriteria"]	= 'NAME';
			
			
			
			$paypalCreateAccount = curl_init();
			curl_setopt($paypalCreateAccount, CURLOPT_URL, Configure::read('Paypal.API.endpoint') . '/AdaptiveAccounts/GetVerifiedStatus');
			curl_setopt($paypalCreateAccount, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($paypalCreateAccount, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($paypalCreateAccount, CURLOPT_POST, 1);
			curl_setopt($paypalCreateAccount, CURLOPT_HTTPHEADER, $cURL_header);
			curl_setopt($paypalCreateAccount, CURLOPT_POSTFIELDS, json_encode($paypalData));
				
			$paypalCreateAccount_response = json_decode(curl_exec($paypalCreateAccount));
			
			if (isset($paypalCreateAccount_response->accountStatus)) {
				if ($this->checkIfPaypalEmailIsBillable($paypalData['emailAddress'])) {
					$this->fillPaypalEmail($paypalData['emailAddress']);
					$status = 1;
					
				} else {
					$status = 0;
					
				}
				
			} else {
				$status = -1;
				
			}
				
				
			
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header("Content-type: application/json");
			echo "{\"emailStatus\":$status}";
			exit;
			
		} else
		if (isset($this->request->data['paypalAdaptiveAccount'])) {

			$paypalAdaptiveAccount = $this->request->data['paypalAdaptiveAccount'];
		
			$cURL_header = array(
					'X-PAYPAL-APPLICATION-ID: ' . Configure::read('Paypal.API.appId'),
					'X-PAYPAL-SECURITY-USERID:' . Configure::read('Paypal.API.username'),
					'X-PAYPAL-SECURITY-PASSWORD:' . Configure::read('Paypal.API.password'),
					'X-PAYPAL-SECURITY-SIGNATURE:' . Configure::read('Paypal.API.signature'),
					'X-PAYPAL-DEVICE-IPADDRESS:' . $this->RequestHandler->getClientIp(),
					'X-PAYPAL-REQUEST-DATA-FORMAT: JSON',
					'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
				);
				
				
			$paypalData["accountType"] = "Premier";
			$paypalData["useMiniBrowser"] = true;
			$paypalData["name"]    = array('firstName' => $paypalAdaptiveAccount['firstName'], 'lastName' => $paypalAdaptiveAccount['lastName']);
			$paypalData["address"] = array(	'line1'			=> $paypalAdaptiveAccount['address'],
											'line2'			=> $paypalAdaptiveAccount['addressLine2'],
											'city'			=> $paypalAdaptiveAccount['city'],
											'state'			=> $paypalAdaptiveAccount['state'],
											'postalCode'	=> preg_replace("/[^0-9]/","",$paypalAdaptiveAccount['zipcode']),
											'countryCode'	=> 'BR'
										);
			$paypalData['contactPhoneNumber']		= $paypalAdaptiveAccount['phone'];				
			$paypalData['citizenshipCountryCode']	= 'BR';
			$paypalData['dateOfBirth'] 				= date("Y-m-d\Z", strtotime($paypalAdaptiveAccount['birthday']));;
			$paypalData['returnUrl'] = $returnUrl;#, 'returnUrlDescription' => 'BazzApp');
			$paypalData['currencyCode'] 			= 'BRL';
			$paypalData['emailAddress'] 			= $paypalAdaptiveAccount['paypalEmail'];
			$paypalData['taxId']			 		=  preg_replace("/[^0-9]/","",$paypalAdaptiveAccount['uniqueIdentifierNumber']);
			$paypalData['preferredLanguageCode'] 	= 'pt_BR';
			$paypalData['registrationType'] 		= 'Web';
			$paypalData['requestEnvelope'] 			= array('errorLanguage'=>'pt_BR');
			$paypalData['showAddCreditCard']		= false;

			$this->Session->write('paypalData', $paypalData, 1200);
 			// Establishes communication with Paypal server and generate a payment key.
			$paypalCreateAccount = curl_init();
			curl_setopt($paypalCreateAccount, CURLOPT_URL, Configure::read('Paypal.API.endpoint') . '/AdaptiveAccounts/CreateAccount');
			curl_setopt($paypalCreateAccount, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($paypalCreateAccount, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($paypalCreateAccount, CURLOPT_POST, 1);
			curl_setopt($paypalCreateAccount, CURLOPT_HTTPHEADER, $cURL_header);
			curl_setopt($paypalCreateAccount, CURLOPT_POSTFIELDS, json_encode($paypalData));
				
			$paypalCreateAccount_response = json_decode(curl_exec($paypalCreateAccount));
			curl_close($paypalCreateAccount);
			CakeLog::write('paypal', 'CREATEACCOUNT - ' . serialize($paypalCreateAccount_response));
			$this->Session->write('paypalResponse', $paypalCreateAccount_response, 1200);

			$this->set('paypalCreateAccountKey', $paypalCreateAccount_response->createAccountKey);
			if ($paypalCreateAccount_response->responseEnvelope->ack == 'Success') {
				// Lets save some data to ourselves
				$personalStoreId = $this->loggedUser['Store']['id'];
				if ($paypalAdaptiveAccount['hasAddress'] == '') {
					$data = array('model'=>'Store', 'foreign_key'=>$personalStoreId, 'address'=>$paypalAdaptiveAccount['address'], 'address_line2'=>$paypalAdaptiveAccount['addressLine2'], 'district'=>$paypalAdaptiveAccount['district'], 'state'=>$paypalAdaptiveAccount['state'], 'city'=>$paypalAdaptiveAccount['city'], 'zipcode'=>preg_replace("/[^0-9]/","",$paypalAdaptiveAccount['zipcode']));

					$this->Address->create();
					$this->Address->saveAll($data, array('validate' => 'first'));
				}
				
				if ($paypalAdaptiveAccount['hasPhone'] == '') {
					$data = array('model'=>'Store', 'foreign_key'=>$personalStoreId, 'number'=>$paypalAdaptiveAccount['phone']);

					$this->Phone->create();
					$this->Phone->saveAll($data);
				}

				$cpf = preg_replace("/[^0-9]/","",$paypalAdaptiveAccount['uniqueIdentifierNumber']);
				$sql = "UPDATE bazzapp_users SET uniqueIdentifierNumber='$cpf', country='BR' WHERE id=" . $this->loggedUser['User']['id'];
				$this->User->query($sql);

				$this->set('paypalRedirectUrl', $paypalCreateAccount_response->redirectURL);
				#$this->redirect($paypalCreateAccount_response->redirectURL, null, true);
				

			} else {
				$errorMsg = "Desculpe, houve um erro ao tentar criar sua conta. Por favor, tente novamente.";
				
				
				$this->set('errorMsg', $errorMsg);
			}
		}
		
		
		$paypalAdaptiveAccount = array();
		$paypalAdaptiveAccount['firstName'] 	= null;
		$paypalAdaptiveAccount['lastName']		= null;
		$paypalAdaptiveAccount['birthday']		= null;
		$paypalAdaptiveAccount['email']			= null;
		$paypalAdaptiveAccount['zipcode']		= null;
		$paypalAdaptiveAccount['address'] 		= null;
		$paypalAdaptiveAccount['addressLine2']	= null;
		$paypalAdaptiveAccount['district']		= null;
		$paypalAdaptiveAccount['state']			= null;
		$paypalAdaptiveAccount['city']			= null;
		$paypalAdaptiveAccount['uniqueIdentifierNumber']	= null;
		$paypalAdaptiveAccount['phone']			= null;
		$paypalAdaptiveAccount['hasPhone']		= null;
		$paypalAdaptiveAccount['hasAddress']	= null;
		
		
		if (!isset($this->loggedUser['Store']['PaypalAccount']['email'])) {

			$userName = explode(' ',$this->loggedUser['User']['name']);
			
			$paypalAdaptiveAccount['firstName'] = $userName[0];
			$paypalAdaptiveAccount['lastName']	= $userName[count($userName)-1];
			$paypalAdaptiveAccount['birthday']	= substr($this->loggedUser['User']['birthday'],8,2) . '/' . substr($this->loggedUser['User']['birthday'],5,2) . '/' . substr($this->loggedUser['User']['birthday'],0,4);
			$paypalAdaptiveAccount['email']		= $this->loggedUser['User']['email'];
			$paypalAdaptiveAccount['uniqueIdentifierNumber']		= $this->loggedUser['User']['uniqueIdentifierNumber'];
			
			$phone = $this->Phone->find('first', array('conditions'=> array('model'=>'Store', 'foreign_key'=>$this->loggedUser['Store']['id'])));
			if ($phone) {
				$paypalAdaptiveAccount['hasPhone'] = true;
				$paypalAdaptiveAccount['phone']	= $phone['Phone']['number'];
			}
			
			$address = $this->Address->find('first', array('conditions'=> array('model'=>'User', 'foreign_key'=>$this->loggedUser['User']['id'])));
			if ($address) {
				$paypalAdaptiveAccount['hasAddress']	= true;
				$paypalAdaptiveAccount['zipcode']		= $address['Address']['zipcode'];
				$paypalAdaptiveAccount['address'] 		= $address['Address']['address'];
				$paypalAdaptiveAccount['addressLine2']	= $address['Address']['address_line2'];
				$paypalAdaptiveAccount['district']		= $address['Address']['district'];
				$paypalAdaptiveAccount['state']			= $address['Address']['state'];
				$paypalAdaptiveAccount['city']			= $address['Address']['city'];
			
			}
		}
		
		$this->set('paypalAdaptiveAccount', $paypalAdaptiveAccount);
		$this->render('/Products/admin_add_paypalAccount');
		
	}
}
