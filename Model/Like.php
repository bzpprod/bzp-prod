<?php
App::uses('AppModel', 'Model');

class Like extends AppModel
{
	public $name = 'Like';
	public $useTable = 'likes';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
}
