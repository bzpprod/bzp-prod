<?php
App::uses('AppModel', 'Model');

class Follow extends AppModel
{
	public $name = 'Follow';
	public $useTable = 'follows';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
}
