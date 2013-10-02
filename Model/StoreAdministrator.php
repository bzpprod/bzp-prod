<?php
App::uses('AppModel', 'Model');

class StoreAdministrator extends AppModel
{
	public $name = 'StoreAdministrator';
	public $useTable = 'stores_administrators';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'store_id'
		),
		
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
}
