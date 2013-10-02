<?php
App::uses('Payment', 'Model');

class TransactionPayment extends Payment
{
	public $name = 'TransactionPayment';
	public $modelAttribute = 'Transaction';
	
	public $belongsTo = array(
		'Transaction' => array(
			'className' => 'Transaction',
			'foreignKey' => 'foreign_key'
		)
	);
}
