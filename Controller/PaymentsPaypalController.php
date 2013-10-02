<?php
App::uses('AppController', 'Controller');


class PaymentsPaypalController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'PaymentsPaypal';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Transaction', 'TransactionUpdate');
	
/**
 * Controller actions for which authorization is not required.
 *
 * @var array
 */
	public $allowedActions = array('cancel', 'finish', 'notification');
	
	
	
/**
 * Called before the controller action.
 *
 */
	public function beforeFilter()
	{
		// If the requested action has been 'cancel','finish' or 'notification', disable the login request.
		
		if (in_array(strtolower($this->request->params['action']), array('cancel', 'finish', 'notification')))
		{
			$this->loginDisabled = true;
		}
		
		parent::beforeFilter();
	}
	
	
/**
 * Perform actions related to paypal flow cancel.
 *
 */
	public function cancel()
	{
		// Only necessary if we are using the lightbox payment flow.

		if ($this->Session->read('paypalRedirectUrl')) {
			header("Location: " . $this->Session->read('paypalRedirectUrl'));
			exit;

		} else {
			header("Location: /");
			exit;
						
		} 
	}	
	
	
/**
 * Perform actions related to paypal flow success.
 *
 */
	public function finish()
	{
		// Only necessary if we are using the lightbox payment flow.

		$notification = $this->notification($this->request->payment);

		if ($this->Session->read('paypalRedirectUrl')) {
			header("Location: " . $this->Session->read('paypalRedirectUrl'));
			exit;

		} else {
			header("Location: /");
			exit;
						
		} 
	}
	
	
/**
 * Checks the current status of the payment made via paypal.
 * Method called automatically by Paypal through Instant Payment Notification (IPN).
 *
 * @param string $payment
 */
	public function notification($payment)
	{
		// Reports that was executed successfully.
		$success = false;
		
		
		// Checks if the specified payment is valid and was made via Paypal.
		
		$payment = $this->Transaction->Payment->find('first',
			array(
				'conditions' => array(
					'Transaction.hash' => $payment
				)
			)
		);
		
		if (!empty($payment['Payment']['key']) && !empty($payment['Payment']['method']) && strcasecmp($payment['Payment']['method'], 'paypal') == 0)
		{
			/** PAYPAL COMMUNICATION **/
			
			// cURL headers
			
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
						'payKey' => $payment['Payment']['key'],
						'requestEnvelope.errorLanguage' => 'pt_BR'
					)
				)
			);
			
			$paymentDetails_response = json_decode(curl_exec($paymentDetails_cURL));
			
			curl_close($paymentDetails_cURL);
			
			
			// Updates the Paypal log file with results.
			
			CakeLog::write('paypal', 'PAYMENT - ' . $payment['Payment']['hash']);
			CakeLog::write('paypal', serialize($paymentDetails_response));
			CakeLog::write('paypal', '');	
			
			
			// Checks if there were any errors in the execution.
			
			if (strcasecmp($paymentDetails_response->responseEnvelope->ack, 'success') == 0)
			{
				// Checks if the current status of the payment is different than stored.
				
				if (strcasecmp($paymentDetails_response->status, $payment['Payment']['status']) != 0)
				{
					if (strcasecmp($paymentDetails_response->status, 'completed') == 0)
					{
						// Payment is now completed.
						
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
					}
					else if (strcasecmp($paymentDetails_response->status, 'error') == 0)
					{
						// There was an error in the payment and the same was canceled.
						
						$this->Transaction->Payment->id = $payment['Payment']['id'];
						$this->Transaction->Payment->saveField('status', 'error');
						$this->Transaction->Payment->saveField('is_canceled', 1);
						
						$this->Transaction->id = $payment['Transaction']['id'];
						$this->Transaction->saveField('status', 'payment rejected');
						
						$this->TransactionUpdate->create();
						$this->TransactionUpdate->save(
							array(
								'TransactionUpdate' => array(
									'transaction_id' => $payment['Transaction']['id'],
									'status' => 'payment error'
								)
							)
						);
					}
					else if (strcasecmp($paymentDetails_response->status, 'reversalerror') == 0)
					{
						// There was an error in the payment and the same was canceled.
						
						$this->Transaction->Payment->id = $payment['Payment']['id'];
						$this->Transaction->Payment->saveField('status', 'reversalerror');
						$this->Transaction->Payment->saveField('is_canceled', 1);
						
						$this->TransactionUpdate->create();
						$this->TransactionUpdate->save(
							array(
								'TransactionUpdate' => array(
									'transaction_id' => $payment['Transaction']['id'],
									'status' => 'payment reversal error'
								)
							)
						);
					}
					else if (strcasecmp($paymentDetails_response->status, 'processing') == 0)
					{
						// Payment is being processed.
						
						$this->Transaction->Payment->id = $payment['Payment']['id'];
						$this->Transaction->Payment->saveField('status', 'processing');
						
						$this->Transaction->id = $payment['Transaction']['id'];
						$this->Transaction->saveField('status', 'processing payment');
						
						$this->TransactionUpdate->create();
						$this->TransactionUpdate->save(
							array(
								'TransactionUpdate' => array(
									'transaction_id' => $payment['Transaction']['id'],
									'status' => 'payment processing'
								)
							)
						);
					}
					else if (strcasecmp($paymentDetails_response->status, 'pending') == 0)
					{
						// Payment is pending.
					
						$this->Transaction->Payment->id = $payment['Payment']['id'];
						$this->Transaction->Payment->saveField('status', 'pending');
						
						$this->Transaction->id = $payment['Transaction']['id'];
						$this->Transaction->saveField('status', 'pending payment');
						
						$this->TransactionUpdate->create();
						$this->TransactionUpdate->save(
							array(
								'TransactionUpdate' => array(
									'transaction_id' => $payment['Transaction']['id'],
									'status' => 'payment pending'
								)
							)
						);
					}
				}
				
				$success = true;
			}
			
			/*******************************/
		}
		
			
		$this->set(compact('success'));
		$this->set('_serialize', array('success'));
		
		return $success;
	}
	
	
/**
 * Return a valid Paypal payment key.
 *
 * @param string $payment
 */
	public function admin_add()
	{
		// Stores payment key to be returned.
		
		$payment_key = '';
		
		
		// Checks if the transaction has been specified and is valid.
		
		if (!empty($this->request->query['transaction']))
		{
			$transaction = $this->request->query['transaction'];
			
			$transaction = $this->Transaction->find('first',
				array(
					'conditions' => array(
						'Transaction.hash' => $transaction,
						'Transaction.buyer_id' => $this->loggedUser['User']['id']
					)
				)
			);
		}
		
		if (!empty($transaction))
		{
			// Checks if there is any valid key payment.
			
			$payment = $this->Transaction->Payment->find('first',
				array(
					'conditions' => array(
						'Payment.model' => 'Transaction',
						'Payment.foreign_key' => $transaction['Transaction']['id'],
						'Payment.method' => 'paypal',
						'Payment.is_canceled' => false,
						'Payment.is_deleted' => false
					)
				)
			);
			
			if (!empty($payment['Payment']['key']))
			{
				/** PAYPAL COMMUNICATION **/
				
				// cURL headers
				
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
							'payKey' => $payment['Payment']['key'],
							'requestEnvelope.errorLanguage' => 'pt_BR'
						)
					)
				);
				
				$paymentDetails_response = json_decode(curl_exec($paymentDetails_cURL));
				
				curl_close($paymentDetails_cURL);
				
				
				// Checks if there were any errors in the execution.
				
				if (strcasecmp($paymentDetails_response->responseEnvelope->ack, 'success') == 0)
				{
					// Returns the key payment if it's still valid.
				
					if (strcasecmp($paymentDetails_response->status, 'created') == 0)
					{
						$payment_key = $payment['Payment']['key'];
					}
					else if (strcasecmp($paymentDetails_response->status, 'expired') == 0)
					{
						$payment_key = $this->__generatePaymentKey($transaction['Transaction']['hash']);
					}
				}
				else
				{
					$payment_key = $this->__generatePaymentKey($transaction['Transaction']['hash']);
				}
				
				/*******************************/
			}
			else
			{
				$payment_key = $this->__generatePaymentKey($transaction['Transaction']['hash']);
			}
		}
		
		
		$this->set(compact('payment_key'));
		$this->set('_serialize', array('payment_key'));
		
		return $payment_key;
	}
	
	
	
	
	
/**
 * Generate Paypal payment key.
 *
 * @param string $payment
 */
	protected function __generatePaymentKey($transaction)
	{
		// Stores payment key to be returned.
		
		$payment_key = '';
		
		
		// Checks if the specified transaction is valid.
		
		$transaction = $this->Transaction->find('first',
			array(
				'contain' => array(
					'StoreProduct' => array(
						'Product'
					),
					'Store' => array(
						'PaypalAccount'
					)
				),
				'conditions' => array(
					'Transaction.hash' => $transaction
				)
			)
		);
		
		if (!empty($transaction))
		{
			// Disables all payments that may be open.
			$this->Transaction->Payment->updateAll(
				array(
					'Payment.is_canceled' => 1
				),
				array(
					'Payment.model' => 'Transaction',
					'Payment.foreign_key' => $transaction['Transaction']['id']
				)
			);
			
			
			// Creates a new payment record.
			$data = array(
				'Payment' => array(
					'model' => 'Transaction',
					'foreign_key' => $transaction['Transaction']['id'],
					'method' => 'paypal',
					'status' => 'created'
				)
			);
			
			
			$this->Transaction->Payment->create();
					
			if ($this->Transaction->Payment->save($data))
			{
				$payment = $this->Transaction->Payment->findById($this->Transaction->Payment->getLastInsertId());
				
				
				/** PAYPAL COMMUNICATION **/
				
				// cURL headers
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
							'actionType' => 'PAY',
							'currencyCode' => 'BRL',
							'feesPayer' => 'SECONDARYONLY',
							'cancelUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'cancel', 'admin' => false, 'payment' => $payment['Payment']['hash']), true),
							'returnUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'finish', 'admin' => false, 'payment' => $payment['Payment']['hash']), true),
							'ipnNotificationUrl' => Router::url(array('controller' => 'payments_paypal', 'action' => 'notification', 'admin' => false, 'payment' => $payment['Payment']['hash'], 'ext' => 'json'), true),
							'trackingId' => $payment['Payment']['hash'],
							'requestEnvelope.errorLanguage' => 'pt_BR',
							'receiverList.receiver(0).email' => $transaction['Store']['PaypalAccount']['email'],
							'receiverList.receiver(0).amount' => $transaction['Transaction']['total_price'],
							'receiverList.receiver(0).primary' => true,
							'receiverList.receiver(1).email' => Configure::read('Paypal.payment.BazzApp.email'),
							'receiverList.receiver(1).amount' => number_format(($transaction['Transaction']['total_price'] * (Configure::read('Paypal.payment.tax') / 100)) + (($transaction['Transaction']['price'] * $transaction['Transaction']['quantity']) * (Configure::read('Paypal.payment.BazzApp.tax') / 100)), 2, '.', ''),
							'receiverList.receiver(1).primary' => false
						)
					)
				);
				
				$createPayKey_response = json_decode(curl_exec($createPayKey_cURL));
				
				curl_close($createPayKey_cURL);
				
				
				// Updates the Paypal log file with results.
				
				CakeLog::write('paypal', 'PAYMENT - ' . $payment['Payment']['hash']);
				CakeLog::write('paypal', serialize($createPayKey_response));
				CakeLog::write('paypal', '');
				
				
				// Checks if there were any errors in the execution.
				
				if (strcasecmp($createPayKey_response->responseEnvelope->ack, 'success') == 0)
				{
					// Updates the payment with generated key.
				
					$payment_key = $createPayKey_response->payKey;
					
					$this->Transaction->Payment->id = $payment['Payment']['id'];
					$this->Transaction->Payment->saveField('key', $payment_key);
					
					$this->TransactionUpdate->create();
					$this->TransactionUpdate->save(
						array(
							'TransactionUpdate' => array(
								'transaction_id' => $transaction['Transaction']['id'],
								'status' => 'payment created'
							)
						)
					);
					
					
					// Establishes communication with Paypal server and adds information to the payment key.
				
					$setPaymentOptions_cURL = curl_init();
					
					curl_setopt($setPaymentOptions_cURL, CURLOPT_URL, Configure::read('Paypal.API.endpoint') . '/AdaptivePayments/SetPaymentOptions');
					curl_setopt($setPaymentOptions_cURL, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($setPaymentOptions_cURL, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($setPaymentOptions_cURL, CURLOPT_POST, 1);
					curl_setopt($setPaymentOptions_cURL, CURLOPT_HTTPHEADER, $cURL_header);
					curl_setopt($setPaymentOptions_cURL, CURLOPT_POSTFIELDS,
						http_build_query(
							array(
								'payKey' => $payment_key,
								'requestEnvelope.errorLanguage' => 'pt_BR',
								'receiverOptions(0).receiver.email' => $transaction['Store']['PaypalAccount']['email'],
								'receiverOptions(0).invoiceData.totalShipping' => $transaction['Transaction']['delivery'],
								'receiverOptions(0).invoiceData.item(0).name' => $transaction['StoreProduct']['Product']['title'],
								'receiverOptions(0).invoiceData.item(0).identifier' => $transaction['StoreProduct']['Product']['hash'],
								'receiverOptions(0).invoiceData.item(0).price' => $transaction['Transaction']['price'] * $transaction['Transaction']['quantity'],
								'receiverOptions(0).invoiceData.item(0).itemPrice' => $transaction['Transaction']['price'],
								'receiverOptions(0).invoiceData.item(0).itemCount' => $transaction['Transaction']['quantity']
							)
						)
					);
					
					$setPaymentOptions_response = json_decode(curl_exec($setPaymentOptions_cURL));
					
					curl_close($setPaymentOptions_cURL);
				}
				
				/*******************************/
			}
		}
		
		
		return $payment_key;
	}
}
