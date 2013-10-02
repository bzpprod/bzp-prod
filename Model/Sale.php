<?php
App::uses('Transaction', 'Model');

class Sale extends Transaction
{
	public $name = 'Sale';
	
	public $virtualFields = array(
		'total_price' => '(Sale.quantity * Sale.price) + Sale.delivery'
	);
}
