<?php
App::uses('AppController', 'Controller');


class PurchasesController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Purchases';

/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Transaction', 'TransactionUpdate', 'ViewStoreQualification');
	
/**	
 * Paginator settings.
 *
 * @var array
 */
	public $paginate = array(
		'Transaction' => array(
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
					'FacebookPage',
					'User' => array(
						'FacebookUser'
					)
				),
				'StoreProduct' => array(
					'Product' => array(
						'Photo'
					)
				)
			),
			'order' => array(
				'Transaction.created' => 'desc'
			),
			'limit' => 10
		)
	);
	
	
	
/**
 * Purchase confirmation.
 *
 * @param string $transaction
 */
	public function view($transaction)
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		
		
		// Checks if the transaction exists and belongs to user.
		
		$transaction = $this->__transaction($transaction);
		
		if (empty($transaction) || (!empty($transaction) && $transaction['Transaction']['buyer_id'] != $this->loggedUser['User']['id']))
		{
			$this->redirect(array('controller' => 'purchases', 'action' => 'index', 'admin' => true), null, true);
		}
		
		// In case we are just opening the popup after an successful new product
		elseif ($this->request->is('get') && isset($this->request->query['v']) && !isset($this->request->query['goPaypal']))
		{
			$this->layout = 'empty';
			
			
			$this->set('transaction', $transaction);
			$this->render('/Purchases/view_ok_popup');
			
		} else
		// Redirect user to the paypal page. 
		if ($this->request->is('get') && isset($this->request->query['goPaypal']))
		{
			
			$qty			= $transaction['Transaction']['quantity'];
			$price			= $transaction['Transaction']['price'];
			$totalPrice		= $transaction['Transaction']['total_price'];
			$shippingCost	= $transaction['Transaction']['delivery']; 
			
			$cURL_header = array(
					'X-PAYPAL-APPLICATION-ID: ' . Configure::read('Paypal.API.appId'),
					'X-PAYPAL-SECURITY-USERID:' . Configure::read('Paypal.API.username'),
					'X-PAYPAL-SECURITY-PASSWORD:' . Configure::read('Paypal.API.password'),
					'X-PAYPAL-SECURITY-SIGNATURE:' . Configure::read('Paypal.API.signature'),
					'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
					'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
				);
				
				
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
							'cancelUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'cancel', 'admin' => false, 'payment' => $transaction['Transaction']['hash']), true),
							'returnUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'finish', 'admin' => false, 'payment' => $transaction['Transaction']['hash']), true),
							'ipnNotificationUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'notification', 'admin' => false, 'payment' => $transaction['Transaction']['hash'], 'ext' => 'json'), true),
							'trackingId' => $transaction['Transaction']['hash'],
							'requestEnvelope.errorLanguage' => 'pt_BR',
							'receiverList.receiver(0).email' => $transaction['Store']['PaypalAccount']['email'],
							'receiverList.receiver(0).amount' => $totalPrice,
							'receiverList.receiver(0).primary' => true,
							'receiverList.receiver(1).email' => Configure::read('Paypal.payment.BazzApp.email'),
							'receiverList.receiver(1).amount' => number_format(($totalPrice * (Configure::read('Paypal.payment.tax') / 100)) + (($totalPrice * $qty) * (Configure::read('Paypal.payment.BazzApp.tax') / 100)), 2, '.', ''),
							'receiverList.receiver(1).primary' => false,
							'receiverOptions(0).receiver.email' => $transaction['Store']['PaypalAccount']['email'],
							'receiverOptions(0).invoiceData.totalShipping' => $shippingCost,
							'receiverOptions(0).invoiceData.item(0).name' => $transaction['StoreProduct']['Product']['title'],
							'receiverOptions(0).invoiceData.item(0).identifier' => $transaction['StoreProduct']['Product']['hash'],
							'receiverOptions(0).invoiceData.item(0).price' => $price*$qty,
							'receiverOptions(0).invoiceData.item(0).itemPrice' => $price,
							'receiverOptions(0).invoiceData.item(0).itemCount' => $qty
						)
					)
				);
				
				$createPayKey_response = json_decode(curl_exec($createPayKey_cURL));
				#var_dump($createPayKey_response);
				
				curl_close($createPayKey_cURL);

				// Once upon a time there was an error with paypal
				if (!$createPayKey_response || strcasecmp($createPayKey_response->responseEnvelope->ack, 'success') < 0) {
					// Do nothing
										
					
				if (!empty($transaction))
				{
					// Gets the product administrators and send email to them.

					if ($createPayKey_response->error[0]->errorId == 569042) {
						$administrators = $this->StoreAdministrator->find('all',
							array(
								'conditions' => array(
									'Store.id' => $transaction['Store']['id'],
									'StoreAdministrator.is_deleted' => false
								)
							)
						);
					
						$product = $this->__product($transaction['Transaction']['product_id']);
						
						foreach ($administrators as $key => $value)
						{
							$this->CommunicationQueue->sendSellerNotification($value['User']['email'], array('storeTitle' => $product['Store']['title'], 'productTitle' => $product['Product']['title'], 'productUrl' => $product['StoreProduct']['url']));
						
						}
					}
				}
					
				// Ok, let's open the modal
				} else {

					$store = $this->__store($transaction['Store']['id']);
					$urlPrefix = explode('app_data=', $store['Store']['appUrl'], 1);
					
					if (!$store['Store']['is_personal']) {
						$urlPrefix = $urlPrefix[0] . '/fp';
					}
					
					$paypalRedirectUrl = $urlPrefix . $this->here;
					$this->Session->write('paypalRedirectUrl', $paypalRedirectUrl);
					
					$data = array(
						'Payment' => array(
							'model' => 'Transaction',
							'foreign_key' => $transaction['Transaction']['id'],
							'method' => 'paypal',
							'status' => 'created',
							'key'	 => $createPayKey_response->payKey
						)
					);
			
			
					$this->Transaction->Payment->create();
					$this->Transaction->Payment->save($data);
					
					$this->TransactionUpdate->create();
					$this->TransactionUpdate->save(
						array(
							'TransactionUpdate' => array(
								'transaction_id' => $transaction['Transaction']['id'],
								'status' => 'payment created'
							)
						)
					);
					
					$this->set('paypalPayKey', $createPayKey_response->payKey);
					$this->set(compact('transaction'));
					$this->set('title_for_layout', $transaction['StoreProduct']['Product']['title']);
			
					$this->render('/Purchases/goPaypal');
										
				}
			
			
		}

		$this->set(compact('transaction'));
		$this->set('title_for_layout', $transaction['StoreProduct']['Product']['title']);
	}
	
	
/**
 * Fanpage Purchase confirmation.
 *
 * @param string $transaction
 */
	public function fanpage_view($transaction, $store)
	{
		// Apply 'fanpage_default' layout.
		
		$this->layout = 'fanpage_default';
		
		
		// Checks if the specified store is valid.
		
		$store = $this->__fanpageStore($store);
		
		if (empty($store))
		{
			$this->redirect(array('controller' => 'products', 'action' => 'home', 'fanpage' => true), null, true);
		}
		
		
		// Checks if the transaction exists and belongs to user.
		
		$transaction = $this->__transaction($transaction);
		
		if (empty($transaction) || (!empty($transaction) && $transaction['Transaction']['buyer_id'] != $this->loggedUser['User']['id']))
		{
			$this->redirect(array('controller' => 'products', 'action' => 'home', 'fanpage' => true, 'store' => $store['Store']['slug']), null, true);
		}
		
		
	// In case we are just opening the popup after an successful new product
		elseif ($this->request->is('get') && isset($this->request->query['v']) && !isset($this->request->query['goPaypal']))
		{
			$this->layout = 'empty';
			
			
			$this->set('transaction', $transaction);
			$this->render('/Purchases/view_ok_popup');
			
		} else
		// Redirect user to the paypal page. 
		if ($this->request->is('get') && isset($this->request->query['goPaypal']))
		{
			
			$qty			= $transaction['Transaction']['quantity'];
			$price			= $transaction['Transaction']['price'];
			$totalPrice		= $transaction['Transaction']['total_price'];
			$shippingCost	= $transaction['Transaction']['delivery']; 
			
			$cURL_header = array(
					'X-PAYPAL-APPLICATION-ID: ' . Configure::read('Paypal.API.appId'),
					'X-PAYPAL-SECURITY-USERID:' . Configure::read('Paypal.API.username'),
					'X-PAYPAL-SECURITY-PASSWORD:' . Configure::read('Paypal.API.password'),
					'X-PAYPAL-SECURITY-SIGNATURE:' . Configure::read('Paypal.API.signature'),
					'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
					'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON'
				);
				
				
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
							'cancelUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'cancel', 'admin' => false, 'payment' => $transaction['Transaction']['hash']), true),
							'returnUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'finish', 'admin' => false, 'payment' => $transaction['Transaction']['hash']), true),
							'ipnNotificationUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'notification', 'admin' => false, 'payment' => $transaction['Transaction']['hash'], 'ext' => 'json'), true),
							'trackingId' => $transaction['Transaction']['hash'],
							'requestEnvelope.errorLanguage' => 'pt_BR',
							'receiverList.receiver(0).email' => $transaction['Store']['PaypalAccount']['email'],
							'receiverList.receiver(0).amount' => $totalPrice,
							'receiverList.receiver(0).primary' => true,
							'receiverList.receiver(1).email' => Configure::read('Paypal.payment.BazzApp.email'),
							'receiverList.receiver(1).amount' => number_format(($totalPrice * (Configure::read('Paypal.payment.tax') / 100)) + (($totalPrice * $qty) * (Configure::read('Paypal.payment.BazzApp.tax') / 100)), 2, '.', ''),
							'receiverList.receiver(1).primary' => false,
							'receiverOptions(0).receiver.email' => $transaction['Store']['PaypalAccount']['email'],
							'receiverOptions(0).invoiceData.totalShipping' => $shippingCost,
							'receiverOptions(0).invoiceData.item(0).name' => $transaction['StoreProduct']['Product']['title'],
							'receiverOptions(0).invoiceData.item(0).identifier' => $transaction['StoreProduct']['Product']['hash'],
							'receiverOptions(0).invoiceData.item(0).price' => $price*$qty,
							'receiverOptions(0).invoiceData.item(0).itemPrice' => $price,
							'receiverOptions(0).invoiceData.item(0).itemCount' => $qty
						)
					)
				);
				
				$createPayKey_response = json_decode(curl_exec($createPayKey_cURL));
				#var_dump($createPayKey_response);
				
				curl_close($createPayKey_cURL);

				// Once upon a time there was an error with paypal
				if (!$createPayKey_response || strcasecmp($createPayKey_response->responseEnvelope->ack, 'success') < 0) {
					// Do nothing
										
					
				if (!empty($transaction))
				{
					// Gets the product administrators and send email to them.

					if ($createPayKey_response->error[0]->errorId == 569042) {
						$administrators = $this->StoreAdministrator->find('all',
							array(
								'conditions' => array(
									'Store.id' => $transaction['Store']['id'],
									'StoreAdministrator.is_deleted' => false
								)
							)
						);
					
						foreach ($administrators as $key => $value)
						{
							$this->__sendEmail('atendimento@bazzapp.com', $value['User']['email'], $transaction['Buyer']['email'], String::insert(__d('controller', 'Products.api_buy.email.seller.subject'), array('product' => $transaction['StoreProduct']['Product']['title'])), 'transaction_paypalNotVerified', 'default', array('transaction' => $transaction), 'both', 9);
						
						}
					}
				}
					
				// Ok, let's open the modal
				} else {

					$store = $this->__store($transaction['Store']['id']);
					$urlPrefix = explode('app_data=', $store['Store']['appUrl'], 1);
					
					if (!$store['Store']['is_personal']) {
						$urlPrefix = $urlPrefix[0] . '/fp';
					}
					
					$paypalRedirectUrl = $urlPrefix . $this->here;
					$this->Session->write('paypalRedirectUrl', $paypalRedirectUrl);
					
					$data = array(
						'Payment' => array(
							'model' => 'Transaction',
							'foreign_key' => $transaction['Transaction']['id'],
							'method' => 'paypal',
							'status' => 'created',
							'key'	 => $createPayKey_response->payKey
						)
					);
			
			
					$this->Transaction->Payment->create();
					$this->Transaction->Payment->save($data);
					
					$this->TransactionUpdate->create();
					$this->TransactionUpdate->save(
						array(
							'TransactionUpdate' => array(
								'transaction_id' => $transaction['Transaction']['id'],
								'status' => 'payment created'
							)
						)
					);
					
					$this->set('paypalPayKey', $createPayKey_response->payKey);
					$this->set(compact('transaction'));
					$this->set('title_for_layout', $transaction['StoreProduct']['Product']['title']);
			
					$this->render('/Purchases/goPaypal');
										
				}
			
			
		}
		
		$this->set(compact('transaction', 'store'));
		$this->set('title_for_layout', $transaction['StoreProduct']['Product']['title']);
	}
	
	
/**
 * Lists the user purchases.
 *
 */	
	public function admin_index()
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		
		
		// Finds the transactions related to user as buyer.
		
		$transactions = $this->paginate('Transaction',
			array(
				'Transaction.buyer_id' => $this->loggedUser['User']['id']
			)
		);
		
		
		$store = $this->Store->find('first', array(
													'conditions'=> array('Store.is_personal'=>1, 'Store.user_id'=>$this->loggedUser['User']['id'])
												)
									);
		
		$store['Store']['totalFollowers'] = $this->Store->getTotalFollowers($store['Store']['id']);
		$this->set(compact('transactions', 'store'));
		$this->set('title_for_layout', __d('controller', 'Purchases.admin_index.title'));
	}
	
	
/**
 * Displays the specified transaction.
 *
 * @param string $transaction
 */	
	public function admin_view($transaction)
	{
		// Checks if the transaction exists and belongs to user.
		
		$transaction = $this->__transaction($transaction);
		if (!empty($transaction) && $transaction['Transaction']['buyer_id'] != $this->loggedUser['User']['id'])
		{
			$transaction = array();
		}
		
		$this->set(compact('transaction'));
	}
	
	
/**
 * Displays the sale qualification form and stores submitted data.
 *
 * @param string $transaction
 */	
	public function admin_qualify($transaction)
	{
		// Indicates if the transaction has already been qualified.
		$qualified = false;
		
		
		// Checks if the transaction exists and belongs to user.
		
		$transaction = $this->__transaction($transaction);
		
		if (!empty($transaction) && $transaction['Transaction']['buyer_id'] != $this->loggedUser['User']['id'])
		{
			$transaction = array();
		}
		
		
		// Checks if the transaction has already been qualified by the buyer.
		
		if (!empty($transaction['PurchaseQualification']['id']))
		{
			$qualified = true;
		}
		else if (!empty($transaction))
		{
			// Checks if the qualification was submitted and if so, stores the provided information.
		
			if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data['PurchaseQualification']))
			{
				$data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));
				$data['PurchaseQualification']['user_id'] = $this->loggedUser['User']['id'];
				$data['PurchaseQualification']['transaction_id'] = $transaction['Transaction']['id'];
				$data['PurchaseQualification']['method'] = 'purchase';
				
				
				$this->Transaction->PurchaseQualification->create();
				
				if ($this->Transaction->PurchaseQualification->save($data))
				{
					$qualified = true;					
					
					
					// Updates the stored qualifications number.
					
					$negative_qualifications = $this->ViewStoreQualification->find('count',
						array(
							'conditions' => array(
								'ViewStoreQualification.qualified_store_id' => $transaction['Store']['id'],
								'ViewStoreQualification.qualification' => 'negative'
							)
						)
					);
					
					$positive_qualifications = $this->ViewStoreQualification->find('count',
						array(
							'conditions' => array(
								'ViewStoreQualification.qualified_store_id' => $transaction['Store']['id'],
								'ViewStoreQualification.qualification' => 'positive'
							)
						)
					);
					
					$this->Transaction->Store->id = $transaction['Store']['id'];
					$this->Transaction->Store->saveField('qualification_negative', $negative_qualifications);
					$this->Transaction->Store->saveField('qualification_positive', $positive_qualifications);
					
					
					// Updates the transaction history.
					
					$this->TransactionUpdate->create();
					$this->TransactionUpdate->save(
						array(
							'TransactionUpdate' => array(
								'transaction_id' => $transaction['Transaction']['id'],
								'status' => 'purchase qualified'
							)
						)
					);
					
					$store   = __store($transaction['Transaction']['store_id']);
					$product = __product($transaction['Transaction']['product_id']);
					
					$to = $store['User']['email'];
					$qualification = $data['PurchaseQualification']['qualification'];
					$qualification = ($qualification == 'positive' ? 'positiva' : ($qualification == 'neutral' ? 'neutra' : 'negativa'));
					$transactionType = 'venda';
					$productTitle = $product['Product']['title'];
					$qualificationUrl = Configure::read('baseUrl') . '/stores/' . $store['Store']['slug'] . '/qualificatons';
					$this->CommunicationQueue->sendQualificationReceived($to, array('productTitle' => $productTitle, 'qualification'=> $qualification, 'transactionType'=> $transactionType, 'qualificationUrl' => $qualificationUrl));
				}
			}
		}
		
		
		$this->set(compact('transaction', 'qualified'));
	}
}
