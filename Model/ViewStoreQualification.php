<?php
App::uses('AppModel', 'Model');

class ViewStoreQualification extends AppModel
{
	public $name = 'ViewStoreQualification';
	public $useTable = 'view_stores_qualifications';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'qualified_store_id'
		),
		
		'Transaction' => array(
			'className' => 'Transaction',
			'foreignKey' => 'transaction_id'
		)
	);
}
