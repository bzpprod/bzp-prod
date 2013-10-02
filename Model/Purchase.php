<?php
App::uses('Transaction', 'Model');

class Purchase extends Transaction
{
	public $name = 'Purchase';
	
	public $virtualFields = array(
		'total_price' => '(Purchase.quantity * Purchase.price) + Purchase.delivery'
	);
}
