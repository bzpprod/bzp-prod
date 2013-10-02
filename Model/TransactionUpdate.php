<?php
App::uses('AppModel', 'Model');

class TransactionUpdate extends AppModel
{
	public $name = 'TransactionUpdate';
	public $useTable = 'transactions_updates';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'Transaction' => array(
			'className' => 'Transaction',
			'foreignKey' => 'transaction_id'
		)
	);
}
