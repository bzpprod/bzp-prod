<?php
App::uses('AppModel', 'Model');

class ViewBuyerQualification extends AppModel
{
	public $name = 'ViewBuyerQualification';
	public $useTable = 'view_buyers_qualifications';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		
		'Buyer' => array(
			'className' => 'User',
			'foreignKey' => 'qualified_buyer_id'
		),
		
		'Transaction' => array(
			'className' => 'Transaction',
			'foreignKey' => 'transaction_id'
		)
	);
}
