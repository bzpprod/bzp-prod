<?php
App::uses('AppModel', 'Model');

class StoreFacebookPage extends AppModel
{
	public $name = 'StoreFacebookPage';
	public $useTable = 'stores_facebook_page';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'Store' => array(
			'className' => 'Store',
			'foreignKey' => 'store_id',
			'dependent' => false
		)
	);
}
