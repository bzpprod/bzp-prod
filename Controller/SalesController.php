<?php
App::uses('AppController', 'Controller');


class SalesController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Sales';

/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Store', 'StoreAdministrator', 'Transaction', 'TransactionUpdate', 'ViewBuyerQualification');

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
 * Lists the store sales.
 *
 * @param string $store
 */	
	public function admin_index($store = null)
	{
		// Apply 'admin' layout.
		
		$this->layout = 'admin';
		
		
		// Gets the Facebook manage_pages permisson status.
		
		$fb_manage_pages = $this->__facebookFanpagePermission();
		
		
		// Checks if the specified store is valid.
		
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
			
			$this->redirect(array('controller' => 'sales', 'action' => 'index', 'admin' => true, 'store' => $store['Store']['slug']), null, true);
		}
		
			
		// Finds the transactions related to specified store as seller.
		
		$transactions = $this->paginate('Transaction',
			array(
				'Store.id' => $store['Store']['id']
			)
		);
		
		
		$this->set(compact('store', 'transactions'));
		$this->set('title_for_layout', __d('controller', 'Sales.admin_index.title'));
	}
	
	
/**
 * Displays the specified transaction.
 *
 * @param string $transaction
 * @param string $store
 */	
	public function admin_view($transaction, $store)
	{
		// Checks if the specified store and transaction are valid.
		
		list($transaction, $store) = $this->__administeredStoreTransaction($transaction, $store);
		
		
		$this->set(compact('transaction', 'store'));
	}
	
	
/**
 * Displays the purchase qualification form and stores submitted data.
 *
 * @param string $transaction
 * @param string $store
 */	
	public function admin_qualify($transaction, $store)
	{
		// Indicates if the transaction has already been qualified.
		
		$qualified = false;
		
		
		// Checks if the specified store and transaction are valid.
		
		list($transaction, $store) = $this->__administeredStoreTransaction($transaction, $store);
		
		
		// Checks if the transaction has already been qualified by the seller.
		
		if (!empty($transaction['SaleQualification']['id']))
		{
			$qualified = true;
		}
		else if (!empty($transaction))
		{
			// Checks if the qualification data was submitted.
		
			if (($this->request->is('post') || $this->request->is('put')) && !empty($this->request->data['SaleQualification']))
			{
				$data = Sanitize::clean($this->request->data, array('encode' => false, 'escape' => false));
				$data['SaleQualification']['user_id'] = $this->loggedUser['User']['id'];
				$data['SaleQualification']['transaction_id'] = $transaction['Transaction']['id'];
				$data['SaleQualification']['method'] = 'sale';
				
				
				$this->Transaction->SaleQualification->create();
				
				if ($this->Transaction->SaleQualification->save($data))
				{
					$qualified = true;
					
					
					// Updates the stored qualifications number.
				
					$negative_qualifications = $this->ViewBuyerQualification->find('count',
						array(
							'conditions' => array(
								'ViewBuyerQualification.qualified_buyer_id' => $transaction['Buyer']['id'],
								'ViewBuyerQualification.qualification' => 'negative'
							)
						)
					);
					
					$positive_qualifications = $this->ViewBuyerQualification->find('count',
						array(
							'conditions' => array(
								'ViewBuyerQualification.qualified_buyer_id' => $transaction['Buyer']['id'],
								'ViewBuyerQualification.qualification' => 'positive'
							)
						)
					);
					
					$this->Transaction->Buyer->id = $transaction['Buyer']['id'];
					$this->Transaction->Buyer->saveField('qualification_negative', $negative_qualifications);
					$this->Transaction->Buyer->saveField('qualification_positive', $positive_qualifications);
					
					
					// Updates the transaction history.
					
					$this->TransactionUpdate->create();
					$this->TransactionUpdate->save(
						array(
							'TransactionUpdate' => array(
								'transaction_id' => $transaction['Transaction']['id'],
								'status' => 'sale qualified'
							)
						)
					);
				}
			}
		}
		
		
		$this->set(compact('store', 'transaction', 'qualified'));
	}
}
