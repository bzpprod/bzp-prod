<?php
App::uses('AppModel', 'Model');

class Comment extends AppModel
{
	public $name = 'Comment';
	public $useTable = 'comments';
	public $actsAs = array('Containable');
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);
}
