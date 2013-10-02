<?php
App::uses('AppController', 'Controller');


class SellersController extends AppController
{
/**
 * Controller name.
 *
 * @var string
 */
	public $name = 'Sellers';
	
/**
 * Models used by the controller.
 *
 * @var array
 */
	public $uses = array('Store', 'Transaction');
	
	
	
/**
 * Displays the seller information.
 *
 * @params string $transaction
 */	
	public function admin_view($transaction)
	{
		// Gets the seller data.
		
		return $this->api_view($transaction);
	}
	
	
/**
 * Gets the seller information.
 *
 * @params mixed $transaction
 */	
	public function api_view($transaction)
	{
		// Checks if the specified transaction exists and belongs to logged user as buyer.
		
		$transaction = $this->__transaction($transaction);
		
		if (!empty($transaction['Store']['id']) && $transaction['Transaction']['buyer_id'] == $this->loggedUser['User']['id'])
		{
			$seller = $this->__store($transaction['Store']['id']);
		}
		else
		{
			$seller = array();
			$transaction = array();
		}
		
		
		$this->set(compact('transaction', 'seller'));
		$this->set('_serialize', array('transaction', 'seller'));
		
		return array('transaction', 'seller');
	}
}
