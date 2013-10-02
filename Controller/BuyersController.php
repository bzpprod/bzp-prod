<?php
App::uses('AppController', 'Controller');


class BuyersController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Buyers';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Transaction');
	
	
	
/**
 * Displays the buyer information.
 *
 * @params string $transaction
 * @params string $store
 */	
	public function admin_view($transaction, $store)
	{
		// Gets the buyer data.
		
		return $this->api_view($transaction, $store);
	}
	
	
/**
 * Gets the buyer information.
 *
 * @params string $transaction
 */	
	public function api_view($transaction, $store)
	{
		// Checks if the specified transaction is valid.
		
		list($transaction, $store) = $this->__administeredStoreTransaction($transaction, $store);
		
		if (!empty($transaction['Transaction']['buyer_id']))
		{
			$buyer = $this->Transaction->Buyer->find('first',
				array(
					'contain' => array(
						'FacebookUser'
					),
					'conditions' => array(
						'Buyer.id' => $transaction['Transaction']['buyer_id']
					)
				)
			);
		}
		else
		{
			$buyer = array();
		}
		
		
		$this->set(compact('buyer', 'transaction', 'store'));
		
		return array($buyer, $transaction, $store);
	}
}
